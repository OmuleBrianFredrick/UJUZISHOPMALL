<?php

namespace App\Services;

use App\Mail\OrderNotification;
use App\Mail\OrderStatusUpdate;
use App\Models\NotificationLog;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function order(Order $order, string $type): void
    {
        $this->send($order, $type, fn (int $logId) => new OrderNotification($order->fresh(), $type, $logId));
    }

    public function status(Order $order, string $status): void
    {
        $this->send($order, 'order_status_'.$status, fn (int $logId) => new OrderStatusUpdate($order->fresh(), $status, $logId));
    }

    private function send(Order $order, string $type, callable $mailable): void
    {
        $recipient = $order->customer_email;
        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) return;

        $log = NotificationLog::create([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'channel' => 'email',
            'type' => $type,
            'recipient' => $recipient,
            'status' => 'queued',
        ]);

        try {
            Mail::to($recipient)->queue($mailable($log->id));
        } catch (\Throwable $e) {
            $log->update(['status' => 'failed', 'failure_reason' => $e->getMessage()]);
            report($e);
        }
    }
}
