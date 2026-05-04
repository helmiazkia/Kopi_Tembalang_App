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
            'image' => './menu/1777446658_69f1af022736b.jpg',
            'is_available' => true
        ]);

        Menu::create([
            'category_id' => 1,
            'name' => 'Latte',
            'description' => 'Kopi susu',
            'price' => 20000,
            'image' => './menu/1776934466_69e9de4264c6c.jpg',
            'is_available' => true
        ]);

        Menu::create([
            'category_id' => 2,
            'name' => 'Matcha Latte',
            'description' => 'Minuman matcha',
            'price' => 22000,
            'image' => './menu/1776768215_69e754d7b7078.jpg',
            'is_available' => true
        ]);

        Menu::create([
            'category_id' => 3,
            'name' => 'French Fries',
            'description' => 'Kentang goreng',
            'price' => 12000,
            'image' => './menu/1776768226_69e754e227323.jpg',
            'is_available' => true
        ]);
    }
}
