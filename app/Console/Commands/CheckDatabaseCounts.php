<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Models\Category;
use App\Models\Supplier;

class CheckDatabaseCounts extends Command
{
    protected $signature = 'check:db-counts';
    protected $description = 'Check database record counts';

    public function handle()
    {
        $this->info("Database Counts:");
        $this->line("Items: " . Item::count());
        $this->line("Categories: " . Category::count());
        $this->line("Suppliers: " . Supplier::count());
        
        return 0;
    }
}