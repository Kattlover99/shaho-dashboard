@extends('layouts.app')

@section('title', "Add Expire")

@section('content')
<input type="hidden" id="__precision" value="{{config('constants.currency_precision')}}">
<!-- Content Header (Page header) -->
<section class="content-header">
	@if (!empty($expireData->id))
		<h1>Edit Expire</h1>
	@else
		<h1>Add Expire</h1>
	@endif
</section>
<!-- Main content -->
<section class="content no-print">
	@if (!empty($expireData->id))
		{!! Form::open(['url' => 'expire/update/'.$expireData->id, 'method' => 'post', 'id' => 'add_expire_form' ]) !!}
	@else
		{!! Form::open(['url' => action('ExpireController@store'), 'method' => 'post', 'id' => 'add_expire_form' ]) !!}
	@endif
	<div class="row">
		<div class="col-md-12 col-sm-12">
			<div class="box box-solid">
				<div class="box-body">

					
					
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								{!! Form::label('transaction_date', 'Date:*') !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</span>
									@if (!empty($expireData->id))	
										{!! Form::text('transaction_date', $transaction_date, ['class' => 'form-control', 'readonly', 'required']); !!}
									@else
										{!! Form::text('transaction_date', @format_date('now'), ['class' => 'form-control', 'readonly', 'required']); !!}
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-3">
							<div class="form-group">
								{!! Form::label('total_1', 'Erbil Expiry:*') !!}
								<div class="input-group">
									{!! Form::text('total_1', $expireData->total_1, ['class' => 'form-control total_1', 'required']); !!}
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								{!! Form::label('total_2', 'Suli Expiry:*') !!}
								<div class="input-group">
									{!! Form::text('total_2', $expireData->total_2, ['class' => 'form-control', 'required']); !!}
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								{!! Form::label('total', 'Total:') !!}
								<div class="input-group">
									{!! Form::number('total', $expireData->total_1+$expireData->total_2, ['class' => 'form-control', 'readonly']); !!}
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								{!! Form::label('each', 'Each:') !!}
								<div class="input-group">
									{!! Form::number('each', ($expireData->total_1+$expireData->total_2) / 2, ['class' => 'form-control', 'readonly']); !!}
								</div>
							</div>
						</div>
					</div>

					
				</div>
				<!-- /.box-body -->
			</div>
			

			<div class="box box-solid">
				<div class="box-body">
					<div class="row">
						<div class="col-sm-12">
							<button type="submit" id="submit-expire" class="btn btn-primary pull-right btn-flat">@lang('messages.submit')</button>
						</div>
						
					</div>
				</div>	
			</div>	
			
		</div>
	</div>
	
	{!! Form::close() !!}
</section>


@stop

@section('javascript')
	<script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
	
	<script>
	$('#total_1').on('keyup change', function () {
		
			var total_1 = parseFloat($('#total_1').val(), 10);
			var total_2 = parseFloat($('#total_2').val(), 10);
			
			var total = total_1 + total_2;
			console.log(total);
			if (total) {
				$('#total').val(total);	
				$('#each').val(total/2);		
			}
	});
	
	$('#total_2').on('keyup change', function () {
		
			var total_1 = parseFloat($('#total_1').val(), 10);
			var total_2 = parseFloat($('#total_2').val(), 10);
			
			var total = total_1 + total_2;
			console.log(total);
			if (total) {
				$('#total').val(total);	
				$('#each').val(total/2);		
			}
	});
	</script>
@endsection
