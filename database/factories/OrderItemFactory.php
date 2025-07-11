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
<<<<<<< HEAD
        return [
            'order_id' => rand(1,3),
            'product_id' => rand(1, 5),
            'quantity' => rand(3, 5),
            'price' => fake()->randomFloat(2, 30, 200),
            'total' =>fake()->randomFloat(300,400, 20000),
=======
         return [
            'order_id'=> rand(1, 10),
            'product_id'=> rand(1, 10),
            'quantity'=> rand(1, 10),
            'price'=> rand(1, 10),
            'total'=> rand(1, 10),
>>>>>>> 0b7984589e4fd3c43e63b4f5df8c1a607c343a98
        ];
    }
}
