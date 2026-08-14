@extends('layouts.dashboard')
@section('title','Edit Product')
@section('content')
<div class="dashboard-hero"><div><div class="dashboard-kicker">Seller Centre</div><h1 class="dashboard-title">Edit Product</h1></div><a href="{{ route('seller.products.index') }}" class="btn-outline-dark">Back</a></div>
<section class="dashboard-panel"><form method="POST" action="{{ route('seller.products.update',$product) }}" class="payment-form">@csrf @method('PUT')
@foreach([['name','Product name','text'],['sku','SKU','text'],['category','Category','text'],['price','Price (UGX)','number'],['quantity','Stock','number'],['reorder_level','Reorder level','number'],['image','Image URL','text']] as [$name,$label,$type])<label>{{ $label }}<input name="{{ $name }}" type="{{ $type }}" value="{{ old($name,$product->$name) }}" {{ in_array($name,['name','sku','price','quantity','reorder_level']) ? 'required' : '' }}></label>@endforeach
<label>Description<textarea name="description" rows="5">{{ old('description',$product->description) }}</textarea></label><button class="btn-solid" type="submit">Save Changes</button></form></section>
@endsection
