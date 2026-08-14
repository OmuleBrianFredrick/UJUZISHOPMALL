@extends('layouts.dashboard')
@section('title', 'Pay Order '.$order->order_number)
@section('content')
<div class="dashboard-hero"><div><div class="dashboard-kicker">Secure payment</div><h1 class="dashboard-title">Pay for {{ $order->order_number }}</h1><p class="dashboard-subtitle">Choose a Uganda mobile-money method.</p></div><a href="{{ route('orders.show',$order) }}" class="btn-outline-dark">View Order</a></div>
<section class="dashboard-panel payment-panel">
    <div class="payment-total"><span>Amount to pay</span><strong>UGX {{ number_format($order->total,0) }}</strong></div>
    <form method="POST" action="{{ route('payments.store',$order) }}" class="payment-form">@csrf
        <label>Payment method
            <select name="method" required><option value="mtn_momo">MTN Mobile Money</option><option value="airtel_money">Airtel Money</option></select>
        </label>
        <label>Mobile-money phone number
            <input type="tel" name="phone" value="{{ old('phone',$order->customer_phone) }}" placeholder="07XXXXXXXX" required>
        </label>
        <button class="btn-solid" type="submit"><i class="fa-solid fa-mobile-screen-button"></i> Start Payment</button>
    </form>
    <p class="panel-note" style="margin-top:16px"><i class="fa-solid fa-shield-halved"></i> Payment credentials remain outside the application source code.</p>
</section>
@endsection
