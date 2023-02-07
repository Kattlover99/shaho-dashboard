@extends('layouts.app')
@section('title', 'Expire')

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
    <h1>@lang( 'All Expire')
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
            </div>
        </div>
    </div>

	<div class="box">
        <div class="box-header">
        	<h3 class="box-title">All Expire</h3>
            @can('exire_add')
            	<div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('ExpireController@create')}}">
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
				</div>
                <div class="table-responsive sell_table_html">
            	<table class="table table-bordered table-striped ajax_view" id="sell_table">
            		<thead>
            			<tr>
            				<th>#</th>
                            <th>Date</th>
    						<th>Erbil Expiry</th>
    						<th>Suli Expiry</th>
    						<th>Total</th>
    						<th>Each</th>
							<th class='notexport'>@lang('messages.action')</th>
            			</tr>
            		</thead>
                </table>
                </div>
            @endcan
        </div>
    </div>
</section>
@stop

@section('javascript')
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
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
	
	
		sell_table = $('#sell_table').DataTable({
			//processing: true,
			serverSide: true,
			//"searching": true,
			//paging: true,
			
			aaSorting: [[1, 'desc']],
			"ajax": {
				"url": "/expire",
				"data": function ( d ) {
					var start = $('#sell_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
					var end = $('#sell_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
					d.start_date = start;
					d.end_date = end;
				}
			},
			columns: [
				{ data: '#', name: 'id'  },
				{ data: 'date', name: 'date'},
				{ data: 'erbil_expiry', name: 'total_1'},
				{ data: 'suli_expiry', name: 'total_2'},
				{ data: 'total'},
				{ data: 'each'},
				{ data: 'action', name: 'action'}
			],
			"fnDrawCallback": function (oSettings) {
				
				__currency_convert_recursively($('#sell_table'));
			},
		});
});
</script>

@endsection