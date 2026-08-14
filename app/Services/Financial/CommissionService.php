<?php

namespace App\Services\Financial;

use App\Models\FinancialLedger;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommissionService
{
    public function settlePaidOrder(Order $order, Payment $payment): void
    {
        DB::transaction(function () use ($order, $payment) {
            foreach ($order->items()->whereNotNull('seller_id')->get() as $item) {
                if (FinancialLedger::where('order_id', $order->id)->where('payment_id', $payment->id)->where('type', 'sale')->where('seller_id', $item->seller_id)->exists()) {
                    continue;
                }
                $gross = (float) $item->line_total;
                $rate = (float) config('commerce.commission_rate', env('PLATFORM_COMMISSION_RATE', 10));
                $commission = round($gross * ($rate / 100), 2);
                $net = round($gross - $commission, 2);
                $this->entry($item->seller_id, $order->id, $payment->id, 'sale', 'credit', $net, 'Seller sale earnings', ['gross' => $gross, 'commission' => $commission, 'rate' => $rate]);
                $this->entry($item->seller_id, $order->id, $payment->id, 'commission', 'debit', $commission, 'Platform commission', ['gross' => $gross, 'rate' => $rate]);
            }
        });
    }

    private function entry(int $sellerId, int $orderId, int $paymentId, string $type, string $direction, float $amount, string $description, array $metadata): void
    {
        FinancialLedger::create([
            'seller_id' => $sellerId, 'order_id' => $orderId, 'payment_id' => $paymentId,
            'type' => $type, 'direction' => $direction, 'amount' => $amount, 'currency' => 'UGX',
            'reference' => 'UJM-' . Str::upper($type) . '-' . now()->format('YmdHisv') . '-' . Str::upper(Str::random(8)),
            'description' => $description, 'metadata' => $metadata,
        ]);
    }
}
