<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('ContactController@update', [$contact->id]), 'method' => 'PUT', 'id' => 'contact_edit_form']) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('contact.edit_contact')</h4>
    </div>



    <div class="modal-body">
      <div class="row">

        <div class="col-md-6">
          <div class="form-group">
              {!! Form::label('type', __('contact.contact_type') . ':*' ) !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-user"></i>
                  </span>
                  {!! Form::select('type', $types, $contact->type, ['class' => 'form-control', 'id' => 'contact_type','placeholder' => __('messages.please_select'), 'required']); !!}
                  <input type="hidden" id="hidden_id" value="{{ $contact->id }}">
              </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
              {!! Form::label('name', __('contact.name') . ':*') !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-user"></i>
                  </span>
                  @if ($count == 0)
                  {!! Form::text('name', $contact->name, ['class' => 'form-control','placeholder' => __('contact.name'), 'required']); !!}
                  @else
                  {!! Form::text('name', $contact->name, ['class' => 'form-control','placeholder' => __('contact.name'), 'required', 'readonly']); !!}
                  @endif
              </div>
          </div>
        </div>

        <div class="clearfix"></div>
          <div class="col-md-4 supplier_fields">
              <div class="form-group">
                  {!! Form::label('supplier_business_name', __('business.business_name') . ':*') !!}
                  <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-briefcase"></i>
                  </span>
                      {!! Form::text('supplier_business_name',
                      $contact->supplier_business_name, ['class' => 'form-control', 'required', 'placeholder' => __('business.business_name')]); !!}
                  </div>
              </div>
          </div>
        <div class="clearfix"></div>

        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('mobile', __('contact.mobile') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-mobile"></i>
                    </span>
                    {!! Form::text('mobile', $contact->mobile, ['class' => 'form-control', 'required', 'placeholder' => __('contact.mobile')]); !!}
                </div>
            </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
              {!! Form::label('opening_balance', __('lang_v1.opening_balance') . ':') !!}
              <div class="input-group">
              <span class="input-group-addon">
                  <i class="fa fa-money"></i>
              </span>
                  {!! Form::text('opening_balance', $opening_balance, ['class' => 'form-control input_number']); !!}
              </div>
          </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('city', __('business.city') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-map-marker"></i>
                    </span>
                    {!! Form::text('city', $contact->city, ['class' => 'form-control', 'required', 'placeholder' => __('business.city')]); !!}
                </div>
            </div>
        </div>
        <div class="col-md-3">
              <div class="form-group">
                  {!! Form::label('contact_person', __('lang_v1.contact_person') . ':') !!}
                  <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-id-badge"></i>
            </span>
                      {!! Form::text('contact_person', $contact->contact_person, ['class' => 'form-control']); !!}
                  </div>
              </div>
          </div>
		
		<!-- <div class="col-md-6">
			<div class='form-group'>
				{!! Form::label('category', __('Category') . ':') !!}
				<select id='category' name='category' class='form-control select2' style='width:100% !important;'>
					<?php foreach ($contactCategory as $key => $column):?>
						<option value='<?php echo $key;?>' <?php echo ($contact->category == $key) ? 'selected' : '';?>><?php echo $column;?></option>
					<?php endforeach;?>
				</select>
			</div>
		</div>
		<div class="col-md-3">
          <div class="form-group">
              {!! Form::label('past_debit', __('lang_v1.past_debit') . ':') !!}
              <div class="input-group">
              <span class="input-group-addon">
                  <i class="fa fa-money"></i>
              </span>
              {!! Form::text('past_debit', $contact->past_debit, ['class' => 'form-control input_number', 'required']); !!}
              </div>
          </div>
      </div> -->


      <div class="col-md-3">
          <div class="form-group">
              {!! Form::label('debit_in_iqd', __('lang_v1.debit_in_iqd') . ':') !!}
              <div class="input-group">
              <span class="input-group-addon">
                  <i class="fa fa-money"></i>
              </span>
              {!! Form::text('debit_in_iqd', $contact->debit_in_iqd, ['class' => 'form-control input_number', 'required']); !!}
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="form-group">
              {!! Form::label('debit_in_usd', __('lang_v1.debit_in_usd') . ':') !!}
              <div class="input-group">
              <span class="input-group-addon">
                  <i class="fa fa-money"></i>
              </span>
              {!! Form::text('debit_in_usd', $contact->debit_in_usd, ['class' => 'form-control input_number', 'required']); !!}
              </div>
          </div>
      </div>
 
        <div class="clearfix"></div> 

        </div>
    </div>
	<div class="row">
		
	</div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->