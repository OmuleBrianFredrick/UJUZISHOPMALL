<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Promotion;
use App\Models\LoyaltyTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_rejects_insufficient_stock_without_clearing_cart(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create(['quantity' => 1, 'price' => 10000]);
        $this->actingAs($user)->withSession(['cart' => [[
            'product_id' => $product->id, 'name' => $product->name, 'price' => 10000, 'quantity' => 2,
        ]]])->post(route('checkout.store'), [
            'customer_name' => $user->name, 'customer_email' => $user->email,
            'customer_phone' => '0700000000', 'delivery_address' => 'Kampala',
        ])->assertSessionHasErrors('cart');

        $this->assertNotEmpty(session('cart'));
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_rejects_insufficient_loyalty_points(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create(['quantity' => 5, 'price' => 10000]);
        LoyaltyTransaction::create(['user_id' => $user->id, 'type' => 'earn', 'points' => 5, 'description' => 'Test']);
        $this->actingAs($user)->withSession(['cart' => [[
            'product_id' => $product->id, 'name' => $product->name, 'price' => 10000, 'quantity' => 1,
        ]]])->post(route('checkout.store'), [
            'customer_name' => $user->name, 'customer_email' => $user->email,
            'customer_phone' => '0700000000', 'delivery_address' => 'Kampala', 'loyalty_points' => 6,
        ])->assertSessionHasErrors('loyalty_points');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_rejects_invalid_promotion_without_creating_order(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create(['quantity' => 5, 'price' => 10000]);
        Promotion::create(['code' => 'USED', 'type' => 'percentage', 'value' => 10, 'usage_limit' => 1, 'usage_count' => 1, 'active' => true]);
        $this->actingAs($user)->withSession(['cart' => [[
            'product_id' => $product->id, 'name' => $product->name, 'price' => 10000, 'quantity' => 1,
        ]]])->post(route('checkout.store'), [
            'customer_name' => $user->name, 'customer_email' => $user->email,
            'customer_phone' => '0700000000', 'delivery_address' => 'Kampala', 'promotion_code' => 'USED',
        ])->assertSessionHasErrors('promotion_code');

        $this->assertDatabaseCount('orders', 0);
    }
}
