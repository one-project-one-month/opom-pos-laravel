<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            'order_id'=> rand(1, 10),
            'product_id'=> rand(1, 10),
            'quantity'=> rand(1, 10),
            'price'=> rand(1, 10),
            'total'=> rand(1, 10),
        ];
    }
}
