<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Table;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Table::create([
                'table_number' => 'Meja ' . $i,
                'qr_code' => 'http://localhost:5173/menu?table=' . $i,
                'status' => 'available'
            ]);
        }
    }
}
