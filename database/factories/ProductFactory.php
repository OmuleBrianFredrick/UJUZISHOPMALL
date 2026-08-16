<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' ' . $this->faker->unique()->numberBetween(1, 9999),
            'sku' => 'SKU-' . strtoupper($this->faker->bothify('???-#####')),
            'category' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(1000, 100000),
            'quantity' => $this->faker->numberBetween(0, 100),
            'reorder_level' => $this->faker->numberBetween(1, 10),
            'image' => null,
            'seller_id' => User::factory(),
        ];
    }
}
