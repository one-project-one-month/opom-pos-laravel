<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order_item>
 */
class Order_itemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => rand(1,3),
            'product_id' => rand(1, 5),
            'quantity' => rand(3, 5),
            'price' => fake()->randomFloat(2, 30, 200),
            'total' =>fake()->rand(300,400, 20000),
        ];
    }
}
