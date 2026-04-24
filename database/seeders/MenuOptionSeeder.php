<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuOption;

class MenuOptionSeeder extends Seeder
{
    public function run(): void
    {
        MenuOption::create([

            
            'name' => 'Ukuran Cup',
            'type' => 'select'


        ]);
        MenuOption::create([

       
            'name' => 'Gula',
            'type' => 'select'


        ]);
        MenuOption::create([

           
            'name' => 'Topping',
            'type' => 'checkbox'


        ]);
    }
}
