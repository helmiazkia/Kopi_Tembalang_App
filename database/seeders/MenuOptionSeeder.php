<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuOption;

class MenuOptionSeeder extends Seeder
{
    public function run(): void
    {
        MenuOption::create([

            'menu_id' => 1,
            'name' => 'Ukuran Cup',
            'type' => 'select'


        ]);
        MenuOption::create([

            'menu_id' => 1,
            'name' => 'Gula',
            'type' => 'select'


        ]);
        MenuOption::create([

            'menu_id' => 1,
            'name' => 'Topping',
            'type' => 'checkbox'


        ]);
    }
}
