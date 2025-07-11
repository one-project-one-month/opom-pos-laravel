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
            "order_number" => rand(1, 5),
            "user_id" => rand(1, 2),
            "total" => fake()->randomFloat(2, 300, 400),
            "payment_id" => rand(1, 5),
            "customer_id" => rand(1, 5),
            "paid_amount" => fake()->randomFloat(5, 20, 300),
            "change_amount" =>fake()->randomFloat(5, 20, 200),
         ];
    }
}
