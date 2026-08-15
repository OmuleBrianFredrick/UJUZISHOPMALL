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
        @php($rating = $product->averageRating())
        <div aria-label="{{ $rating }} out of 5 stars" style="margin:8px 0 12px;">
            @for($star = 1; $star <= 5; $star++)
                <span>{{ $star <= round($rating) ? '★' : '☆' }}</span>
            @endfor
            <small> {{ number_format($rating, 1) }}/5 · {{ $product->approvedReviews()->count() }} reviews</small>
        </div>
        <div class="shop-price">UGX {{ number_format($product->price, 0) }}</div>
        <p>{{ $product->description ?: 'Quality product available at Ujuzi Shop Mall.' }}</p>
        <div class="shop-stock"><i class="fa-solid fa-circle-check"></i> {{ number_format($product->quantity) }} available</div>
        <form method="POST" action="{{ route('storefront.cart.add', $product) }}" class="add-cart-form">
            @csrf
            <label>Quantity <input type="number" name="quantity" value="1" min="1" max="{{ $product->quantity }}"></label>
            <button class="btn-solid" type="submit"><i class="fa-solid fa-cart-plus"></i> Add to Cart</button>
        </form>
        @auth
        <form method="POST" action="{{ route('wishlist.toggle', $product) }}" style="margin-top:10px;">@csrf<button class="btn-outline" type="submit"><i class="fa-solid fa-heart"></i> Wishlist</button></form>
        @endauth
    </div>
</div>

<section class="dashboard-panel" style="margin-top:28px;">
    <div class="panel-head"><div><h2 class="panel-title">Customer reviews</h2><p class="panel-note">Reviews from customers who received this product</p></div></div>
    @forelse($product->approvedReviews()->with('user')->latest()->get() as $review)
        <article style="padding:14px 0;border-bottom:1px solid #eee;">
            <strong>{{ $review->user->name ?? 'Customer' }}</strong>
            <span style="margin-left:10px;">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
            @if($review->verified_purchase)<small> · Verified purchase</small>@endif
            <p>{{ $review->body }}</p>
        </article>
    @empty
        <p>No approved reviews yet. Be the first verified customer to share your experience.</p>
    @endforelse

    @auth
    <form method="POST" action="{{ route('reviews.store', $product) }}" style="margin-top:20px;">
        @csrf
        <h3>Leave a review</h3>
        <label>Rating
            <select name="rating" required>
                @for($i=5;$i>=1;$i--)<option value="{{ $i }}">{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>@endfor
            </select>
        </label>
        <label style="display:block;margin-top:10px;">Your review
            <textarea name="body" rows="4" minlength="5" maxlength="2000" required style="width:100%;"></textarea>
        </label>
        <button class="btn-solid" type="submit" style="margin-top:10px;">Submit verified review</button>
    </form>
    @endauth
</section>

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
