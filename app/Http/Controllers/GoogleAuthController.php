<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request)
    {
        $config = config('auth_services.google');
        abort_if(empty($config['client_id']) || empty($config['client_secret']), 503, 'Google sign-in is not configured yet.');

        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
        ]));
    }

    public function callback(Request $request)
    {
        abort_unless($request->filled('code'), 422, 'Google did not return an authorization code.');
        abort_unless(hash_equals((string) $request->session()->pull('google_oauth_state'), (string) $request->string('state')), 419, 'Invalid Google authentication state.');

        $config = config('auth_services.google');
        $token = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'code' => $request->string('code'),
            'grant_type' => 'authorization_code',
            'redirect_uri' => $config['redirect_uri'],
        ]);
        abort_unless($token->successful(), 422, 'Google token exchange failed.');

        $profile = Http::withToken($token->json('access_token'))->get('https://openidconnect.googleapis.com/v1/userinfo');
        abort_unless($profile->successful(), 422, 'Google profile lookup failed.');

        $data = $profile->json();
        abort_if(empty($data['sub']) || empty($data['email']), 422, 'Google did not return the required account information.');

        $user = User::where('google_id', $data['sub'])->orWhere('email', $data['email'])->first();
        if (!$user) {
            $user = new User();
            $user->name = $data['name'] ?? $data['email'];
            $user->email = $data['email'];
            $user->password = Hash::make(Str::random(64));
        }

        $user->google_id = $data['sub'];
        $user->email_verified_at = $user->email_verified_at ?? now();
        $user->save();

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('products.index'));
    }
}
