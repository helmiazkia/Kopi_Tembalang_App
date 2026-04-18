<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;

class PaymentSeeder extends Seeder
{

    public function run(): void
    {

        Payment::insert([

            [
                'order_id' => 1,
                'transaction_id' => 'TRX001',
                'method' => 'cash',
                'channel' => null,
                'amount' => 58000,
                'status' => 'paid',
                'paid_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'order_id' => 2,
                'transaction_id' => 'TRX002',
                'method' => 'qris',
                'channel' => 'gopay',
                'amount' => 20000,
                'status' => 'paid',
                'paid_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]

        ]);

    }

}