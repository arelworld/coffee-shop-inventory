<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    public function run()
    {
        // Create a default user for inventory transactions
        User::firstOrCreate(
            ['email' => 'inventory@coffeeshop.com'],
            [
                'name' => 'Inventory Manager',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Default inventory user created!');
    }
}