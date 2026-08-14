<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public string $status)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Order ' . $this->order->order_number . ' — ' . ucfirst($this->status));
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.order-status-update');
    }

    public function attachments(): array
    {
        return [];
    }
}
