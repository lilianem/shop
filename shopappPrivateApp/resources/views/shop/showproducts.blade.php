@extends('layouts.app')

@section('title', "Shopify App")

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header"> 
      <h2>Products List</h2>    
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if(count($products) >= 1)
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Product N°#</th>
                <th>Designation</th>
                <th>Quantity</th>
                <th>Action</th>
              </tr>
            </thead>           
            @foreach($products as $product)
              <tbody>
                <tr>
                  <td>{{$product->id}}</td>
                  <td>{{$product->title}}</td>
                  @foreach($product->variants as $variant)                  
                  <td>{{$variant->inventory_quantity}}</td>
                  @endforeach
                  <td><a href=""><button type="button" class="btn btn-primary btn-sm">Show</button></a></td>                  
                  <td><a href="{{ URL::to('/shop/edit/' . $product->id) }}"><button type="button" class="btn btn-primary btn-sm">Update</button></a></td>
              </tbody>
            @endforeach
          </table>
        @else
          <p>Aucun produit existant</p>
        @endif  
      </div>
    </div>
  </div>
</div>
@endsection