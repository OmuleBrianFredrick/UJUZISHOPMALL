@extends('layouts.dashboard')
@section('title', $product->name . ' — Ujuzi Shop Mall')
@section('content')
<div class="shop-detail dashboard-panel">
    <div class="shop-detail-image">
        @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
        @else
            <div class="shop-image-placeholder"><i class="fa-solid fa-box-open"></i></div>
        @endif
    </div>
    <div class="shop-detail-copy">
        <div class="shop-category">{{ $product->category ?: 'General' }}</div>
        <h1>{{ $product->name }}</h1>
        <div class="shop-price">UGX {{ number_format($product->price, 0) }}</div>
        <p>{{ $product->description ?: 'Quality product available at Ujuzi Shop Mall.' }}</p>
        <div class="shop-stock"><i class="fa-solid fa-circle-check"></i> {{ number_format($product->quantity) }} available</div>
        <form method="POST" action="{{ route('storefront.cart.add', $product) }}" class="add-cart-form">
            @csrf
            <label>Quantity <input type="number" name="quantity" value="1" min="1" max="{{ $product->quantity }}"></label>
            <button class="btn-solid" type="submit"><i class="fa-solid fa-cart-plus"></i> Add to Cart</button>
        </form>
    </div>
</div>

@if($related->isNotEmpty())
<section style="margin-top:28px;">
    <div class="panel-head"><div><h2 class="panel-title">You may also like</h2><p class="panel-note">More products from the mall</p></div></div>
    <div class="storefront-grid">
        @foreach($related as $item)
            <article class="shop-card">
                <a href="{{ route('storefront.show', $item) }}" class="shop-image-wrap">
                    @if($item->image)<img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}">@else<div class="shop-image-placeholder"><i class="fa-solid fa-box"></i></div>@endif
                </a>
                <div class="shop-card-body"><div class="shop-category">{{ $item->category ?: 'General' }}</div><h2><a href="{{ route('storefront.show', $item) }}">{{ $item->name }}</a></h2><div class="shop-card-footer"><strong>UGX {{ number_format($item->price, 0) }}</strong></div></div>
            </article>
        @endforeach
    </div>
</section>
@endif
@endsection
