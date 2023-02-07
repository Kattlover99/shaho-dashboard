@extends('layouts.app')
@section('title', __( 'report.profit_loss' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'report.profit_loss' )
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-3 col-md-offset-7 col-xs-6">
            <div class="input-group">
                <span class="input-group-addon bg-light-blue"><i class="fa fa-map-marker"></i></span>
                 <select class="form-control select2" id="profit_loss_location_filter">
                    @foreach($business_locations as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2 col-xs-6">
            <div class="form-group pull-right">
                <div class="input-group">
                  <button type="button" class="btn btn-primary" id="profit_loss_date_filter">
                    <span>
                      <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                    </span>
                    <i class="fa fa-caret-down"></i>
                  </button>
                </div>
            </div>
        </div>
    </div>
	<?php /*<div class="row">
        <div class="col-md-12">
			<div class="btn-group" style="float:right;">
				<button type="button" class="btn btn-info dropdown-toggle btn-xs" 
					data-toggle="dropdown" aria-expanded="false"><?php echo __("Select Category");?>
					<span class="caret"></span><span class="sr-only">Toggle Dropdown
					</span>
					
				</button>
					<ul class="dropdown-menu dropdown-menu-right" role="menu">
						<?php foreach($contactCategory as $key => $contactCategory_value):?>
							<li class='' >&nbsp;<span class='view_column_p'><input type='checkbox' class='view_column' name='category' value='<?php echo $key;?>'></span><span>&nbsp;<?php echo $contactCategory_value;?></span></li>
						<?php endforeach;?>	
					</ul>
				
			</div>
        </div>
       
    </div>*/?>
    <br>
	<div class="row">
        <div class="col-sm-6">
			<div class="row">
				<div class="col-sm-12">
					<div class="box box-solid">
						<div class="box-body">
							<div class="row">
								<div class="col-sm-12 text-center">
									<strong><?php echo $business->name;?>&nbsp;&nbsp;<?php echo date('d/m/Y');?></strong>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="box box-solid">
						<div class="box-body">
							<table class="table table-striped">
								<tr>
									<th>{{ __('Inventory Value') }}:</th>
									<td>
										 <span class="total_inventory">
											<i class="fa fa-refresh fa-spin fa-fw"></i>
										</span>
									</td>
								</tr>
								<tr>
									<th width='70%;'>{{ __('home.total_purchase') }}:</th>
									<td width='30%;'>
										 <span class="total_purchase">
											<i class="fa fa-refresh fa-spin fa-fw"></i>
										</span>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="box box-solid">
						<div class="box-body">
							<table class="table table-striped">
								<tr class="hide">
									<th>{{ __('report.opening_stock') }}:</th>
									<td>
										<span class="opening_stock">
											<i class="fa fa-refresh fa-spin fa-fw"></i>
										</span>
									</td>
								</tr>
								
								<tr>
									<th width='70%;'>{{ __('Total Sales') }}:</th>
									<td width='30%;'>
										 <span class="total_sell">
											<i class="fa fa-refresh fa-spin fa-fw"></i>
										</span>
									</td>
								</tr>

								<tr>
									<th>{{ __('Sales Profit') }}:</th>
									<td>
										 <span class="total_profits_sold_items">
											<i class="fa fa-refresh fa-spin fa-fw"></i>
										</span>
									</td>
								</tr>

								<tr>
									<th>{{ __('report.total_expense') }}:</th>
									<td>
										 <span class="total_expense">
											<i class="fa fa-refresh fa-spin fa-fw"></i>
										</span>
									</td>
								</tr>
								<tr class="hide">
									<th>{{ __('lang_v1.total_transfer_shipping_charges') }}:</th>
									<td>
										 <span class="total_transfer_shipping_charges">
											<i class="fa fa-refresh fa-spin fa-fw"></i>
										</span>
									</td>
								</tr>
								<tr class="hide">
									<th>{{ __('lang_v1.total_sell_discount') }}:</th>
									<td>
										 <span class="total_sell_discount">
											<i class="fa fa-refresh fa-spin fa-fw"></i>
										</span>
									</td>
								</tr>
								<tr>
									<th>{{ __('Expired Loss') }}</th>
									<td>
										<span class="total_adjustment">
											<i class="fa fa-refresh fa-spin fa-fw"></i>
										</span>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<?php /*<div class="col-sm-6 hide">
					<div class="box box-solid">
						<div class="box-body">
							<table class="table table-striped">
								<tr class="hide">
									<th>{{ __('report.closing_stock') }}:</th>
									<td>
										<span class="closing_stock">
											<i class="fa fa-refresh fa-spin fa-fw"></i>
										</span>
									</td>
								</tr>

								<tr class="hide">
									<th>{{ __('report.total_stock_recovered') }}:</th>
									<td>
										 <span class="total_recovered">
											<i class="fa fa-refresh fa-spin fa-fw"></i>
										</span>
									</td>
								</tr>
								<tr>
									<th>{{ __('lang_v1.total_purchase_discount') }}:</th>
									<td>
										 <span class="total_purchase_discount">
											<i class="fa fa-refresh fa-spin fa-fw"></i>
										</span>
									</td>
								</tr>
								<tr>
									<td colspan="2">
									&nbsp;
									</td>
								</tr>
								<tr>
									<td colspan="2">
									&nbsp;
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>*/?>
			</div>
			<br>
			<div class="row">
				<div class="col-sm-12">
					<div class="box box-solid">
						<div class="box-body">
							<div class="row">
								<div class="col-sm-8">
									<h3 class="text-muted">
										{{ __('report.net_profit') }}: 
									</h3>
								</div>
								<div class="col-sm-4">
									<h3 class="text-muted">
										<span class="net_profit" style='color:red;'>
											<i class="fa fa-refresh fa-spin fa-fw"></i>
										</span>
									</h3>
								</div>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="row">
				<div class="col-sm-12">
					<div class="box box-solid">
						<div class="box-body">
							<div class="row">
								<div class="col-sm-12 text-left">
									<strong>Choose Category for filter</strong>
								</div>
							</div>
							<?php foreach($contactCategory as $key => $contactCategory_value):?>
								<input type='checkbox' class='view_column' name='category' value='<?php echo $key;?>'>&nbsp;<?php echo $contactCategory_value;?><br/>
							<?php endforeach;?>
						</div>
					</div>
				</div>	
			</div>	
		</div>	
	</div>

</section>
<!-- /.content -->
@stop
@section('javascript')
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
@endsection
