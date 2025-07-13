<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscountItem>
 */
class DiscountItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'product_id' => rand(1, 5),
            'title' => fake()->randomElement(['Water','Thatinkyaut']),
            'dis_percent' => rand(1, 5),
            'start_date' => now(),
            



        ];
    }
}
