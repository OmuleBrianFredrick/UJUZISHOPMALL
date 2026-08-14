@extends('layouts.dashboard')
@section('title',$profile->store_name.' — Seller Dashboard')
@section('content')
<div class="dashboard-hero"><div><div class="dashboard-kicker">Seller centre</div><h1 class="dashboard-title">{{ $profile->store_name }}</h1><p class="dashboard-subtitle">{{ $profile->location ?: 'Ujuzi Shop Mall seller' }}</p></div><a href="{{ route('seller.apply') }}" class="btn-outline-dark">Store Profile</a></div>
<div class="dashboard-grid"><div class="dashboard-stat stat-blue"><div class="stat-top">Products</div><div class="stat-value">{{ $products->total() }}</div><div class="stat-meta">Your catalogue</div></div><div class="dashboard-stat stat-green"><div class="stat-top">Store status</div><div class="stat-value">Approved</div><div class="stat-meta">Visible to customers</div></div></div>
<section class="dashboard-panel"><div class="panel-head"><div><h2 class="panel-title">Your products</h2><p class="panel-note">Only products owned by your seller account appear here.</p></div></div>
<table class="modern-table"><thead><tr><th>Product</th><th>SKU</th><th>Price</th><th>Stock</th></tr></thead><tbody>
@forelse($products as $product)<tr><td><strong>{{ $product->name }}</strong></td><td>{{ $product->sku }}</td><td>UGX {{ number_format($product->price,0) }}</td><td>{{ $product->quantity }}</td></tr>@empty<tr><td colspan="4">No seller products yet.</td></tr>@endforelse
</tbody></table></section>
<div class="storefront-pagination">{{ $products->links() }}</div>
@endsection
