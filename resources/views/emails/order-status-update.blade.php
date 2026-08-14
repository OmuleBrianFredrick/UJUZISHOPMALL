<x-mail::message>
# Order update

Hello {{ $order->customer_name }},

Your order **{{ $order->order_number }}** is now **{{ ucfirst($status) }}**.

@if($status === 'shipped')
Your order has been dispatched and is on its way to the delivery address you provided at checkout.
@elseif($status === 'delivered')
Your order has been marked as delivered. Thank you for shopping with Ujuzi Shop Mall.
@elseif($status === 'ready')
Your order is ready for dispatch. We will notify you when it is shipped.
@else
We will continue to update you as your order moves through fulfilment.
@endif

**Delivery address:** {{ $order->delivery_address }}

**Order total:** UGX {{ number_format($order->total, 0) }}

If you did not expect this update, please contact the platform support team.

Thanks,<br>
{{ config('app.name', 'Ujuzi Shop Mall') }}
</x-mail::message>
