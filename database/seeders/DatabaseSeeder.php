<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\DiscountItem;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

        // Product::factory(10)->create();
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
<<<<<<< HEAD
        // Order::factory(10)->create();
        DiscountItem::factory(10)->create();
=======
        Order::factory(10)->create();
        DiscountItemsTableSeeder::class;
>>>>>>> 54c452e272deb607785b3080ce032ea102668c5f
    }
}
