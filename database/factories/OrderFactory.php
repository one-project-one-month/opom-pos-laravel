<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number'=> fake()->randomFloat(20, 20, 300),
            'user_id'=>1,
            'total'=>fake()->randomFloat(1, 20, 300),
            'payment_id'=>1,
            'customer_id'=>1,
            'paid_amount'=>fake()->randomFloat(1, 20, 300),
            'change_amount'=>fake()->randomFloat(1, 20, 300),
        ];
    }
}
