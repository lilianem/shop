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
      
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Product N°#</th>
                <th>Designation</th>
                <th>Quantity</th>
                <th>Action</th>
              </tr>
            </thead>           
            @foreach($product2ss as $product2s)
              @foreach($product2s as $product2)
              <tbody>
                <tr>
                  <td>{{$product2->id}}</td>
                  <td>{{$product2->title}}</td>
                  @foreach($product2->variants as $variant)                  
                  <td>{{$variant->inventory_quantity}}</td>
                  @endforeach
                  <td><a href=""><button type="button" class="btn btn-primary btn-sm">Show</button></a></td>                  
                  <td><a href="{{ URL::to('/shop/edit/' . $product2->id . '/' . $token) }}"><button type="button" class="btn btn-primary btn-sm">Update</button></a></td>
              </tbody>
              @endforeach
            @endforeach
          </table>
      
      </div>
    </div>
  </div>
</div>
@endsection