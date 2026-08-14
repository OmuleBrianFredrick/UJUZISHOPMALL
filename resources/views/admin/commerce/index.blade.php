@extends('layouts.dashboard')
@section('title','Commerce Command Centre')
@section('content')
<div class="dashboard-hero"><div><div class="dashboard-kicker">Admin Operations</div><h1 class="dashboard-title">Commerce Command Centre</h1><p class="dashboard-subtitle">One operational view of sales, orders, payments, sellers and inventory.</p></div></div>
<section class="dashboard-grid">
@foreach([
 ['Paid sales','UGX '.number_format($summary['sales'],0)],['Orders',$summary['orders']],['Paid orders',$summary['paid_orders']],['Customers',$summary['customers']],['Products',$summary['products']],['Low stock',$summary['low_stock']],['Sellers',$summary['sellers']],['Pending sellers',$summary['pending_sellers']],['Pending payments',$summary['payments_pending']],['Platform commission','UGX '.number_format($summary['commission'],0)],['Seller credits','UGX '.number_format($summary['seller_credits'],0)]] as $card)
<div class="dashboard-panel"><span>{{ $card[0] }}</span><h2>{{ $card[1] }}</h2></div>
@endforeach
</section>
<section class="dashboard-panel"><h3>Sales by day</h3><div class="table-wrap"><table class="data-table"><thead><tr><th>Date</th><th>Paid orders</th><th>Sales</th></tr></thead><tbody>@forelse($salesByDay as $row)<tr><td>{{ $row->day }}</td><td>{{ $row->orders }}</td><td>UGX {{ number_format($row->total,0) }}</td></tr>@empty<tr><td colspan="3">No paid sales in this period.</td></tr>@endforelse</tbody></table></div></section>
<section class="dashboard-panel"><h3>Recent orders</h3><div class="table-wrap"><table class="data-table"><thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Payment</th><th>Total</th><th>Date</th></tr></thead><tbody>@foreach($recentOrders as $order)<tr><td>{{ $order->order_number }}</td><td>{{ $order->user?->name ?? $order->customer_name }}</td><td>{{ ucfirst($order->status) }}</td><td>{{ ucfirst($order->payment_status) }}</td><td>UGX {{ number_format($order->total,0) }}</td><td>{{ $order->created_at->format('d M Y H:i') }}</td></tr>@endforeach</tbody></table></div></section>
<section class="dashboard-panel"><h3>Payment health</h3><div class="table-wrap"><table class="data-table"><thead><tr><th>Method</th><th>Status</th><th>Count</th></tr></thead><tbody>@foreach($paymentBreakdown as $row)<tr><td>{{ strtoupper($row->method) }}</td><td>{{ ucfirst($row->status) }}</td><td>{{ $row->total }}</td></tr>@endforeach</tbody></table></div></section>
<section class="dashboard-panel"><h3>Inventory watch</h3><div class="table-wrap"><table class="data-table"><thead><tr><th>Product</th><th>SKU</th><th>Stock</th><th>Reorder level</th></tr></thead><tbody>@foreach($topProducts as $product)<tr><td>{{ $product->name }}</td><td>{{ $product->sku }}</td><td>{{ $product->quantity }}</td><td>{{ $product->reorder_level }}</td></tr>@endforeach</tbody></table></div></section>
@endsection
