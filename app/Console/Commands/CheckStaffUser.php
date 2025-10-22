<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckStaffUser extends Command
{
    protected $signature = 'check:staff-user';
    protected $description = 'Check staff user status';

    public function handle()
    {
        $staff = User::where('email', 'staff@coffeeshop.com')->first();
        
        if ($staff) {
            $this->info("Staff User Found:");
            $this->line("ID: " . $staff->id);
            $this->line("Name: " . $staff->name);
            $this->line("Role: " . $staff->role);
            $this->line("Active: " . ($staff->is_active ? 'Yes' : 'No'));
            $this->line("Created: " . $staff->created_at);
        } else {
            $this->error("Staff user not found!");
        }

        return 0;
    }
}