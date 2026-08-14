@extends('layouts.dashboard')
@section('title','Seller Orders')
@section('content')
<div class="dashboard-hero"><div><div class="dashboard-kicker">Seller Centre</div><h1 class="dashboard-title">My Orders</h1><p class="dashboard-subtitle">Orders containing your products.</p></div></div>
<section class="dashboard-panel"><div class="table-wrap"><table class="data-table"><thead><tr><th>Order</th><th>Date</th><th>Status</th><th>Items</th><th>Action</th></tr></thead><tbody>@forelse($orders as $order)<tr><td>{{ $order->order_number }}</td><td>{{ $order->created_at->format('d M Y') }}</td><td>{{ ucfirst($order->status) }}</td><td>{{ $order->items->sum('quantity') }}</td><td><a href="{{ route('seller.orders.show',$order) }}">Manage</a></td></tr>@empty<tr><td colspan="5">No seller orders yet.</td></tr>@endforelse</tbody></table></div>{{ $orders->links() }}</section>
@endsection
