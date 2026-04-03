<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN
        User::create([
            'name' => 'Admin',
            'email' => 'admin@coffee.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // CASHIER
        User::create([
            'name' => 'Kasir',
            'email' => 'kasir@coffee.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
        ]);
    }
}
