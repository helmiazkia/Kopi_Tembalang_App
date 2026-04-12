<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuOptionItem;

class MenuOptionItemSeeder extends Seeder
{
    public function run(): void
    {
        MenuOptionItem::create([


            'menu_option_id' => 1,
            'name' => 'Small',
            'price' => 0

        ]);
        MenuOptionItem::create([



            'menu_option_id' => 1,
            'name' => 'Medium',
            'price' => 2000

        ]);
        MenuOptionItem::create([


            'menu_option_id' => 1,
            'name' => 'Large',
            'price' => 4000

        ]);
        MenuOptionItem::create([



            'menu_option_id' => 2,
            'name' => 'Normal Sugar',
            'price' => 0


        ]);
    }
}
