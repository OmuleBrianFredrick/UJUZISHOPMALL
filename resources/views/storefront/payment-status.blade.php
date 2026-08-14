@extends('layouts.dashboard')
@section('title', 'Payment Status — '.$order->order_number)
@section('content')
<div class="dashboard-hero"><div><div class="dashboard-kicker">Payment status</div><h1 class="dashboard-title">{{ $order->order_number }}</h1><p class="dashboard-subtitle">Track the payment for your order.</p></div></div>
<section class="dashboard-panel payment-panel" style="text-align:center;max-width:720px;margin:auto">
    <div style="font-size:46px;margin-bottom:12px"><i class="fa-solid {{ $payment->status === 'successful' ? 'fa-circle-check' : ($payment->status === 'failed' ? 'fa-circle-xmark' : 'fa-mobile-screen-button') }}"></i></div>
    <h2 style="text-transform:capitalize">{{ $payment->status }}</h2>
    <p class="panel-note">{{ $payment->provider }} · {{ $payment->method }}</p>
    <div class="payment-total" style="justify-content:center;margin:24px 0"><span>Amount</span><strong>UGX {{ number_format($payment->amount,0) }}</strong></div>
    @if($payment->status === 'processing')<p>Payment request created. Complete the mobile-money prompt, then return here to verify the transaction.</p>@elseif($payment->status === 'successful')<p>Your payment has been confirmed.</p>@elseif($payment->status === 'failed')<p>{{ $payment->failure_reason ?: 'The payment could not be completed.' }}</p>@else<p>Your payment is waiting to be processed.</p>@endif
    <div style="margin-top:24px"><a href="{{ route('orders.show',$order) }}" class="btn-solid">Back to Order</a></div>
</section>
@endsection
