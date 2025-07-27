<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'=> fake()->name(),
            'sku' => fake()->randomFloat(20, 20, 300),
            'price' => rand(4000, 5000),
            'const_price' =>rand(1000, 2000),
            'dis_percent' => rand(5,10),
            'stock' => rand(0, 100),
            'brand_id' => rand(1, 5),
            'category_id' => rand(1, 5),

        ];
    }
}
