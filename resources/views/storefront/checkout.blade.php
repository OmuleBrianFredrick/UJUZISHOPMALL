@extends('layouts.dashboard')
@section('title', 'Checkout — Ujuzi Shop Mall')
@section('content')
<div class="dashboard-hero"><div><div class="dashboard-kicker">Secure checkout</div><h1 class="dashboard-title">Complete your order</h1><p class="dashboard-subtitle">Your contact and delivery details are captured so your order remains correctly linked to you.</p></div></div>
@if($errors->any())<div class="dashboard-panel" style="border-color:#fecaca;color:#b91c1c;margin-bottom:16px;"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="checkout-grid">
    <form method="POST" action="{{ route('checkout.store') }}" class="dashboard-panel checkout-form">
        @csrf
        <h2 class="panel-title">Customer & delivery details</h2>
        <label>Full name<input name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" autocomplete="name" required></label>
        <label>Email address<input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()->email) }}" autocomplete="email" required></label>
        <label>Phone number<input name="customer_phone" value="{{ old('customer_phone') }}" autocomplete="tel" placeholder="07XXXXXXXX" required></label>
        <label>Delivery address<textarea name="delivery_address" rows="4" autocomplete="street-address" placeholder="House/building, area, town/city" required>{{ old('delivery_address') }}</textarea></label>
        <label>Order notes <span class="panel-note">Optional</span><textarea name="notes" rows="3" placeholder="Any special delivery instructions">{{ old('notes') }}</textarea></label>
        <div style="margin-top:16px;padding:16px;border:1px solid #e5e7eb;border-radius:12px;">
            <h3 style="margin-top:0;">🎟️ Promotion code</h3>
            <p class="panel-note">Apply a valid promotion before placing your order.</p>
            <input name="promotion_code" value="{{ old('promotion_code') }}" maxlength="50" placeholder="e.g. WELCOME10" style="text-transform:uppercase;">
        </div>
        <div style="margin-top:16px;padding:16px;border:1px solid #e5e7eb;border-radius:12px;">
            <h3 style="margin-top:0;">🏆 Loyalty points</h3>
            <p class="panel-note">Redeem points at 10 UGX per point. Your available balance is checked securely at checkout.</p>
            <input type="number" name="loyalty_points" value="{{ old('loyalty_points', 0) }}" min="0" step="1" placeholder="Points to redeem">
        </div>
        <button class="btn-solid" type="submit" style="margin-top:16px;"><i class="fa-solid fa-check"></i> Place Order</button>
    </form>
    <section class="dashboard-panel">
        <div class="panel-head"><div><h2 class="panel-title">Order summary</h2><p class="panel-note">{{ $cart->sum('quantity') }} item(s)</p></div></div>
        @foreach($cart as $item)<div class="checkout-item"><div><strong>{{ $item['name'] }}</strong><span>{{ $item['quantity'] }} × UGX {{ number_format($item['price'],0) }}</span></div><strong>UGX {{ number_format($item['price']*$item['quantity'],0) }}</strong></div>@endforeach
        <div class="checkout-total"><span>Subtotal</span><strong>UGX {{ number_format($subtotal,0) }}</strong></div>
        <div class="checkout-total"><span>Promotion</span><strong>Validated at checkout</strong></div>
        <div class="checkout-total"><span>Loyalty</span><strong>Validated at checkout</strong></div>
        <div class="checkout-total"><span>Delivery</span><strong>{{ $deliveryFee ? 'UGX '.number_format($deliveryFee,0) : 'FREE' }}</strong></div>
        <div class="checkout-total grand"><span>Base total</span><strong>UGX {{ number_format($subtotal+$deliveryFee,0) }}</strong></div>
        <p class="panel-note" style="margin-top:12px;">Final discount and loyalty deductions are calculated on the server during the transaction.</p>
    </section>
</div>
@endsection
