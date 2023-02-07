@extends('layouts.app')
@section('title', __( 'lang_v1.all_sales'))

@section('css')
<style>
.column_right {
	float:right;
}
#sell_table_filter{
	/*display:none;*/
}
</style>
@stop
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang( 'sale.sells')
        <small></small>
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary" id="accordion">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseFilter" aria-expanded="true">
                            <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters')
                        </a>
                    </h3>
                </div>
                <div id="collapseFilter" class="panel-collapse active collapse in" aria-expanded="true">
                    <div class="box-body">
                        {!! Form::open(['url' => action('SellController@index'), 'method' => 'get', 'id' => 'filter_form' ]) !!}
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('locations', __('sale.location') . ':') !!}
                                {!! Form::select('locations', $locations, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'locations']); !!}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('payment_status', __('sale.payment_status') . ':') !!}
                                {!! Form::select('payment_status', $payment_status, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'payment_status']); !!}
                            </div>
                        </div>
						<div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('locations', __('Search Exact Word') . ':') !!}
                                <input class="form-control" name="search_record" type="text" value="" id="search_record" aria-invalid="false">
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

	<div class="box">
        <div class="box-header">
        	<h3 class="box-title">@lang( 'lang_v1.all_sales')</h3>
            @can('sell.create')
            	<div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('SellController@create')}}">
    				<i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endcan
        </div>
        <div class="box-body">
            @can('direct_sell.access')
                <div class="row">
                    <div class="col-sm-8">
                        <div class="form-group">
                            <div class="input-group">
                              <button type="button" class="btn btn-primary" id="sell_date_filter">
                                <span>
                                  <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                                </span>
                                <i class="fa fa-caret-down"></i>
                              </button>
                            </div>
                          </div>
                    </div>
					<div class="col-sm-4">
						<div class="btn-group" style="float:right;">
							<button type="button" class="btn btn-info dropdown-toggle btn-xs" 
								data-toggle="dropdown" aria-expanded="false"><?php echo __("Select Columns");?>
								<span class="caret"></span><span class="sr-only">Toggle Dropdown
								</span>
								
							</button>
							<?php if (auth()->user()->can('profit_coulmn_read.access')):?>
								<ul class="dropdown-menu dropdown-menu-right" role="menu">
									<li class='' >&nbsp;<span class='view_column_p'><input type='checkbox' class='view_column' checked value='0'></span><span>&nbsp;@lang('messages.date')</span></li>
									<li class='' >&nbsp;<span class='view_column_p'><input type='checkbox' class='view_column' checked value='1'></span>&nbsp;@lang('sale.invoice_no')</li>
									<li class='' >&nbsp;<input type='checkbox' class='view_column' checked value='2'></span><span>&nbsp;@lang('sale.customer_name')</span></li>
									<li class='' >&nbsp;<input type='checkbox' class='view_column'  value='3'>&nbsp;@lang('Category')</li>
									<li class='' >&nbsp;<input type='checkbox' class='view_column'  value='4'>&nbsp;@lang('sale.location')</li>
									<li class='' >&nbsp;<input type='checkbox' class='view_column'  checked value='5'>&nbsp;@lang('sale.payment_status')</li>
									<li class='' >&nbsp;<input type='checkbox' class='view_column' checked value='6'>&nbsp;@lang('sale.total_amount')</li>
									<li class='' >&nbsp;<input type='checkbox' class='view_column'  value='7'>&nbsp;Pro</li>
									<li class='' >&nbsp;<input type='checkbox' class='view_column'  value='8'>&nbsp;@lang('sale.total_paid')</li>
									<li class='' >&nbsp;<input type='checkbox' class='view_column'  value='9'>&nbsp;@lang('sale.total_remaining')</li>
									
								</ul>
							<?php else:?>
							<ul class="dropdown-menu dropdown-menu-right" role="menu">
									<li class='' >&nbsp;<input type='checkbox' class='view_column' checked value='0'>&nbsp;@lang('messages.date')</li>
									<li class='' >&nbsp;<input type='checkbox' class='view_column' checked value='1'>&nbsp;@lang('sale.invoice_no')</li>
									<li class='' >&nbsp;<input type='checkbox' class='view_column' checked value='2'>&nbsp;@lang('sale.customer_name')</li>
									<li class='' >&nbsp;<input type='checkbox' class='view_column' checked value='3'>&nbsp;@lang('Category')</li>
									<li class='' >&nbsp;<input type='checkbox' class='view_column'  value='4'>&nbsp;@lang('sale.location')</li>
									<li class='' >&nbsp;<input type='checkbox' class='view_column'  checked value='5'>&nbsp;@lang('sale.payment_status')</li>
									<li class='' >&nbsp;<input type='checkbox' class='view_column' checked value='6'>&nbsp;@lang('sale.total_amount')</li>
									<li class='' >&nbsp;<input type='checkbox' class='view_column'  value='7'>&nbsp;@lang('sale.total_paid')</li>
									<li class='' >&nbsp;<input type='checkbox' class='view_column'  value='8'>&nbsp;@lang('sale.total_remaining')</li>
									
								</ul>							
							<?php endif;?>
						</div>
					</div>
                </div>
                <div class="table-responsive sell_table_html">
            	<table class="table table-bordered table-striped ajax_view" id="sell_table">
            		<thead>
            			<tr>
            				<th>@lang('messages.date')</th>
                            <th>@lang('sale.invoice_no')</th>
    						<th>@lang('sale.customer_name')</th>
							<th>@lang('Category')</th>
                            <th>@lang('sale.location')</th>
                            <th>@lang('sale.payment_status')</th>
							<th>@lang('sale.total_amount')</th>
							<?php if (auth()->user()->can('profit_coulmn_read.access')):?>
								<th>Pro</th>
							<?php endif;?>
    						
                            <th>@lang('sale.total_paid')</th>
                            <th>@lang('sale.total_remaining')</th>
                            <th class='notexport'>@lang('messages.action')</th>
            			</tr>
            		</thead>
                    <tfoot>
                        <tr class="bg-gray font-17 footer-total text-center">
							<td colspan="5"><strong>@lang('sale.total'):</strong></td>
                            
                            <td  id="footer_payment_status_count"></td>
							<td><span class="display_currency" id="footer_sale_total" data-currency_symbol ="true"></span></td>
							<?php if (auth()->user()->can('profit_coulmn_read.access')):?>
								<td ><span class="display_currency" id="footer_profit_total" data-currency_symbol ="true"></span></td>
							<?php else:?>
							
							<?php endif;?>
                            
                            
                            <td><span class="display_currency" id="footer_total_paid" data-currency_symbol ="true"></span></td>
                            <td><span class="display_currency" id="footer_total_remaining" data-currency_symbol ="true"></span></td>
                            <td></td>
                        </tr>
                    </tfoot>
            	</table>
                </div>
            @endcan
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<!-- Modal -->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">@lang('messages.print_options')</h4>
        </div>
        <div class="modal-body">
			
			<div class="row" style='padding-bottom:15px;'>
				<div class="col-md-3">
					<h5 class="modal-title">Headers</h5>
				</div>
			</div>
			<div class="row">
				<?php /*<div class="col-md-3">
					<input type="radio" name='header_1' value='Medical Tools' checked class="check_all input-icheck" >Medical Tools
				</div>
				<div class="col-md-3">
					<input type="radio" name='header_1' value='Biolive' class="check_all input-icheck" >Biolive
				</div>
				<div class="col-md-3">
					<input type="radio" name='header_1' value='Sidra'  class="check_all input-icheck" >Sidra
				</div>*/?>
				<div class="col-md-6">
					<?php
					$business_id = request()->session()->get('user.business_id');
					$invoiceLayout = \App\InvoiceLayout::where('business_id', $business_id)->get();
					?>
					<div class='form-group'>
						<select id='header_1' name='header_1' class='form-control select2' style='width:100% !important;'>
							<?php foreach ($invoiceLayout as $invoiceLayout_value):?>
								<option value='<?php echo $invoiceLayout_value->id;?>'><?php echo $invoiceLayout_value->name;?></option>
							<?php endforeach;?>
						</select>
					</div>
				</div>
			</div>
			<hr/>
			<div class="row" style='padding-bottom:15px;'>
				<div class="col-md-3">
					<h5 class="modal-title">Price</h5>
				</div>
			</div>
			<div class="row" >
				<div class="col-md-3">
					<input type="radio" name='price_column' value='price_show' checked class="check_all input-icheck" >Show
				</div>
				<div class="col-md-3">
					<input type="radio" name='price_column' value='price_hide' class="check_all input-icheck" >Hide
				</div>
			</div>
			<hr/>
			<div class="row" style='padding-bottom:15px;'>
				<div class="col-md-3">
					<h5 class="modal-title">Currancy</h5>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<input type="radio" name='currancy_column' value='USD' checked class="check_all input-icheck" >USD
				</div>
				<div class="col-md-3">
					<input type="radio" name='currancy_column' value='IQD' class="check_all input-icheck" >IQD
				</div>
			</div>
        </div>
        <div class="modal-footer">
			<a href="#" class="print_custom" data-href="{{ url('sells/print_custom')}}" data-id='' ><i class="fa fa-print" aria-hidden="true"></i> Print</a>
			&nbsp;&nbsp;<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>

<!-- This will be printed -->
<!-- <section class="invoice print_section" id="receipt_section">
</section> -->

@stop

@section('javascript')
<script type="text/javascript">

$(document).ready( function(){
	var dateRangeSettings = {
		ranges: ranges,
		startDate: '',
		endDate: '',
		locale: {
			cancelLabel: LANG.clear,
			applyLabel: LANG.apply,
			customRangeLabel: LANG.custom_range,
			format: moment_date_format,
			toLabel: "~",
		}
	};
    //Date range as a button
    $('#sell_date_filter').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#sell_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            sell_table.ajax.reload();
        }
    );
    $('#sell_date_filter').on('cancel.daterangepicker', function(ev, picker) {
        $('#sell_date_filter').html('<i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}');
        sell_table.ajax.reload();
    });
	
	$('.print_custom').on('click', function() {
		
		$('#myModal').modal('toggle');
		
		
		var href = $(this).attr('data-href');
		var id = $(this).attr('data-id');
		
		var currancy_column = $('input[name="currancy_column"]:checked').val();
		var header_1 = $('select[name="header_1"]').val();
		var price_column = $('input[name="price_column"]:checked').val();
		
		var new_url = href+'/'+id+'/'+currancy_column+'/'+header_1+'/'+price_column;
		
		$.ajax({
            method: "GET",
            url: new_url,
            dataType: "json",
            success: function(result){

                if(result.success == 1 && result.receipt.html_content != ''){
                    $('#receipt_section').html(result.receipt.html_content);
                    __currency_convert_recursively($('#receipt_section'));
                    setTimeout(function(){window.print();}, 1000);
                } else {
                    toastr.error(result.msg);
                }
            }
        });
		
		console.log('new_url_'+new_url);
		//$('.invoice_generate_link').click();
		
	})
	
	$('#sell_table').on('click', '.id_set', function() {
		id = $(this).attr('data-id');
		
		//console.log('id_'+id);
		$('.print_custom').attr('data-id', id);
	});
	//Kak Abdulrahman
	<?php if (auth()->user()->can('profit_coulmn_read.access')):?>
		sell_table = $('#sell_table').DataTable({
			//processing: true,
			serverSide: true,
			//"searching": true,
			//paging: true,
			
			aaSorting: [[0, 'desc'], [1, 'desc']],
			"ajax": {
				"url": "/sells",
				"data": function ( d ) {
					var start = $('#sell_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
					var end = $('#sell_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
					d.start_date = start;
					d.search_record = $('#search_record').val();
					d.end_date = end;
					d.is_direct_sale = 1;
					d.location = $('#locations').val();
					d.payment_status = $('#payment_status').val();
				}
			},
			columns: [
				{ data: 'transaction_date', name: 'transaction_date'  },
				{ data: 'invoice_no', name: 'invoice_no'},
				{ data: 'name', name: 'contacts.name'},
				{ data: 'category', name: 'contact_categories.name'},
				{ data: 'business_location', name: 'bl.name'},
				{ data: 'payment_status', name: 'payment_status'},
				{ data: 'final_total', name: 'final_total'},
				{ data: 'profit', name: 'profit'},
				{ data: 'total_paid', name: 'total_paid'},
				{ data: 'total_remaining', name: 'total_remaining'},
				{ data: 'action', name: 'action'}
			],
			columnDefs: [
					{
						'searchable'    : false, 
						'targets'       : [6] 
					},
					{
						//'targets': [0, 4, 6, 7],
						'targets': [3, 4, 7, 8, 9],
						visible: false,
					}
					
				],
			"fnDrawCallback": function (oSettings) {
				
				$('#footer_profit_total').text(sum_table_col($('#sell_table'), 'profit-paid'));
		
				$('#footer_sale_total').text(sum_table_col($('#sell_table'), 'final-total'));
				
				$('#footer_total_paid').text(sum_table_col($('#sell_table'), 'total-paid'));

				$('#footer_total_remaining').text(sum_table_col($('#sell_table'), 'total-remaining'));

				$('#footer_payment_status_count').html(__sum_status_html($('#sell_table'), 'payment-status-label'));

				__currency_convert_recursively($('#sell_table'));
			},
			createdRow: function( row, data, dataIndex ) {
				//$( row ).find('td:eq(4)').attr('class', 'clickable_td');
			}
		});
	<?php else:?>
		sell_table = $('#sell_table').DataTable({
			//processing: true,
			serverSide: true,
			//paging: true,
			
			aaSorting: [[0, 'desc'], [1, 'desc']],
			"ajax": {
				"url": "/sells",
				"data": function ( d ) {
					var start = $('#sell_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
					var end = $('#sell_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
					d.start_date = start;
					d.end_date = end;
					d.search_record = $('#search_record').val();
					d.is_direct_sale = 1;
					d.location = $('#locations').val();
					d.payment_status = $('#payment_status').val();
				}
			},
			columns: [
				{ data: 'transaction_date', name: 'transaction_date'  },
				{ data: 'invoice_no', name: 'invoice_no'},
				{ data: 'name', name: 'contacts.name'},
				{ data: 'category', name: 'contact_categories.name'},
				{ data: 'business_location', name: 'bl.name'},
				{ data: 'payment_status', name: 'payment_status'},
				//{ data: 'profit', name: 'profit'},
				{ data: 'final_total', name: 'final_total'},
				{ data: 'total_paid', name: 'total_paid'},
				{ data: 'total_remaining', name: 'total_remaining'},
				{ data: 'action', name: 'action'}
			],
			columnDefs: [
					{
						'searchable'    : false, 
						'targets'       : [6] 
					},
					{
						'targets': [3, 4, 7],
						visible: false,
					}
				],
			"fnDrawCallback": function (oSettings) {
				
				//$('#footer_profit_total').text(sum_table_col($('#sell_table'), 'profit-paid'));
		
				$('#footer_sale_total').text(sum_table_col($('#sell_table'), 'final-total'));
				
				$('#footer_total_paid').text(sum_table_col($('#sell_table'), 'total-paid'));

				$('#footer_total_remaining').text(sum_table_col($('#sell_table'), 'total-remaining'));

				$('#footer_payment_status_count').html(__sum_status_html($('#sell_table'), 'payment-status-label'));

				__currency_convert_recursively($('#sell_table'));
			},
			createdRow: function( row, data, dataIndex ) {
				//$( row ).find('td:eq(4)').attr('class', 'clickable_td');
			}
		});
	<?php endif;?>
    
	/*sell_table.column(2)
	 //.search("^" + $(this).val() + "$", true, false, true)
	 //.search("^" + this.value + "$", true, false, true)
	 .search( "^" + $(this).val(), true, false, true )
	 .draw();*/
	 
    $('#locations, #payment_status').change( function(){
        console.log('Here');
        sell_table.ajax.reload();
    });
	
	
	
	$('#search_record').on( 'keyup', function () {
		sell_table.ajax.reload();
	} );
	
	
	$("input:checkbox.view_column").change(function() {
		var ischecked= $(this).is(':checked');
		
		var data_column = $(this).val();
		if(!ischecked) {
			
			sell_table.column(data_column).visible(false);
		} else {
			
			sell_table.column(data_column).visible(true);
			
			
		}
		
		$('#footer_payment_status_count').html(__sum_status_html($('#sell_table'), 'payment-status-label'));
			
			$('#footer_profit_total').text(sum_table_col($('#sell_table'), 'profit-paid'));
			
			$('#footer_sale_total').text(sum_table_col($('#sell_table'), 'final-total'));
			
			$('#footer_total_paid').text(sum_table_col($('#sell_table'), 'total-paid'));

			$('#footer_total_remaining').text(sum_table_col($('#sell_table'), 'total-remaining'));

			$('#footer_payment_status_count').html(__sum_status_html($('#sell_table'), 'payment-status-label'));
			
			__currency_convert_recursively($('#sell_table'));
		 
	});
	
	<?php if (auth()->user()->can('profit_coulmn_read.access')):?>
		$(window).load(function() {
			/*sell_table.column(0).visible(false);
			sell_table.column(4).visible(false);
			sell_table.column(5).visible(false);
			sell_table.column(7).visible(false);
			sell_table.column(8).visible(false);*/
		});
	<?php else:?>
		/*sell_table.column(0).visible(false);
		sell_table.column(4).visible(false);
		sell_table.column(6).visible(false);
		sell_table.column(7).visible(false);*/
	<?php endif;?>
});
</script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection