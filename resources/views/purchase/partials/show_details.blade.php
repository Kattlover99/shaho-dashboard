<div class="modal-header">
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="modalTitle"> @lang('purchase.purchase_details') (<b>@lang('purchase.ref_no'):</b> #{{ $purchase->ref_no }})
    </h4>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-sm-12">
      <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($purchase->transaction_date) }}</p>
    </div>
  </div>
  <div class="row invoice-info">
    <div class="col-sm-4 invoice-col">
      @lang('purchase.supplier'):
      <address>
        <strong>{{ $purchase->contact->supplier_business_name }}</strong>
        {{ $purchase->contact->name }}
        @if(!empty($purchase->contact->landmark))
          <br>{{$purchase->contact->landmark}}
        @endif
        @if(!empty($purchase->contact->city) || !empty($purchase->contact->state) || !empty($purchase->contact->country))
          <br>{{implode(',', array_filter([$purchase->contact->city, $purchase->contact->state, $purchase->contact->country]))}}
        @endif
        @if(!empty($purchase->contact->tax_number))
          <br>@lang('contact.tax_no'): {{$purchase->contact->tax_number}}
        @endif
        @if(!empty($purchase->contact->mobile))
          <br>@lang('contact.mobile'): {{$purchase->contact->mobile}}
        @endif
        @if(!empty($purchase->contact->email))
          <br>Email: {{$purchase->contact->email}}
        @endif
      </address>
      @if($purchase->document)
        @php
          $document_name = !empty(explode("_", $purchase->document, 2)[1]) ? explode("_", $purchase->document, 2)[1] : $purchase->document ;
        @endphp
        <a href="{{url( 'storage/documents/' . $purchase->document )}}" 
        download="{{$document_name}}" class="btn btn-sm btn-success pull-right no-print">
          <i class="fa fa-download"></i> 
            &nbsp;{{ __('purchase.download_document') }}
        </a>
      @endif
    </div>

    <div class="col-sm-4 invoice-col">
      @lang('business.business'):
      <address>
        <strong>{{ $purchase->business->name }}</strong>
        {{ $purchase->location->name }}
        @if(!empty($purchase->location->landmark))
          <br>{{$purchase->location->landmark}}
        @endif
        @if(!empty($purchase->location->city) || !empty($purchase->location->state) || !empty($purchase->location->country))
          <br>{{implode(',', array_filter([$purchase->location->city, $purchase->location->state, $purchase->location->country]))}}
        @endif
        
        @if(!empty($purchase->business->tax_number_1))
          <br>{{$purchase->business->tax_label_1}}: {{$purchase->business->tax_number_1}}
        @endif

        @if(!empty($purchase->business->tax_number_2))
          <br>{{$purchase->business->tax_label_2}}: {{$purchase->business->tax_number_2}}
        @endif

        @if(!empty($purchase->location->mobile))
          <br>@lang('contact.mobile'): {{$purchase->location->mobile}}
        @endif
        @if(!empty($purchase->location->email))
          <br>@lang('business.email'): {{$purchase->location->email}}
        @endif
      </address>
    </div>

    <div class="col-sm-4 invoice-col">
      <b>@lang('purchase.ref_no'):</b> #{{ $purchase->ref_no }}<br/>
      <b>@lang('messages.date'):</b> {{ @format_date($purchase->transaction_date) }}<br/>
      <!--<b>@lang('purchase.purchase_status'):</b> {{ ucfirst( $purchase->status ) }}<br>-->
      <b>@lang('purchase.payment_status'):</b> {{ ucfirst( $purchase->payment_status ) }}<br>
    </div>
  </div>

  <br>
  <div class="row">
    <div class="col-sm-12 col-xs-12">
      <div class="table-responsive">
        <table class="table bg-gray">
          <thead>
            <tr class="bg-green">
              <th>#</th>
              <th>@lang('product.product_name')</th>
              <th>@lang('purchase.purchase_quantity')</th>
              <th>@lang( 'purchase.unit_price' )</th>
              @if(session('business.enable_product_expiry'))
                {{--<th>@lang('product.mfg_date')</th>--}}
                <th>@lang('product.exp_date')</th>
              @endif
              <th>@lang('purchase.total_price')</th>
            </tr>
          </thead>
          @php 
            $total_before_tax = 0.00;
          @endphp
          @foreach($purchase->purchase_lines as $purchase_line)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>
                {{ $purchase_line->product->name }}
              </td>
              <td>{{ $purchase_line->quantity }}</td>
              <td ><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->purchase_price }}</span></td>
              @if(session('business.enable_product_expiry'))
              {{--<td>--}}
                {{--@if( !empty($purchase_line->product->expiry_period_type) )--}}
                  {{--@if(!empty($purchase_line->mfg_date))--}}
                    {{--{{ @format_date($purchase_line->mfg_date) }}--}}
                  {{--@endif--}}
                {{--@else--}}
                  {{--@lang('product.not_applicable')--}}
                {{--@endif--}}
              {{--</td>--}}
              <td>
                @if( !empty($purchase_line->product->expiry_period_type) )
                  @if(!empty($purchase_line->exp_date))
                    {{ @format_date($purchase_line->exp_date) }}
                  @endif
                @else
                  @lang('product.not_applicable')
                @endif
              </td>
              @endif
              <td><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->purchase_price * $purchase_line->quantity }}</span></td>
            </tr>
            @php
              $total_before_tax += ($purchase_line->quantity * $purchase_line->purchase_price);
            @endphp
          @endforeach
        </table>
      </div>
    </div>
  </div>
  <br>
  <div class="row">
    <div class="col-sm-12 col-xs-12">
      <h4>{{ __('sale.payment_info') }}:</h4>
    </div>
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="table-responsive">
        <table class="table">
          <tr class="bg-green">
            <th>#</th>
            <th>{{ __('messages.date') }}</th>
            <th>{{ __('purchase.ref_no') }}</th>
            <th>{{ __('sale.amount') }}</th>
            <th>{{ __('sale.payment_mode') }}</th>
            <th>{{ __('sale.payment_note') }}</th>
          </tr>
          @php
            $total_paid = 0;
          @endphp
          @forelse($purchase->payment_lines as $payment_line)
            @php
              $total_paid += $payment_line->amount;
            @endphp
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ @format_date($payment_line->paid_on) }}</td>
              <td>{{ $payment_line->payment_ref_no }}</td>
              <td><span class="display_currency" data-currency_symbol="true">{{ $payment_line->amount }}</span></td>
              <td>{{ $payment_methods[$payment_line->method] }}</td>
              <td>@if($payment_line->note) 
                {{ ucfirst($payment_line->note) }}
                @else
                --
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center">
                @lang('purchase.no_payments')
              </td>
            </tr>
          @endforelse
        </table>
      </div>
    </div>
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="table-responsive">
        <table class="table">
          <!-- <tr class="hide">
            <th>@lang('purchase.total_before_tax'): </th>
            <td></td>
            <td><span class="display_currency pull-right">{{ $total_before_tax }}</span></td>
          </tr> -->
          <tr>
            <th>@lang('purchase.net_total_amount'): </th>
            <td></td>
            <td><span class="display_currency pull-right">{{ $total_before_tax }}</span></td>
          </tr>
          {{--<tr>--}}
            {{--<th>@lang('purchase.discount'):</th>--}}
            {{--<td>--}}
              {{--<b>(-)</b>--}}
              {{--@if($purchase->discount_type == 'percentage')--}}
                {{--({{$purchase->discount_amount}} %)--}}
              {{--@endif--}}
            {{--</td>--}}
            {{--<td>--}}
              {{--<span class="display_currency pull-right">--}}
                {{--@if($purchase->discount_type == 'percentage')--}}
                  {{--{{$purchase->discount_amount * $total_before_tax / 100}}--}}
                {{--@else--}}
                  {{--{{$purchase->discount_amount}}--}}
                {{--@endif                  --}}
              {{--</span>--}}
            {{--</td>--}}
          {{--</tr>--}}
          {{--<tr>--}}
            {{--<th>@lang('purchase.purchase_tax'):</th>--}}
            {{--<td><b>(+)</b></td>--}}
            {{--<td><span class="display_currency pull-right">{{ $purchase->tax_amount }}</span></td>--}}
          {{--</tr>--}}
          @if( !empty( $purchase->shipping_charges ) )
            <tr class="hide">
              <th>@lang('purchase.additional_shipping_charges'):</th>
              <td><b>(+)</b></td>
              <td><span class="display_currency pull-right" >{{ $purchase->shipping_charges }}</span></td>
            </tr>
          @endif
          <tr>
            <th>@lang('purchase.purchase_total'):</th>
            <td></td>
            <td><span class="display_currency pull-right" data-currency_symbol="true" >{{ $purchase->final_total }}</span></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6 hide">
      <strong>@lang('purchase.shipping_details'):</strong><br>
      <p class="well well-sm no-shadow bg-gray">
        @if($purchase->shipping_details)
          {{ $purchase->shipping_details }}
        @else
          --
        @endif
      </p>
    </div>
    <div class="col-sm-6">
      <strong>@lang('purchase.additional_notes'):</strong><br>
      <p class="well well-sm no-shadow bg-gray">
        @if($purchase->additional_notes)
          {{ $purchase->additional_notes }}
        @else
          --
        @endif
      </p>
    </div>
  </div>

  {{-- Barcode --}}
  <div class="row print_section">
    <div class="col-xs-12">
      <img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($purchase->ref_no, 'C128', 2,30,array(39, 48, 54), true)}}">
    </div>
  </div>
</div>