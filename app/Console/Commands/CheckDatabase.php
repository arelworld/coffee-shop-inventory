<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckDatabase extends Command
{
    protected $signature = 'check:database';
    protected $description = 'Check database status and important tables';

    public function handle()
    {
        $this->info("Database Connection: " . (DB::connection()->getPdo() ? 'Connected' : 'Failed'));
        
        try {
            $users = DB::table('users')->count();
            $items = DB::table('items')->count();
            $categories = DB::table('categories')->count();
            $suppliers = DB::table('suppliers')->count();
            
            $this->info("Table Counts:");
            $this->line("Users: $users");
            $this->line("Items: $items");
            $this->line("Categories: $categories");
            $this->line("Suppliers: $suppliers");
            
            // Check staff user specifically
            $staff = DB::table('users')->where('email', 'staff@coffeeshop.com')->first();
            if ($staff) {
                $this->info("\nStaff User Details:");
                $this->line("Name: " . $staff->name);
                $this->line("Role: " . $staff->role);
                $this->line("Active: " . $staff->is_active);
            } else {
                $this->error("Staff user not found in database!");
            }
            
        } catch (\Exception $e) {
            $this->error("Database error: " . $e->getMessage());
        }

        return 0;
    }
}