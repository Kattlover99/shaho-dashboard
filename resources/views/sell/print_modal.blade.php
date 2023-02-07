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
			<a href="#" class="print_custom" data-href="{{ url('sells/print_custom')}}" data-id='{{$id}}' ><i class="fa fa-print" aria-hidden="true"></i> Print</a>
			&nbsp;&nbsp;<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>