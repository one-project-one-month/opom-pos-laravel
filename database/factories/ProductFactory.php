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
            'price' => fake()->randomFloat(1, 20, 300),
            'const_price' => fake()->randomFloat(1, 20, 300),
            'stock' => rand(200, 300),
            'brand_id' => rand(1, 10),
            'category_id' => rand(1, 4),
        ];
    }
}
