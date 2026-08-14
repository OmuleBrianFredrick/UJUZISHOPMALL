<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MtnMomoGateway implements PaymentGateway
{
    public function initiate(Order $order, Payment $payment, string $phone): array
    {
        $base = rtrim(config('services.mtn_momo.base_url'), '/');
        $subscriptionKey = config('services.mtn_momo.subscription_key');
        $apiUser = config('services.mtn_momo.api_user');
        $apiKey = config('services.mtn_momo.api_key');

        if (! $base || ! $subscriptionKey || ! $apiUser || ! $apiKey) {
            throw new RuntimeException('MTN MoMo is not configured.');
        }

        $token = Http::asForm()->withHeaders([
            'Ocp-Apim-Subscription-Key' => $subscriptionKey,
        ])->withBasicAuth($apiUser, $apiKey)
            ->post($base.'/collection/token')
            ->throw()
            ->json('access_token');

        $callback = rtrim(config('app.url'), '/').'/payments/callback/mtn';

        $referenceId = (string) Str::uuid();
        $response = Http::withToken($token)->withHeaders([
            'X-Reference-Id' => $referenceId,
            'X-Target-Environment' => config('services.mtn_momo.target_environment', 'sandbox'),
            'Ocp-Apim-Subscription-Key' => $subscriptionKey,
            'X-Callback-Url' => $callback,
        ])->post($base.'/collection/v1_0/requesttopay', [
            'amount' => (string) $payment->amount,
            'currency' => $payment->currency,
            'externalId' => $order->order_number,
            'payer' => [
                'partyIdType' => 'MSISDN',
                'partyId' => $this->normalizePhone($phone),
            ],
            'payerMessage' => 'Ujuzi Shop Mall order '.$order->order_number,
            'payeeNote' => 'Ujuzi Shop Mall payment',
        ]);

        if ($response->status() !== 202) {
            throw new RuntimeException('MTN MoMo rejected the payment request.');
        }

        return [
            'status' => 'processing',
            'provider_reference' => $referenceId,
            'provider_response' => $response->json(),
        ];
    }

    public function handleCallback(array $payload): array
    {
        $status = strtolower((string) ($payload['status'] ?? ''));
        $mapped = match ($status) {
            'successful' => 'successful',
            'failed', 'rejected', 'expired' => 'failed',
            default => 'processing',
        };

        return [
            'status' => $mapped,
            'provider_reference' => $payload['referenceId'] ?? $payload['financialTransactionId'] ?? null,
            'failure_reason' => $payload['reason'] ?? null,
            'provider_response' => $payload,
        ];
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        return str_starts_with($digits, '0') ? '256'.substr($digits, 1) : $digits;
    }
}
