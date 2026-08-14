@extends('layouts.dashboard')
@section('title', 'Ujuzi Shop Mall — Shop')
@section('content')
<div class="storefront-hero">
    <div>
        <div class="dashboard-kicker">Ujuzi Shop Mall</div>
        <h1 class="dashboard-title">Shop the mall online.</h1>
        <p class="dashboard-subtitle">Browse available products, compare prices and build your cart.</p>
    </div>
    <a href="{{ route('storefront.cart') }}" class="btn-solid"><i class="fa-solid fa-cart-shopping"></i> Cart ({{ collect(session('cart', []))->sum('quantity') }})</a>
</div>

<section class="storefront-toolbar dashboard-panel">
    <form method="GET" action="{{ route('storefront.index') }}" class="storefront-search">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products, SKU or category…">
        <select name="category">
            <option value="">All categories</option>
            @foreach($categories as $category)
                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
            @endforeach
        </select>
        <select name="sort">
            <option value="latest" @selected(request('sort', 'latest') === 'latest')>Newest</option>
            <option value="name" @selected(request('sort') === 'name')>Name A–Z</option>
            <option value="price_low" @selected(request('sort') === 'price_low')>Price: Low to High</option>
            <option value="price_high" @selected(request('sort') === 'price_high')>Price: High to Low</option>
        </select>
        <button class="btn-solid" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
    </form>
</section>

<div class="storefront-grid">
    @forelse($products as $product)
        <article class="shop-card">
            <a href="{{ route('storefront.show', $product) }}" class="shop-image-wrap">
                @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
                @else
                    <div class="shop-image-placeholder"><i class="fa-solid fa-box-open"></i></div>
                @endif
            </a>
            <div class="shop-card-body">
                <div class="shop-category">{{ $product->category ?: 'General' }}</div>
                <h2><a href="{{ route('storefront.show', $product) }}">{{ $product->name }}</a></h2>
                <p>{{ $product->description ? Str::limit($product->description, 90) : 'Quality product available at Ujuzi Shop Mall.' }}</p>
                <div class="shop-card-footer">
                    <strong>UGX {{ number_format($product->price, 0) }}</strong>
                    <form method="POST" action="{{ route('storefront.cart.add', $product) }}">
                        @csrf
                        <button class="btn-solid btn-sm" type="submit"><i class="fa-solid fa-cart-plus"></i> Add</button>
                    </form>
                </div>
            </div>
        </article>
    @empty
        <div class="dashboard-panel" style="grid-column:1/-1;text-align:center;padding:60px;">
            <i class="fa-solid fa-box-open" style="font-size:42px;margin-bottom:16px;"></i>
            <h2>No products found</h2>
            <p class="panel-note">Try another search or category.</p>
        </div>
    @endforelse
</div>

<div class="storefront-pagination">{{ $products->links() }}</div>
@endsection
