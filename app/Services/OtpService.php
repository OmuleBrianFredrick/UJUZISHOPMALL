<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OtpService
{
    public function send(string $phone, string $code): void
    {
        $message = "Your UjuziShopMall verification code is {$code}. It expires in 5 minutes.";
        $config = config('auth_services.otp');

        if ($config['provider'] === 'log') {
            Log::info('UjuziShopMall OTP', ['phone' => $phone, 'code' => $code]);
            return;
        }

        if (empty($config['api_key']) || empty($config['username'])) {
            throw new RuntimeException('OTP SMS is not configured. Add Africa\'s Talking credentials to the environment.');
        }

        $endpoint = $config['sandbox']
            ? 'https://api.sandbox.africastalking.com/version1/messaging'
            : 'https://api.africastalking.com/version1/messaging';

        $payload = [
            'username' => $config['username'],
            'to' => $phone,
            'message' => $message,
        ];

        if (!empty($config['sender_id'])) {
            $payload['from'] = $config['sender_id'];
        }

        $response = Http::asForm()
            ->withHeaders(['apiKey' => $config['api_key'], 'Accept' => 'application/json'])
            ->timeout(15)
            ->post($endpoint, $payload);

        if ($response->failed()) {
            throw new RuntimeException('The OTP SMS provider rejected the request.');
        }
    }
}
