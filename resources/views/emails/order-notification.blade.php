<x-mail::message>
# {{ match($type) {
    'order_confirmation' => 'Order received',
    'payment_success' => 'Payment confirmed',
    'payment_failed' => 'Payment needs attention',
    default => 'Order update',
} }}

Hello {{ $order->customer_name }},

@if($type === 'order_confirmation')
We have received your order **{{ $order->order_number }}** and recorded your delivery details.
@elseif($type === 'payment_success')
Your payment for order **{{ $order->order_number }}** has been confirmed. We will continue processing your order.
@elseif($type === 'payment_failed')
We could not complete the payment for order **{{ $order->order_number }}**. Please return to the payment page and try again.
@endif

**Order total:** UGX {{ number_format($order->total, 0) }}

**Delivery address:** {{ $order->delivery_address }}

You can sign in to Ujuzi Shop Mall at any time to track your order.

Thanks,<br>
{{ config('app.name', 'Ujuzi Shop Mall') }}
</x-mail::message>
