<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
   public function run(): void
{
    \App\Models\Item::insert([
        [
            'name' => 'Espresso Beans',
            'quantity' => 50,
            'reorder_point' => 20,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Milk',
            'quantity' => 30,
            'reorder_point' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Sugar',
            'quantity' => 15,
            'reorder_point' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
}

}
