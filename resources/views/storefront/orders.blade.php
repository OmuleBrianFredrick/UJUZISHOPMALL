@extends('layouts.dashboard')
@section('title', 'My Orders — Ujuzi Shop Mall')
@section('content')
<div class="dashboard-hero"><div><div class="dashboard-kicker">Customer account</div><h1 class="dashboard-title">My Orders</h1><p class="dashboard-subtitle">Track your purchases and order status.</p></div><a class="btn-solid" href="{{ route('storefront.index') }}">Continue Shopping</a></div>
<section class="dashboard-panel">
@forelse($orders as $order)
<a class="order-card" href="{{ route('orders.show',$order) }}"><div><strong>{{ $order->order_number }}</strong><span>{{ $order->created_at->format('d M Y, H:i') }} · {{ $order->items->sum('quantity') }} item(s)</span></div><div><span class="order-status order-status-{{ $order->status }}">{{ ucfirst($order->status) }}</span><strong>UGX {{ number_format($order->total,0) }}</strong></div></a>
@empty
<div style="text-align:center;padding:50px"><h2>No orders yet</h2><p class="panel-note">Your completed purchases will appear here.</p></div>
@endforelse
</section>
<div class="storefront-pagination">{{ $orders->links() }}</div>
@endsection
