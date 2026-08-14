@extends('layouts.dashboard')
@section('title', 'Shopping Cart — Ujuzi Shop Mall')
@section('content')
<div class="dashboard-hero">
    <div><div class="dashboard-kicker">Shopping cart</div><h1 class="dashboard-title">Your cart</h1><p class="dashboard-subtitle">Review your items before checkout.</p></div>
    <a href="{{ route('storefront.index') }}" class="btn-outline-dark"><i class="fa-solid fa-arrow-left"></i> Continue Shopping</a>
</div>
@if($cart->isEmpty())
<section class="dashboard-panel" style="text-align:center;padding:70px"><i class="fa-solid fa-cart-shopping" style="font-size:46px;margin-bottom:18px"></i><h2>Your cart is empty</h2><p class="panel-note">Find something you love in the catalogue.</p><a href="{{ route('storefront.index') }}" class="btn-solid" style="display:inline-block;margin-top:18px">Browse Products</a></section>
@else
<form method="POST" action="{{ route('storefront.cart.update') }}">@csrf
<section class="dashboard-panel">
@foreach($cart as $item)<div class="cart-row"><div class="cart-product">@if($item['image'])<img src="{{ asset('storage/'.$item['image']) }}" alt="{{ $item['name'] }}">@else<div class="product-placeholder"><i class="fa-solid fa-box"></i></div>@endif<div><strong>{{ $item['name'] }}</strong><span>UGX {{ number_format($item['price'],0) }} each</span></div></div><input class="cart-qty" type="number" name="quantity[{{ $item['product_id'] }}]" min="1" value="{{ $item['quantity'] }}"><strong>UGX {{ number_format($item['price']*$item['quantity'],0) }}</strong><a href="{{ route('storefront.cart.remove',$item['product_id']) }}" class="cart-remove" onclick="event.preventDefault();document.getElementById('remove-{{ $item['product_id'] }}').submit();"><i class="fa-solid fa-trash"></i></a></div>@endforeach
</section>
<div class="cart-summary dashboard-panel"><div><span>Cart total</span><strong>UGX {{ number_format($total,0) }}</strong></div><div class="cart-actions"><button type="submit" class="btn-outline-dark">Update Cart</button><a href="{{ route('checkout') }}" class="btn-solid">Proceed to Checkout</a></div></div>
</form>
@foreach($cart as $item)<form id="remove-{{ $item['product_id'] }}" method="POST" action="{{ route('storefront.cart.remove',$item['product_id']) }}" style="display:none">@csrf @method('DELETE')</form>@endforeach
@endif
@endsection
