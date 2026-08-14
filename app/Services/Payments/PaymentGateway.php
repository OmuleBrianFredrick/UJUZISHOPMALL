<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;

interface PaymentGateway
{
    /**
     * Start a provider payment request. Concrete gateways should return
     * provider data without exposing credentials or secrets to callers.
     */
    public function initiate(Order $order, Payment $payment, string $phone): array;

    /**
     * Normalize a provider callback into the platform payment state model.
     */
    public function handleCallback(array $payload): array;
}
