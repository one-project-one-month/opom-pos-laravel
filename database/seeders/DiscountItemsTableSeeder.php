<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discountItems = [
            [
                'title' => 'Summer Sale Discount',
                'dis_percent' => 15,
                'start_date' => '2024-06-01',
                'end_date' => '2024-06-30',
            ],
            [
                'title' => 'Winter Special Offer',
                'dis_percent' => 20,
                'start_date' => '2024-12-01',
                'end_date' => '2024-12-31',
            ],
            [
                'title' => 'Halloween Event',
                'dis_percent' => 30,
                'start_date' => null,
                'end_date' => null,
            ],
            [
                'title' => 'New Year Promotion',
                'dis_percent' => 25,
                'start_date' => '2024-12-25',
                'end_date' => '2025-01-05',
            ],
            [
                'title' => 'Thingyan Festival Promotion',
                'dis_percent' => 50,
                'start_date' => '2024-07-15',
                'end_date' => '2024-07-17',
            ]
        ];
        DB::table('discount_items')->insert($discountItems);
    }
}
