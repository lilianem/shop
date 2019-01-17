@extends('layouts.app')

@section('title', "Shopify App")

@section('content')
<div class="container">
@foreach($product as $productdet)
{!! Form::open(['route' => ['shop.update', $productdet->id, $access_token], 'method' => 'PUT']) !!}	
    <div class="form-group font-weight-bold">
		{{Form::label('id', 'id')}}
		{{Form::text('id', $productdet->id, ['class' => 'form-control', 'placeholder' => 'id']) }}
	</div>
	<div class="form-group font-weight-bold">
		{{Form::label('title', 'title')}}
		{{Form::text('title', $productdet->title, ['class' => 'form-control', 'placeholder' => 'title']) }}
	</div>
    @foreach($productdet->variants as $variant)
		@foreach($inventoryLevels as $inventory_level)
			@foreach($inventory_level as $inventory_leveldet)
				<div class="form-group font-weight-bold">
					{{Form::label('available', 'available')}}
					{{Form::text('available', $inventory_leveldet->available, ['class' => 'form-control', 'placeholder' => 'available']) }}
				</div>
			@endforeach
        @endforeach 
    @endforeach               
    <div class="btn-group">
		<a href="{{ URL::to('/index') }}"><button type="button" class='btn btn-primary form-control' name="cancel" value="cancel">Cancel</button></a>
		<button type="submit" class='btn btn-primary form-control' name="confirm" value="confirm">Confirm</button>
		{!! Form::close() !!}
@endforeach
	</div>
</div>
@endsection