<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Item;

class CoffeeShopDataSeeder extends Seeder
{
    public function run()
    {
        // Create units
        $units = [
            ['name' => 'Kilogram', 'abbreviation' => 'kg'],
            ['name' => 'Liter', 'abbreviation' => 'L'],
            ['name' => 'Piece', 'abbreviation' => 'pcs'],
            ['name' => 'Gram', 'abbreviation' => 'g'],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }

        // Create suppliers
        $suppliers = [
            ['name' => 'Local Coffee Roaster', 'contact_person' => 'Juan Dela Cruz', 'phone' => '09171234567'],
            ['name' => 'Fresh Dairy Co.', 'contact_person' => 'Maria Santos', 'phone' => '09177654321'],
            ['name' => 'Sweet Flavors Inc.', 'contact_person' => 'Robert Lim', 'phone' => '09172345678'],
            ['name' => 'Metro Packaging', 'contact_person' => 'Anna Torres', 'phone' => '09173456789'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        // Create coffee shop categories if they don't exist
        $categories = [
            ['name' => 'Coffee Beans'],
            ['name' => 'Dairy & Milk'],
            ['name' => 'Syrups & Flavors'],
            ['name' => 'Tea'],
            ['name' => 'Bakery Items'],
            ['name' => 'Packaging'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']]);
        }

        // Create sample coffee shop items
        $items = [
            [
                'name' => 'Espresso Roast Beans',
                'category_id' => Category::where('name', 'Coffee Beans')->first()->id,
                'quantity' => 8.5,
                'reorder_point' => 5,
                'unit_id' => Unit::where('abbreviation', 'kg')->first()->id,
                'cost_per_unit' => 800.00,
                'supplier_id' => Supplier::where('name', 'Local Coffee Roaster')->first()->id,
                'sku' => 'COF-ESPRESSO-01'
            ],
            [
                'name' => 'Whole Milk',
                'category_id' => Category::where('name', 'Dairy & Milk')->first()->id,
                'quantity' => 12.0,
                'reorder_point' => 10,
                'unit_id' => Unit::where('abbreviation', 'L')->first()->id,
                'cost_per_unit' => 60.00,
                'supplier_id' => Supplier::where('name', 'Fresh Dairy Co.')->first()->id,
                'sku' => 'DAIRY-MILK-01'
            ],
            [
                'name' => 'Vanilla Syrup',
                'category_id' => Category::where('name', 'Syrups & Flavors')->first()->id,
                'quantity' => 3.0,
                'reorder_point' => 2,
                'unit_id' => Unit::where('abbreviation', 'L')->first()->id,
                'cost_per_unit' => 250.00,
                'supplier_id' => Supplier::where('name', 'Sweet Flavors Inc.')->first()->id,
                'sku' => 'SYRUP-VANILLA-01'
            ],
            [
                'name' => 'Medium Cups',
                'category_id' => Category::where('name', 'Packaging')->first()->id,
                'quantity' => 150,
                'reorder_point' => 100,
                'unit_id' => Unit::where('abbreviation', 'pcs')->first()->id,
                'cost_per_unit' => 5.00,
                'supplier_id' => Supplier::where('name', 'Metro Packaging')->first()->id,
                'sku' => 'PKG-CUP-MED-01'
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }

        $this->command->info('Coffee shop sample data created successfully!');
    }
}