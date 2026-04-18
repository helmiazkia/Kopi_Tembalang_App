<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderItemOption;

class OrderItemOptionSeeder extends Seeder
{
    public function run(): void
    {

        OrderItemOption::insert([

            [
                'order_item_id' => 1,
                'menu_option_item_id' => 1,
                'price' => 4000,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'order_item_id' => 1,
                'menu_option_item_id' => 3,
                'price' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'order_item_id' => 2,
                'menu_option_item_id' => 2,
                'price' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],

        ]);

    }
}