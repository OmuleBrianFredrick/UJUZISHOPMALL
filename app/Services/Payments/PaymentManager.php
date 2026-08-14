<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentManager
{
    public function gateway(string $method): PaymentGateway
    {
        return match ($method) {
            'mtn_momo' => app(MtnMomoGateway::class),
            default => throw new RuntimeException('Payment provider is not configured for this method.'),
        };
    }

    public function initiate(Order $order, Payment $payment, string $phone): Payment
    {
        $result = $this->gateway($payment->method)->initiate($order, $payment, $phone);

        return DB::transaction(function () use ($payment, $result) {
            $payment->update([
                'status' => $result['status'],
                'provider_reference' => $result['provider_reference'] ?? null,
                'provider_response' => $result['provider_response'] ?? null,
                'failure_reason' => $result['failure_reason'] ?? null,
            ]);

            return $payment->fresh();
        });
    }
}
