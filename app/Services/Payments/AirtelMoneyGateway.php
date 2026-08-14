<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class AirtelMoneyGateway implements PaymentGateway
{
    public function initiate(Order $order, Payment $payment, string $phone): array
    {
        $base = rtrim((string) config('services.airtel_money.base_url'), '/');
        $clientId = config('services.airtel_money.client_id');
        $clientSecret = config('services.airtel_money.client_secret');
        $country = config('services.airtel_money.country', 'UG');
        $currency = config('services.airtel_money.currency', 'UGX');

        if (! $base || ! $clientId || ! $clientSecret) {
            throw new RuntimeException('Airtel Money is not configured.');
        }

        $tokenResponse = Http::asForm()->post($base.'/auth/oauth2/token', [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
        ]);
        $tokenResponse->throw();
        $token = $tokenResponse->json('access_token');
        if (! $token) throw new RuntimeException('Airtel Money authentication did not return an access token.');

        $reference = (string) Str::uuid();
        $response = Http::withToken($token)->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-Country' => $country,
            'X-Currency' => $currency,
        ])->post($base.'/merchant/v1/payments/', [
            'reference' => $reference,
            'subscriber' => [
                'country' => $country,
                'currency' => $currency,
                'msisdn' => $this->normalizePhone($phone),
            ],
            'transaction' => [
                'amount' => (string) $payment->amount,
                'country' => $country,
                'currency' => $currency,
                'id' => $order->order_number,
            ],
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Airtel Money rejected the payment request.');
        }

        $status = strtolower((string) ($response->json('data.transaction.status') ?? $response->json('status') ?? 'pending'));
        return [
            'status' => in_array($status, ['success', 'successful', 'completed'], true) ? 'successful' : 'processing',
            'provider_reference' => $response->json('data.transaction.id') ?? $response->json('transaction.id') ?? $reference,
            'provider_response' => $response->json(),
        ];
    }

    public function handleCallback(array $payload): array
    {
        $status = strtolower((string) ($payload['transaction']['status'] ?? $payload['status'] ?? ''));
        $mapped = match ($status) {
            'success', 'successful', 'completed' => 'successful',
            'failed', 'rejected', 'declined', 'cancelled', 'expired' => 'failed',
            default => 'processing',
        };
        return [
            'status' => $mapped,
            'provider_reference' => $payload['transaction']['id'] ?? $payload['transaction']['reference'] ?? $payload['reference'] ?? null,
            'failure_reason' => $payload['transaction']['message'] ?? $payload['message'] ?? null,
            'provider_response' => $payload,
        ];
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        return str_starts_with($digits, '0') ? '256'.substr($digits, 1) : $digits;
    }
}
