<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Menu::create([
            'category_id' => 1,
            'name' => 'Americano',
            'description' => 'Kopi hitam klasik',
            'price' => 15000,
            'is_available' => true
        ]);

        Menu::create([
            'category_id' => 1,
            'name' => 'Latte',
            'description' => 'Kopi susu',
            'price' => 20000,
            'is_available' => true
        ]);

        Menu::create([
            'category_id' => 2,
            'name' => 'Matcha Latte',
            'description' => 'Minuman matcha',
            'price' => 22000,
            'is_available' => true
        ]);

        Menu::create([
            'category_id' => 3,
            'name' => 'French Fries',
            'description' => 'Kentang goreng',
            'price' => 12000,
            'is_available' => true
        ]);
    }
}
