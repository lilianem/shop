@extends('layouts.app')

@section('title', "Shopify App")

@section('content')
<div class="container">
{!! Form::open(['route' => ['shop.update', $product->id], 'method' => 'PUT']) !!}
    <div class="form-group font-weight-bold">
		{{Form::label('id', 'id')}}
		{{Form::text('id', $product->id, ['class' => 'form-control', 'placeholder' => 'id']) }}
	</div>
	<div class="form-group font-weight-bold">
		{{Form::label('title', 'title')}}
		{{Form::text('title', $product->title, ['class' => 'form-control', 'placeholder' => 'title']) }}
	</div>
    @foreach($product->variants as $variant)
		@foreach($inventoryLevels as $inventory_level)
			<div class="form-group font-weight-bold">
				{{Form::label('available', 'available')}}
				{{Form::text('available', $inventory_level->available, ['class' => 'form-control', 'placeholder' => 'available']) }}
			</div>
        @endforeach 
    @endforeach               
    <div class="btn-group">
		<a href="{{ URL::to('/index') }}"><button type="button" class='btn btn-primary form-control' name="cancel" value="cancel">Cancel</button></a>
		<button type="submit" class='btn btn-primary form-control' name="confirm" value="confirm">Confirm</button>
		{!! Form::close() !!}
	</div>
</div>
@endsection