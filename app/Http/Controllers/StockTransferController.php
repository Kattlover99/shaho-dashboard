<?php

namespace App\Http\Controllers;

use App\TransactionSellLine;
use Illuminate\Http\Request;

use App\BusinessLocation;
use App\Transaction;
use App\TransactionSellLinesPurchaseLines;
use App\PurchaseLine;

use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\ModuleUtil;

use Datatables;
use DB;
use Log;

class StockTransferController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('purchase.view') && !auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $edit_days = request()->session()->get('business.transaction_edit_days');

            $stock_transfers = Transaction::join(
                'business_locations AS l1',
                'transactions.location_id',
                '=',
                'l1.id'
            )
                    ->join('transactions as t2', 't2.transfer_parent_id', '=', 'transactions.id')
                    ->join(
                        'business_locations AS l2',
                        't2.location_id',
                        '=',
                        'l2.id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell_transfer')
                    ->select(
                        'transactions.id',
                        'transactions.transaction_date',
                        'transactions.ref_no',
                        'l1.name as location_from',
                        'l2.name as location_to',
                        'transactions.final_total',
                        'transactions.shipping_charges',
                        'transactions.additional_notes',
                        'transactions.id as DT_RowId'
                    );
            
            return Datatables::of($stock_transfers)
                ->addColumn('action', function ($row) use ($edit_days) {
                    $html = '<button type="button" title="' . __("stock_adjustment.view_details") . '" class="btn btn-primary btn-xs view_stock_transfer"><i class="fa fa-eye-slash" aria-hidden="true"></i></button>';

//                    $html .= ' <a href="#" class="print-invoice btn btn-info btn-xs" data-href="' . action('StockTransferController@printInvoice', [$row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i> '. __("messages.print") .'</a>';

                        $date = \Carbon::parse($row->transaction_date)
                        ->addDays($edit_days);
                        $today = today();

                    if ($date->gte($today)) {
                        $html .= '&nbsp;
                        <button type="button" data-href="' . action("StockTransferController@destroy", [$row->id]) . '" class="btn btn-danger btn-xs delete_stock_transfer"><i class="fa fa-trash" aria-hidden="true"></i> ' . __("messages.delete") . '</button>';
                    }

                    return $html;
                })
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency" data-currency_symbol="true">{{$final_total}}</span>'
                )
                ->editColumn(
                    'shipping_charges',
                    '<span class="display_currency" data-currency_symbol="true">{{$shipping_charges}}</span>'
                )
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->rawColumns(['final_total', 'action', 'shipping_charges'])
                ->make(true);
        }

        return view('stock_transfer.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action('StockTransferController@index'));
        }

        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('stock_transfer.create')
                ->with(compact('business_locations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action('StockTransferController@index'));
            }

            DB::beginTransaction();
            
            $input_data = $request->only([ 'location_id', 'ref_no', 'transaction_date', 'additional_notes', 'shipping_charges', 'final_total']);
            
            $user_id = $request->session()->get('user.id');

            $input_data['final_total'] = $this->productUtil->num_uf($input_data['final_total']);
            $input_data['total_before_tax'] = $input_data['final_total'];

            $input_data['type'] = 'sell_transfer';
            $input_data['business_id'] = $business_id;
            $input_data['created_by'] = $user_id;
            $input_data['transaction_date'] = $this->productUtil->uf_date($input_data['transaction_date']);
            $input_data['shipping_charges'] = $this->productUtil->num_uf($input_data['shipping_charges']);
            $input_data['status'] = 'final';
            $input_data['payment_status'] = 'paid';

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('stock_transfer');
            //Generate reference number
            if (empty($input_data['ref_no'])) {
                $input_data['ref_no'] = $this->productUtil->generateReferenceNumber('stock_transfer', $ref_count);
            }

            $products = $request->input('products');
            $sell_lines = [];
            $purchase_lines = [];

            if (!empty($products)) {
                foreach ($products as $product) {
//                    $query = PurchaseLine::where('product_id', '=', $product['product_id'])
//                        ->where('variation_id', '=', $product['variation_id'])->first();

                    $query = PurchaseLine::findOrFail($product['purchase_lines_id']);

                    $sell_line_arr = [
                                'product_id' => $product['product_id'],
                                'variation_id' => $product['variation_id'],
                                'quantity' => $this->productUtil->num_uf($product['quantity']),
//                                'item_tax' => 0,
//                                'tax_id' => null,
                                'exp_date' => $query['exp_date']
                    ];

                    $purchase_line_arr = $sell_line_arr;
                    $sell_line_arr['purchase_lines_id'] = $product['purchase_lines_id'];
                    $sell_line_arr['unit_price'] = $this->productUtil->num_uf($product['unit_price']);
                    $sell_line_arr['unit_price_inc_tax'] = $sell_line_arr['unit_price'];

                    $purchase_line_arr['purchase_price'] = $sell_line_arr['unit_price'];
                    $purchase_line_arr['purchase_price_inc_tax'] = $sell_line_arr['unit_price'];

                    $sell_lines[] = $sell_line_arr;
                    $purchase_lines[] = $purchase_line_arr;
                }
            }

            //Create Sell Transfer transaction
            $sell_transfer = Transaction::create($input_data);

            //Create Purchase Transfer at transfer location
            $input_data['type'] = 'purchase_transfer';
            $input_data['status'] = 'received';
            $input_data['location_id'] = $request->input('transfer_location_id');
            $input_data['transfer_parent_id'] = $sell_transfer->id;

            $purchase_transfer = Transaction::create($input_data);

            //Sell Product from first location
            if (!empty($sell_lines)) {
                $this->transactionUtil->createOrUpdateSellLines($sell_transfer, $sell_lines, $input_data['location_id']);
            }

            //Purchase product in second location
            if (!empty($purchase_lines)) {
                $purchase_transfer->purchase_lines()->createMany($purchase_lines);
            }

            //Decrease product stock from sell location
            //And increase product stock at purchase location
            foreach ($products as $product) {
                if ($product['enable_stock']) {
                    $this->productUtil->decreaseProductQuantity(
                        $product['product_id'],
                        $product['variation_id'],
                        $sell_transfer->location_id,
                        $this->productUtil->num_uf($product['quantity'])
                    );

                    $this->productUtil->updateProductQuantity(
                        $purchase_transfer->location_id,
                        $product['product_id'],
                        $product['variation_id'],
                        $product['quantity']
                    );
                }
            }

            //Map sell lines with purchase lines
            $business = ['id' => $business_id,
                        'accounting_method' => $request->session()->get('business.accounting_method'),
                        'location_id' => $sell_transfer->location_id
                    ];
            $this->transactionUtil->mapPurchaseSell($business, $sell_transfer->sell_lines, 'purchase');

            $output = ['success' => 1,
                            'msg' => __('lang_v1.stock_transfer_added_successfully')
                        ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }

        return redirect('stock-transfers')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('purchase.view')) {
            abort(403, 'Unauthorized action.');
        }

        $stock_adjustment_details = Transaction::
                    join(
                        'transaction_sell_lines as sl',
                        'sl.transaction_id',
                        '=',
                        'transactions.id'
                    )
                    ->join('products as p', 'sl.product_id', '=', 'p.id')
                    ->join('variations as v', 'sl.variation_id', '=', 'v.id')
                    ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
	                ->leftjoin('transaction_sell_lines_purchase_lines as tslpl', 'tslpl.sell_line_id', '=', 'sl.id')
	                ->leftjoin('purchase_lines as pl', 'pl.id', '=', 'tslpl.purchase_line_id')
                    ->where('transactions.id', $id)
                    ->where('transactions.type', 'sell_transfer')
                    ->select(
                        'p.name as product',
                        'p.type as type',
                        'pv.name as product_variation',
                        'v.name as variation',
                        'v.sub_sku',
                        'sl.quantity',
                        'sl.unit_price',
                        'pl.exp_date'
                    )
                    ->groupBy('sl.id')
                    ->get();

        return view('stock_adjustment.partials.details')
                ->with(compact('stock_adjustment_details'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('purchase.delete')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            if (request()->ajax()) {
                $edit_days = request()->session()->get('business.transaction_edit_days');
                if (!$this->transactionUtil->canBeEdited($id, $edit_days)) {
                    return ['success' => 0,
                        'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])];
                }

                //Get sell transfer transaction
                $sell_transfer = Transaction::where('id', $id)
                                    ->where('type', 'sell_transfer')
                                    ->with(['sell_lines'])
                                    ->first();

                //Get purchase transfer transaction
                $purchase_transfer = Transaction::where('transfer_parent_id', $sell_transfer->id)
                                    ->where('type', 'purchase_transfer')
                                    ->with(['purchase_lines'])
                                    ->first();

                //Check if any transfer stock is deleted and delete purchase lines
                $purchase_lines = $purchase_transfer->purchase_lines;

                foreach ($purchase_lines as $purchase_line) {
                    if ($purchase_line->quantity_sold > 0) {
                        return [ 'success' => 0,
                                        'msg' => __('lang_v1.stock_transfer_cannot_be_deleted')
                            ];
                    }
                }

                DB::beginTransaction();
                //Get purchase lines from transaction_sell_lines_purchase_lines and decrease quantity_sold
                $sell_lines = $sell_transfer->sell_lines;

                $deleted_sell_purchase_ids = [];
                $products = []; //variation_id as array

                foreach ($sell_lines as $sell_line) {
	                $purchase_sell_line = TransactionSellLinesPurchaseLines::where('sell_line_id', '=', $sell_line->id)->first();

                    if (!empty($purchase_sell_line)) {
                        //Decrease quntity sold from purchase line
                        PurchaseLine::where('id', $purchase_sell_line->purchase_line_id)
                                ->decrement('quantity_sold', $sell_line->quantity);

                        $deleted_sell_purchase_ids[] = $purchase_sell_line->id;

                        //variation details
                        if (isset($products[$sell_line->variation_id])) {
                            $products[$sell_line->variation_id]['quantity'] += $sell_line->quantity;
                            $products[$sell_line->variation_id]['product_id'] = $sell_line->product_id;
                        } else {
                            $products[$sell_line->variation_id]['quantity'] = $sell_line->quantity;
                            $products[$sell_line->variation_id]['product_id'] = $sell_line->product_id;
                        }
                    }
                }

                //Update quantity available in both location
                if (!empty($products)) {
                    foreach ($products as $key => $value) {
                        //Decrease from location 2
                        $this->productUtil->decreaseProductQuantity(
                            $products[$key]['product_id'],
                            $key,
                            $purchase_transfer->location_id,
                            $products[$key]['quantity']
                        );

                        //Increase in location 1
                        $this->productUtil->updateProductQuantity(
                            $sell_transfer->location_id,
                            $products[$key]['product_id'],
                            $key,
                            $products[$key]['quantity']
                        );
                    }
                }

                //Delete sale line purchase line
                if (!empty($deleted_sell_purchase_ids)) {
                    TransactionSellLinesPurchaseLines::whereIn('id', $deleted_sell_purchase_ids)
                        ->delete();
                }

                //Delete both transactions
                $sell_transfer->delete();
                $purchase_transfer->delete();

                $output = ['success' => 1,
                        'msg' => __('lang_v1.stock_transfer_delete_success')
                    ];
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }
        return $output;
    }

    /**
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            
            $sell_transfer = Transaction::where('business_id', $business_id)
                                ->where('id', $id)
                                ->where('type', 'sell_transfer')
                                ->with(
                                    'contact',
                                    'sell_lines',
                                    'sell_lines.product',
                                    'sell_lines.variations',
                                    'sell_lines.variations.product_variation',
                                    'location'
                                )
                                ->first();

            $purchase_transfer = Transaction::where('business_id', $business_id)
                        ->where('transfer_parent_id', $sell_transfer->id)
                        ->where('type', 'purchase_transfer')
                        ->first();

            $location_details = ['sell' => $sell_transfer->location, 'purchase' => $purchase_transfer->location];


            $output = ['success' => 1, 'receipt' => []];
            $output['receipt']['html_content'] = view('stock_transfer.print', compact('sell_transfer', 'location_details'))->render();
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }

        return $output;
    }

    public function getInventoryProducts() {
	    if (request()->ajax()) {
		    $location_id = request()->input('location_id');
		    $dontShowExpiredItems = false;
		    $business_id = request()->session()->get('user.business_id');
		    $products = PurchaseLine::LeftJoin('transactions as t', 't.id', '=', 'purchase_lines.transaction_id')
			    ->LeftJoin('products as p', 'p.id', '=', 'purchase_lines.product_id')
			    ->LeftJoin('variations as v', 'v.id', '=', 'purchase_lines.variation_id')
			    ->where('t.status', '=', 'received')
			    ->select(
				    'purchase_lines.id',
				    'p.name',
				    'p.product_code',
				    'v.default_sell_price as dsp',
				    //'purchase_lines.purchase_price_inc_tax as dsp',
				    DB::raw('purchase_lines.quantity - purchase_lines.quantity_sold - purchase_lines.quantity_adjusted as quantity'),
				    DB::raw('purchase_lines.exp_date as exp_date',
				    'purchase_lines.product_id as product_id',
				    'purchase_lines.variation_id as variation_id'
				    )
			    )
			    ->when($dontShowExpiredItems, function ($query, $dontShowExpiredItems) {
				    if ($dontShowExpiredItems == 'true') {
					    $sql = '(exp_date >= "' . date("Y-m-d") . '" or exp_date is NULL)';
					    return $query->whereRaw($sql);
				    }
			    })
			    ->where('t.location_id', '=', $location_id)
			    ->where('t.business_id', '=', $business_id)
			    ->orderBy('name')
			    ->get();
		    
		    $html = '<option value="">None</option>';
		    
		    if (!empty($products))
		    {
			    foreach($products as $product)
			    {
			        if ($product->quantity > 0.0)
    			    {
    			        $product_code = $product->product_code;
    			        
    			        if($product_code != '')
                            $product_code = ' - ' . $product_code;
                        
    				    $html .= '<option value="' . $product->id .'">' .$product->name . $product_code . ' ( $'. $product->dsp .' | '.$product->quantity. ($product->quantity > 1 ? ' units' : 'unit'). ' | EXP Date: '. ($product->exp_date != null ? $product->exp_date : ' '). ')'.'</option>';
    			    }
			    }
		    }
		    echo $html;
		    exit;
	    }
    }

    public function getProductForTransfer () {
	    $output = [];

	    try {
		    $row_count = request()->get('product_row');
		    $purchase_id = request()->get('purchase_id');
		    $row_count = $row_count + 1;

		    $business_id = request()->session()->get('user.business_id');

		    $product = PurchaseLine::LeftJoin('transactions as t', 't.id', '=', 'purchase_lines.transaction_id')
			    ->LeftJoin('products as p', 'p.id', '=', 'purchase_lines.product_id')
			    ->LeftJoin('variations as v', 'v.id', '=', 'purchase_lines.variation_id')
			    ->where('t.status', '=', 'received')
			    ->select(
				    'purchase_lines.id',
				    'p.name',
				    'v.default_sell_price as dsp',
				    DB::raw('purchase_lines.quantity - purchase_lines.quantity_sold - purchase_lines.quantity_adjusted as quantity'),
				    DB::raw('purchase_lines.exp_date as exp_date'),
				    'purchase_lines.product_id as product_id',
				    'purchase_lines.variation_id as variation_id',
				    'purchase_lines.purchase_price',
				    'p.enable_stock'
			    )
			    ->findOrFail($purchase_id);

		    $output['success'] = true;

		    $output['html_content'] =  view('stock_transfer.partials.product_table_row')
			    ->with(compact('product', 'row_count'))
			    ->render();

	    } catch (\Exception $e) {
		    \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

		    $output['success'] = false;
		    $output['msg'] = __('lang_v1.item_out_of_stock');
	    }

	    return $output;
    }
}
