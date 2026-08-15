@extends('layouts.dashboard')
@section('title', 'My Wishlist — Ujuzi Shop Mall')
@section('content')
<div class="dashboard-panel">
    <div class="panel-head">
        <div><h1 class="panel-title">My Wishlist</h1><p class="panel-note">Products you saved for later.</p></div>
        <a class="btn-outline-dark btn-sm" href="{{ route('storefront.index') }}"><i class="fa-solid fa-arrow-left"></i> Continue shopping</a>
    </div>
    <div class="storefront-grid">
        @forelse($items as $item)
            @php($product = $item->product)
            @if($product)
            <article class="shop-card">
                <a href="{{ route('storefront.show', $product) }}" class="shop-image-wrap">
                    @if($product->image)<img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">@else<div class="shop-image-placeholder"><i class="fa-solid fa-box-open"></i></div>@endif
                </a>
                <div class="shop-card-body">
                    <div class="shop-category">{{ $product->category ?: 'General' }}</div>
                    <h2><a href="{{ route('storefront.show', $product) }}">{{ $product->name }}</a></h2>
                    <div class="shop-card-footer"><strong>UGX {{ number_format($product->price, 0) }}</strong></div>
                    <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap;">
                        @if($product->quantity > 0)
                        <form method="POST" action="{{ route('storefront.cart.add', $product) }}">@csrf<input type="hidden" name="quantity" value="1"><button class="btn-solid" type="submit"><i class="fa-solid fa-cart-plus"></i> Add to cart</button></form>
                        @else
                        <span class="alert-error">Out of stock</span>
                        @endif
                        <form method="POST" action="{{ route('wishlist.toggle', $product) }}">@csrf<button class="btn-outline" type="submit"><i class="fa-solid fa-heart-crack"></i> Remove</button></form>
                    </div>
                </div>
            </article>
            @endif
        @empty
            <div class="empty-state"><i class="fa-regular fa-heart"></i><h2>Your wishlist is empty</h2><p>Save products here and come back when you're ready to buy.</p><a class="btn-solid" href="{{ route('storefront.index') }}">Browse products</a></div>
        @endforelse
    </div>
</div>
@endsection
