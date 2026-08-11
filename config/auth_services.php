<?php

return [
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/auth/google/callback'),
    ],

    'otp' => [
        'provider' => env('OTP_PROVIDER', 'africastalking'),
        'api_key' => env('AFRICASTALKING_API_KEY'),
        'username' => env('AFRICASTALKING_USERNAME'),
        'sender_id' => env('AFRICASTALKING_SENDER_ID'),
        'sandbox' => env('AFRICASTALKING_SANDBOX', false),
        'expiry_minutes' => 5,
    ],
];
