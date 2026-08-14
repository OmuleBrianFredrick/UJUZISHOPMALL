<?php

namespace App\Http\Controllers;

use App\Mail\StaffLoginOtp;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    public function challenge(Request $request)
    {
        abort_unless($request->session()->has('pending_staff_login_user_id'), 403);
        return view('auth.staff-otp');
    }

    public function requestOtp(Request $request)
    {
        $userId = $request->session()->get('pending_staff_login_user_id');
        $user = $userId ? User::find($userId) : null;

        if (!$user || !$this->isPrivilegedStaff($user)) {
            $request->session()->forget(['pending_staff_login_user_id', 'pending_staff_login_remember']);
            throw ValidationException::withMessages(['email' => 'The staff login session is no longer valid. Please sign in again.']);
        }

        $recentCount = Otp::where('user_id', $user->id)->where('purpose', 'staff_login')->where('created_at', '>=', now()->subMinutes(10))->count();
        if ($recentCount >= 3) {
            throw ValidationException::withMessages(['code' => 'Too many verification codes were requested. Please wait a few minutes.']);
        }

        return $this->issueOtp($request, $user);
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => ['required', 'digits:6']]);
        $userId = $request->session()->get('pending_staff_login_user_id');
        $user = $userId ? User::find($userId) : null;
        $otp = $user ? Otp::where('user_id', $user->id)->where('purpose', 'staff_login')->whereNull('consumed_at')->latest()->first() : null;

        if (!$user || !$this->isPrivilegedStaff($user) || !$otp || $otp->expires_at->isPast() || !Hash::check($request->string('code')->toString(), $otp->code)) {
            throw ValidationException::withMessages(['code' => 'The verification code is invalid or has expired.']);
        }

        $otp->update(['consumed_at' => now()]);
        $remember = $request->session()->pull('pending_staff_login_remember', false);
        $request->session()->forget('pending_staff_login_user_id');
        auth()->login($user, (bool) $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('products.index'));
    }

    private function issueOtp(Request $request, User $user)
    {
        $code = (string) random_int(100000, 999999);
        Otp::where('user_id', $user->id)->where('purpose', 'staff_login')->whereNull('consumed_at')->update(['consumed_at' => now()]);
        Otp::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'code' => Hash::make($code),
            'purpose' => 'staff_login',
            'expires_at' => now()->addMinutes(5),
        ]);
        Mail::to($user->email)->send(new StaffLoginOtp($code, $user->name));
        return redirect()->route('login.otp.challenge')->with('otp_sent', 'A verification code has been sent to your registered email address.');
    }

    private function isPrivilegedStaff(User $user): bool
    {
        return in_array(Str::lower((string) $user->role), ['admin', 'inventory_manager'], true);
    }
}
