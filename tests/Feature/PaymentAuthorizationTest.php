<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_access_another_customers_payment_page(): void
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $other = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->get(route('payments.create', $order))
            ->assertForbidden();
    }
}
