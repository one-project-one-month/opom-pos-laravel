<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\OrderItem;
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
<<<<<<< HEAD
=======
        Category::factory(5)->create();
        Brand::factory(5)->create();
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password'=>Hash::make('admin123'),
        ]);
>>>>>>> 0b7984589e4fd3c43e63b4f5df8c1a607c343a98
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
<<<<<<< HEAD
=======
        OrderItem::factory(10)->create();
        Order::factory(10)->create();
>>>>>>> 0b7984589e4fd3c43e63b4f5df8c1a607c343a98
    }
}
