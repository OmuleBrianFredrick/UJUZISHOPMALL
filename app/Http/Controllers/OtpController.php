<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    public function requestOtp(Request $request, OtpService $otpService)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $phone = $this->normalizeUgandaPhone($validated['phone']);
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'phone' => 'No account is registered with this phone number.',
            ]);
        }

        $recentCount = Otp::where('phone', $phone)
            ->where('purpose', 'login')
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();

        if ($recentCount >= 3) {
            throw ValidationException::withMessages([
                'phone' => 'Too many OTP requests. Please wait a few minutes before trying again.',
            ]);
        }

        $code = (string) random_int(100000, 999999);

        Otp::where('phone', $phone)
            ->where('purpose', 'login')
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        Otp::create([
            'phone' => $phone,
            'code' => Hash::make($code),
            'purpose' => 'login',
            'expires_at' => now()->addMinutes(5),
        ]);

        $otpService->send($phone, $code);

        return back()->with('otp_sent', 'A verification code has been sent to your phone.');
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'code' => ['required', 'digits:6'],
        ]);

        $phone = $this->normalizeUgandaPhone($validated['phone']);
        $otp = Otp::where('phone', $phone)
            ->where('purpose', 'login')
            ->whereNull('consumed_at')
            ->latest()
            ->first();

        if (!$otp || $otp->expires_at->isPast() || !Hash::check($validated['code'], $otp->code)) {
            throw ValidationException::withMessages([
                'code' => 'The verification code is invalid or has expired.',
            ]);
        }

        $user = User::where('phone', $phone)->firstOrFail();
        $otp->update(['consumed_at' => now()]);

        auth()->login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('products.index'));
    }

    private function normalizeUgandaPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', trim($phone));

        if (Str::startsWith($phone, '00')) {
            $phone = '+' . substr($phone, 2);
        }
        if (Str::startsWith($phone, '0')) {
            $phone = '+256' . substr($phone, 1);
        }
        if (Str::startsWith($phone, '256')) {
            $phone = '+' . $phone;
        }

        return $phone;
    }
}
