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
<<<<<<< HEAD
            "order_number" => rand(1, 5),
            "user_id" => rand(1, 2),
            "total" => fake()->randomFloat(2, 300, 400),
            "payment_id" => rand(1, 5),
            "customer_id" => rand(1, 5),
            "paid_amount" => fake()->randomFloat(5, 20, 300),
            "change_amount" =>fake()->randomFloat(5, 20, 200),
         ];
=======
            'order_number'=> fake()->randomFloat(20, 20, 300),
            'user_id'=>1,
            'total'=>fake()->randomFloat(1, 20, 300),
            'payment_id'=>1,
            'customer_id'=>1,
            'paid_amount'=>fake()->randomFloat(1, 20, 300),
            'change_amount'=>fake()->randomFloat(1, 20, 300),
        ];
>>>>>>> 0b7984589e4fd3c43e63b4f5df8c1a607c343a98
    }
}
