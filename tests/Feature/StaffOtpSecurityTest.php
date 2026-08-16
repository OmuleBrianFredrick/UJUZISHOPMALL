<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StaffOtpSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_sent_to_otp_after_password_login(): void
    {
        Mail::fake();
        $user = User::factory()->create(['role' => 'admin', 'password' => Hash::make('password')]);
        $response = $this->post(route('login.submit'), ['email' => $user->email, 'password' => 'password']);
        $response->assertRedirect(route('login.otp.challenge'));
        $this->assertGuest();
        $this->assertNotNull(session('pending_staff_login_user_id'));
        Mail::assertSent(\App\Mail\StaffLoginOtp::class);
    }

    public function test_customer_does_not_enter_staff_otp_flow(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'password' => Hash::make('password')]);
        $response = $this->post(route('login.submit'), ['email' => $user->email, 'password' => 'password']);
        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
        $this->assertNull(session('pending_staff_login_user_id'));
    }

    public function test_seller_is_sent_to_otp_after_password_login(): void
    {
        Mail::fake();
        $user = User::factory()->create(['role' => 'seller', 'password' => Hash::make('password')]);
        $response = $this->post(route('login.submit'), ['email' => $user->email, 'password' => 'password']);
        $response->assertRedirect(route('login.otp.challenge'));
        $this->assertGuest();
        $this->assertNotNull(session('pending_staff_login_user_id'));
        Mail::assertSent(\App\Mail\StaffLoginOtp::class);
    }
}
