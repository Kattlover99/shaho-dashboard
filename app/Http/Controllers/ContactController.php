<?php

namespace App\Http\Controllers;

use App\Contact;
use App\CustomerGroup;
use App\ContactCategory;
use App\Transaction;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

use DB;

use App\Utils\Util;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Log;
class ContactController extends Controller
{
    protected $commonUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, 
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $type = request()->get('type');

        if (request()->ajax()) {
            if ($type == 'supplier') {
                return $this->indexSupplier();
            } elseif ($type == 'customer') {
                return $this->indexCustomer();
            } else {
                die("Not Found");
            }
        }

        return view('contact.index')
            ->with(compact('type'));
    }

    /**
     * Returns the database object for supplier
     *
     * @return \Illuminate\Http\Response
     */
    private function indexSupplier()
    {

        if (!auth()->user()->can('supplier.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $contact = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
                    ->where('contacts.business_id', $business_id)
					->leftJoin('contact_categories', 'contacts.category', '=', 'contact_categories.id')
                    ->onlySuppliers()
                    ->select(['contacts.id', 'contacts.name', 'contacts.type', 'contacts.city', 'contact_categories.name as contact_category_name',
                        DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                        DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_paid"),
                        DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                        DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid")
                    ])
                    ->groupBy('contacts.id');

        return Datatables::of($contact)
           ->addColumn('#', function ($row) {
                    return  $row->id;

            })
			->addColumn('city', function ($row) {
                    return  $row->city;

            })
			->addColumn('name', function ($row) {
                    return  $row->name;

            })
			->addColumn('category', function ($row) {
                    return  $row->contact_category_name;

            })
			->addColumn('balance', function ($row) {
                    return  '<span class="display_currency" data-currency_symbol=true >{{$row->opening_balance - $row->opening_balance_paid - $row->total_purchase + $row->purchase_paid }}</span>';

            })
            ->addColumn(
                'action',
                '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                @can("supplier.view")
                    <li><a href="{{action(\'ContactController@show\', [$id])}}"><i class="fa fa-external-link" aria-hidden="true"></i> @lang("messages.view")</a></li>
                @endcan
                @can("supplier.update")
                    <li><a href="{{action(\'ContactController@edit\', [$id])}}" class="edit_contact_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a></li>
                @endcan
                @can("supplier.delete")
                    <li><a href="{{action(\'ContactController@destroy\', [$id])}}" class="delete_contact_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a></li>
                @endcan </ul></div>'
            )
            ->removeColumn('opening_balance')
            ->removeColumn('total_purchase')
            ->removeColumn('opening_balance_paid')
            ->removeColumn('type')
            ->removeColumn('total_purchase')
            ->removeColumn('purchase_paid')
            ->rawColumns(['balance', 'action'])
            ->make(true);
    }

    /**
     * Returns the database object for customer
     *
     * @return \Illuminate\Http\Response
     */
    private function indexCustomer()
    {
        if (!auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $flag = request()->get('nonZero');

        $contacts = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->where('contacts.business_id', $business_id)
			->leftJoin('contact_categories', 'contacts.category', '=', 'contact_categories.id')
            ->onlyCustomers()
            ->select(['contacts.id', 'contacts.name', 'contacts.type', 'contacts.city', 'contacts.opening_balance', 'contacts.past_debit', 'contacts.debit_in_iqd','contacts.debit_in_usd', 't.final_total', 't.type as type2',
                DB::raw("SUM(IF(t.type = 'sell', final_total, 0)) 
                     - SUM(IF(t.type = 'sell', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0))
                     + SUM(IF(t.type = 'opening_balance', final_total, 0)) as balance")
            ]);

        if ($flag == 'true') {
            $contacts = $contacts->having('balance', '!=', 0)->groupBy('contacts.id');
        }
        else $contacts = $contacts->groupBy('contacts.id');

        return Datatables::of($contacts)
			->addColumn('#', function ($row) {
                    return  $row->id;

            })
			->addColumn('city', function ($row) {
                    return  $row->city;

            })
			->addColumn('name', function ($row) {
                    return  $row->name;

            })
			/*->addColumn('category', function ($row) {
                    return  $row->contact_category_name;

            })*/
           /*  ->addColumn('past_debit', function ($row) {
                    return  $row->past_debit;

            }) */
			
            ->addColumn('debit_in_iqd_amount', function ($row) {
                return  '<span class="display_currency" data-currency_symbol=true >'.$row->opening_balance.'</span><input type="hidden" class="debit_in_iqd" value="'.$row->opening_balance.'">'; 
            })
            ->addColumn('debit_in_usd_amount', function ($row) { 
                return '$'.number_format($row->debit_in_usd, 2, ".", ",").'<input type="hidden" class="debit_in_usd" value="'.$row->debit_in_usd.'">'; 
            })
			->addColumn(
                'action',
                '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                    data-toggle="dropdown" aria-expanded="false">' .
                __("messages.actions") .
                '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">
            @can("customer.view")
                <li><a href="{{action(\'ContactController@show\', [$id])}}"><i class="fa fa-external-link" aria-hidden="true"></i> @lang("messages.view")</a></li>
            @endcan
            @can("customer.update")
                <li><a href="{{action(\'ContactController@edit\', [$id])}}" class="edit_contact_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a></li>
            @endcan
            @can("customer.delete")
                <li><a href="{{action(\'ContactController@destroy\', [$id])}}" class="delete_contact_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a></li>
            @endcan
            
             
            <!--- <li><span style="padding: 0px 7px 0px;">@lang("messages.add_payments")</span></li>
            <li><a href="{{action(\'ContactController@destroy\', [$id])}}" class="add_payment_iqd"><i class="glyphicon glyphicon-plus"></i> @lang("messages.add_iqd")</a></li>

            <li><a href="{{action(\'ContactController@destroy\', [$id])}}" class="add_payment_usd"><i class="glyphicon glyphicon-plus"></i> @lang("messages.add_usd")</a></li> ---->
            </ul></div>'
            )
//            ->removeColumn('total_invoice')
//            ->removeColumn('opening_balance')
//            ->removeColumn('invoice_received')
            ->removeColumn('type')
	        ->removeColumn('type2')
	        ->removeColumn('final_total')
            ->rawColumns(['debit_in_iqd_amount','debit_in_usd_amount', 'action'])
            ->make(true);
    }

    public function getStartingBalance(Request $request) {

        if (!auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }
        $type = $request->get('type');

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $contacts = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
                ->where('contacts.business_id', $business_id)
                ->where('contacts.type', $type)
                ->select(['contacts.id', 'contacts.name', 'contacts.updated_at',
                    DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid")
                ])->groupBy('contacts.id');

            return Datatables::of($contacts)
                ->addColumn(
                    'starting_balance',
                    '<span class="display_currency" data-currency_symbol=true data-highlight=true>{{ $opening_balance }}</span>'
                )
                ->removeColumn('opening_balance')
                ->removeColumn('opening_balance_paid')
                ->rawColumns(['starting_balance'])
                ->make(true);
        }

        return view('contact.starting_balances')
            ->with(compact('type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
		
		$contactCategory = ContactCategory::where('business_id', $business_id)->pluck('name', 'id');
		
		//dump($contactCategory);exit;
		
        return view('contact.create')
            ->with(compact('types', 'contactCategory'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            }

            //$input = $request->only(['type', 'supplier_business_name', 'name', 'mobile', 'city', 'contact_person', 'opening_balance', 'category']);
            //$input = $request->only(['type', 'supplier_business_name', 'name', 'mobile', 'city', 'contact_person', 'opening_balance', 'category', 'past_debit']);
            $input = $request->only(['type', 'supplier_business_name', 'name', 'mobile', 'city', 'contact_person', 'opening_balance', 'debit_in_iqd', 'debit_in_usd']);
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');

            $count = 0;

            if ($count == 0) {
                //Update reference count
                $ref_count = $this->commonUtil->setAndGetReferenceCount('contacts');

                $contact = Contact::create($input);

                //Add opening balance
                if(!empty($request->input('opening_balance'))){
                    $this->transactionUtil->createOpeningBalanceTransaction($business_id, $contact->id, $request->input('opening_balance'));
                }

                $output = ['success' => true,
                            'data' => $contact,
                            'msg' => __("contact.added_success")
                        ];
            } else {
                throw new \Exception("Error Processing Request", 1);
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' =>__("messages.something_went_wrong")
                        ];
        }

        return $output; 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         
        if (!auth()->user()->can('supplier.view') && !auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $contact = Contact::where('contacts.id', $id)
                            ->join('transactions AS t', 'contacts.id', '=', 't.contact_id')
                            ->select(
                                DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                                DB::raw("SUM(IF(t.type = 'sell', final_total, 0)) as total_invoice"),
                                DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_paid"),
                                DB::raw("SUM(IF(t.type = 'sell', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                                'contacts.*'
                            )->first();
        return view('contact.show')
             ->with(compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		
        if (!auth()->user()->can('supplier.update') && !auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

	        $contact = Contact::where('business_id', $business_id)->find($id);

	        $count = Transaction::where('business_id', '=', $business_id)
		        ->where('contact_id', '=', $id)
		        ->count();

	        if (!$this->moduleUtil->isSubscribed($business_id)) {
		        return $this->moduleUtil->expiredResponse();
	        }

	        $types = [];
	        if (auth()->user()->can('supplier.create')) {
		        $types['supplier'] = __('report.supplier');
	        }
	        if (auth()->user()->can('customer.create')) {
		        $types['customer'] = __('report.customer');
	        }
	        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
		        $types['both'] = __('lang_v1.both_supplier_customer');
	        }

	        $customer_groups = CustomerGroup::forDropdown($business_id);

	        $ob_transaction =  Transaction::where('contact_id', $id)
		        ->where('type', 'opening_balance')
		        ->first();
	        $opening_balance = !empty($ob_transaction->final_total) ? $this->commonUtil->num_f($ob_transaction->final_total) : 0;
			
			$contactCategory = ContactCategory::where('business_id', $business_id)->pluck('name', 'id');
			
	        return view('contact.edit')
		        ->with(compact('contact', 'types', 'customer_groups', 'opening_balance', 'count', 'contactCategory'));
        }
    }

    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        if (!auth()->user()->can('supplier.update') && !auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                //$input = $request->only(['type', 'name', 'mobile', 'city', 'supplier_business_name', 'opening_balance', 'category', 'past_debit']);
                $input = $request->only(['type', 'name', 'mobile', 'city', 'supplier_business_name', 'opening_balance', 'debit_in_iqd', 'debit_in_usd']);

                $input['credit_limit'] = $request->input('credit_limit') != '' ? $this->commonUtil->num_uf($request->input('credit_limit')) : null;
                
                $business_id = $request->session()->get('user.business_id');

                if (!$this->moduleUtil->isSubscribed($business_id)) {
                    return $this->moduleUtil->expiredResponse();
                }

                $count = 0;

                //Check Contact id
                if (!empty($input['contact_id'])) {
                    $count = Contact::where('business_id', $business_id)
                            ->where('contact_id', $input['contact_id'])
                            ->where('id', '!=', $id)
                            ->count();
                }
                
                if ($count == 0) {
                    $contact = Contact::where('business_id', $business_id)->findOrFail($id);
                    foreach ($input as $key => $value) {
                        $contact->$key = $value;
                    }
                    $contact->save();

                    //Get opening balance if exists
                    $ob_transaction =  Transaction::where('contact_id', $id)
                                            ->where('type', 'opening_balance')
                                            ->first();

                    if(!empty($ob_transaction)){
                        $amount = $this->commonUtil->num_uf($request->input('opening_balance'));
                        $ob_transaction->final_total = $amount;
                        $ob_transaction->save();
                        //Update opening balance payment status
                        $this->transactionUtil->updatePaymentStatus($ob_transaction->id, $ob_transaction->final_total);
                    } else {
                        //Add opening balance
                        if(!empty($request->input('opening_balance'))){
                            $this->transactionUtil->createOpeningBalanceTransaction($business_id, $contact->id, $request->input('opening_balance'));
                        }
                    }

                    $output = ['success' => true,
                                'msg' => __("contact.updated_success")
                                ];
                } else {
                    throw new \Exception("Error Processing Request", 1);
                }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('supplier.delete') && !auth()->user()->can('customer.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                //Check if any transaction related to this contact exists
                $count = Transaction::where('business_id', $business_id)
                                    ->where('contact_id', $id)
	                                ->where('type', '!=', 'opening_balance')
                                    ->count();
                if ($count == 0) {
                    $contact = Contact::where('business_id', $business_id)->findOrFail($id);
                    if (!$contact->is_default) {
                    	$transactions = Transaction::where('business_id', $business_id)
		                    ->where('contact_id', $id)
		                    ->where('type', '=', 'opening_balance')
		                    ->get();
                    	foreach ($transactions as $transaction) {
                    		$transaction->delete();
	                    }
                        $contact->delete();
                    }
                    $output = ['success' => true,
                                'msg' => __("contact.deleted_success")
                                ];
                } else {
                    $output = ['success' => false,
                                'msg' => __("lang_v1.you_cannot_delete_this_contact")
                                ];
                }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    /**
     * Retrieves list of customers, if filter is passed then filter it accordingly.
     *
     * @param  string  $q
     * @return JSON
     */
    public function getCustomers()
    {
        if (request()->ajax()) {
            $term = request()->input('q', '');

            $business_id = request()->session()->get('user.business_id');
            $suppliers = Contact::where('business_id', $business_id);

            if (!empty($term)) {
                $suppliers->where(function ($query) use ($term) {
                        $query->where('name', 'like', '%' . $term .'%')
                            ->orWhere('supplier_business_name', 'like', '%' . $term .'%')
                            ->orWhere('mobile', 'like', '%' . $term .'%')
                            ->orWhere('contact_id', 'like', '%' . $term .'%');
                });
            }

            $suppliers = $suppliers->select(
                'contacts.id',
                'contacts.name as text',
                'mobile',
                'landmark',
                'city',
                'state'
            )
                                ->onlyCustomers()
                                ->get();
            return json_encode($suppliers);
        }
    }

    /**
     * Checks if the given contact id already exist for the current business.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkContactId(Request $request)
    {
        $contact_id = $request->input('contact_id');

        $valid = 'true';
        if (!empty($contact_id)) {
            $business_id = $request->session()->get('user.business_id');
            $hidden_id = $request->input('hidden_id');

            $query = Contact::where('business_id', $business_id)
                            ->where('contact_id', $contact_id);
            if (!empty($hidden_id)) {
                $query->where('id', '!=', $hidden_id);
            }
            $count = $query->count();
            if ($count > 0) {
                $valid = 'false';
            }
        }
        echo $valid;
        exit;
    }

    public function checkContactName(Request $request) {
	    $name = $request->input('name');

	    $valid = 'true';
	    if (!empty($name)) {
		    $business_id = $request->session()->get('user.business_id');
		    $hidden_id = $request->input('hidden_id');

		    $query = Contact::where('business_id', $business_id)
			    ->where('name', $name);
		    if (!empty($hidden_id)) {
			    $query->where('id', '!=', $hidden_id);
		    }
		    $count = $query->count();
		    if ($count > 0) {
			    $valid = 'false';
		    }
	    }
	    echo $valid;
	    exit;
    }

    public function checkEditPossibility(Request $request) {
	    $contact_id = $request->input('contact_id');

	    $business_id = request()->session()->get('user.business_id');

	    $count = Transaction::where('business_id', $business_id)
		    ->where('contact_id', $contact_id)
		    ->where('type', '!=', 'opening_balance')
		    ->count();

	    if ($count == 0) {
		    return ['success' => true ];
	    } else {
		    return ['success' => false,
			    'msg' => __('lang_v1.you_cannot_edit_this_contact')
		    ];
	    }

    }
}
