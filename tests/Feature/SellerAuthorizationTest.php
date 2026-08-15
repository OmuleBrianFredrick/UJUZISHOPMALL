<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_access_seller_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $this->actingAs($user)->get(route('seller.dashboard'))->assertForbidden();
    }

    public function test_customer_cannot_manage_seller_products(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $this->actingAs($user)->get(route('seller.products.index'))->assertForbidden();
    }
}
