<?php

namespace App\Http\Controllers;

use App\PurchaseLine;
use App\TransactionSellLinesPurchaseLines;
use Illuminate\Http\Request;

use App\Currency;
use App\Business;
use App\TaxRate;
use App\Transaction;
use App\BusinessLocation;
use App\TransactionSellLine;
use App\User;
use App\Bank;
use App\CustomerGroup;
use Yajra\DataTables\Facades\DataTables;
use App\ExpireData;
use App\TransactionPayment;
use DB;

use App\Utils\ContactUtil;
use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Redirect;
use Log;

class ExpireController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $contactUtil;
    protected $businessUtil;
    protected $transactionUtil;


    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ContactUtil $contactUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;

        $this->dummyPaymentLine = ['method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '', 
        'is_return' => 0, 'transaction_no' => ''];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		//dump(auth()->user()->can('profit_coulmn_read.access'));exit;
        if (!auth()->user()->can('exire_view')) {
            abort(403, 'Unauthorized action.');
        }


        if (request()->ajax()) {
         
			$business_id = request()->session()->get('user.business_id');
			
            $expireData = ExpireData::where('business_id', $business_id);;
			
			if (!empty(request()->start_date) && !empty(request()->end_date) && request()->start_date != 'Invalid date' && request()->end_date != 'Invalid date') {
                $start = request()->start_date;
                $end =  request()->end_date;
                $expireData->whereDate('date', '>=', $start)
                            ->whereDate('date', '<=', $end);
            }
			
			
            return Datatables::of($expireData)
				->editColumn('#', function ($data){
					
					static $i = 1;
                  return $i++;
				})
				->editColumn('date', function ($data) {
                  return $data->date;
				})
				->editColumn('erbil_expiry', function ($data) {
                  return '<span class="display_currency total-paid column_right" data-currency_symbol="true" data-orig-value="{{$data->total_1}}">'.$data->total_1.'</span>';
				})

				->editColumn('suli_expiry', function ($data) {
                  return '<span class="display_currency total-paid column_right" data-currency_symbol="true" data-orig-value="{{$data->total_2}}">'.$data->total_2.'</span>';
				})
				->editColumn('total', function ($data) {
                  $value = number_format($data->total_1+$data->total_2, 2);
				  
				  return '<span class="display_currency total-paid column_right" data-currency_symbol="true" data-orig-value="{{$value}}">'.$value.'</span>';
				})	
				->editColumn('each', function ($data) {
                  $value = number_format(($data->total_1+$data->total_2)/2, 2);
				  
				  return '<span class="display_currency total-paid column_right" data-currency_symbol="true" data-orig-value="{{$value}}">'.$value.'</span>';
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
                    
					@can("exire_update")
					<li><a href="{{action(\'ExpireController@edit\', [$id])}}"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a></li>
                    @endcan
					@can("exire_delete")
					<li><a href="{{action(\'ExpireController@delete\', [$id])}}" class=""><i class="fa fa-trash"></i> @lang("messages.delete")</a></li>
                    @endcan
                    </ul></div>'
                )					
				->rawColumns(['action', 'erbil_expiry', 'suli_expiry', 'total', 'each'])
                ->make(true);
        }
        return view('expire_data.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		if (!auth()->user()->can('exire_add')) {
            abort(403, 'Unauthorized action.');
        }
		$expireData = new ExpireData;
		
		$id = '';
        return view('expire_data.create', compact('expireData', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$business_id = request()->session()->get('user.business_id');
		$expireData = new ExpireData;
	 	$transaction_date = str_replace('/', '-', $request->transaction_date);
		
		$transaction_date = date("Y-m-d", strtotime($transaction_date));
		
		$expireData->date = $transaction_date;
		$expireData->business_id = $business_id;
		$expireData->total_1 = $request->total_1;
		$expireData->total_2 = $request->total_2;
		$expireData->save();
		
		$output = ['success' => 1, 'msg' => 'New expire added' ];
		
		return redirect('/expire')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, ExpireData $expireData)
    {
		if (!auth()->user()->can('exire_update')) {
            abort(403, 'Unauthorized action.');
        }
		$transaction_date = $expireData->date;
		$transaction_date = date("d/m/Y", strtotime($transaction_date));
		
        return view('expire_data.create', compact('expireData', 'transaction_date'));
    }

    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExpireData $expireData)
    {
		$business_id = request()->session()->get('user.business_id');
		
      	$transaction_date = str_replace('/', '-', $request->transaction_date);
		
		$transaction_date = date("Y-m-d", strtotime($transaction_date));
		
		$expireData->date = $transaction_date;
		$expireData->business_id = $business_id;
		$expireData->total_1 = $request->total_1;
		$expireData->total_2 = $request->total_2;
		$expireData->save();
		
		$output = ['success' => 1, 'msg' => 'Expire updated' ];
		
		return redirect('/expire')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, ExpireData $expireData)
    {
        if (!auth()->user()->can('exire_delete')) {
            abort(403, 'Unauthorized action.');
		}
		
		$expireData->delete();
		
		$output = ['success' => 1, 'msg' => 'Expire deleted' ];
		
		return redirect('/expire')->with('status', $output);
    }

    
}
