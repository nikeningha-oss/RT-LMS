<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class FixUserRolesSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        
        foreach ($users as $user) {
            if (empty($user->role) || $user->role === 'null') {
                // Set default based on email
                if (strpos($user->email, 'admin') !== false) {
                    $user->role = 'admin';
                } elseif (strpos($user->email, 'driver') !== false) {
                    $user->role = 'driver';
                } else {
                    $user->role = 'customer';
                }
                $user->save();
                $this->command->info("✅ Updated {$user->email} to role: {$user->role}");
            } else {
                $this->command->info("✅ {$user->email} already has role: {$user->role}");
            }
        }
        
        $this->command->info("\n✅ All user roles fixed!");
    }
}