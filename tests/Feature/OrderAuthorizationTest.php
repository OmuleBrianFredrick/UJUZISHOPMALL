<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_own_order(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->get(route('orders.show', $order))->assertOk();
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $other = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create(['user_id' => $owner->id]);
        $this->actingAs($other)->get(route('orders.show', $order))->assertForbidden();
    }
}
