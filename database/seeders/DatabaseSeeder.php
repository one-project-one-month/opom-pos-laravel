<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\DiscountItem;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // OrderItem::factory(10)->create();
        // User::factory(2)->create();
        //Order
        // Customer::factory(5)->create();

        Product::factory(10)->create();
        // Category::factory(5)->create();
       
        // Product::factory(10)->create();
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // OrderItem::factory(10)->create();
        // Order::factory(10)->create();

        // Order::factory(10)->create();
        // DiscountItem::factory(10)->create();
    }
}
