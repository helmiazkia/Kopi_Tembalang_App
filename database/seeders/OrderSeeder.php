<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run(): void
    {

        Order::insert([

            [
                'table_id' => 1,
                'cashier_id' => 2,
                'customer_name' => 'Andi',
                'phone' => '08123456789',
                'notes' => 'Tidak terlalu manis',
                'total_price' => 58000,
                'status' => 'paid',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'table_id' => 2,
                'cashier_id' => 2,
                'customer_name' => 'Budi',
                'phone' => '08123456780',
                'notes' => null,
                'total_price' => 20000,
                'status' => 'preparing',
                'created_at' => now(),
                'updated_at' => now()
            ]

        ]);

    }
}