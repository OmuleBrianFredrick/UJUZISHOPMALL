<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 1000, 100000);
        $delivery = $this->faker->randomFloat(2, 0, 2000);
        $discount = 0.00;
        $total = $subtotal + $delivery - $discount;

        return [
            'user_id' => User::factory(),
            'order_number' => strtoupper($this->faker->bothify('ORD-#####')),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'subtotal' => $subtotal,
            'delivery_fee' => $delivery,
            'total' => $total,
            'discount' => $discount,
            'promotion_code' => null,
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->safeEmail(),
            'customer_phone' => $this->faker->phoneNumber(),
            'delivery_address' => $this->faker->address(),
            'notes' => null,
        ];
    }
}
