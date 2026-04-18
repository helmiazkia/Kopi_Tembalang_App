<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderItem;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {

        OrderItem::insert([

            [
                'order_id' => 1,
                'menu_id' => 1,
                'qty' => 1,
                'price' => 18000,
                'subtotal' => 18000,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'order_id' => 1,
                'menu_id' => 2,
                'qty' => 2,
                'price' => 20000,
                'subtotal' => 40000,
                'created_at' => now(),
                'updated_at' => now()
            ],

        ]);

    }
}