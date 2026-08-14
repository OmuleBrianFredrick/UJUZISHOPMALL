<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdate extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public string $status) {}

    public function build(): self
    {
        $labels = [
            'processing' => 'Your order is being processed',
            'ready' => 'Your order is ready for dispatch',
            'shipped' => 'Your order has been shipped',
            'delivered' => 'Your order has been delivered',
        ];

        return $this->subject('Ujuzi Shop Mall — ' . ($labels[$this->status] ?? 'Order update'))
            ->markdown('emails.order-status-update');
    }

    public function failed(\Throwable $exception): void
    {
        report($exception);
    }
}
