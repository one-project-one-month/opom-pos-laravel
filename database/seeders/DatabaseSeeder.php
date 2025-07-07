<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Order_item;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        Product::factory(10)->create();
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        OrderItem::factory(10)->create();
        Order::factory(10)->create();
    }
}
