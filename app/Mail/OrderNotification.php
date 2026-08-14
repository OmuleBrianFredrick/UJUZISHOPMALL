<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public string $type) {}

    public function build(): self
    {
        $subjects = [
            'order_confirmation' => 'Order confirmation',
            'payment_success' => 'Payment confirmed',
            'payment_failed' => 'Payment update',
        ];
        return $this->subject('Ujuzi Shop Mall — '.($subjects[$this->type] ?? 'Order update'))
            ->markdown('emails.order-notification');
    }
}
