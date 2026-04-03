<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Coffee',
            'Non Coffee',
            'Snack'
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat
            ]);
        }
    }
}
