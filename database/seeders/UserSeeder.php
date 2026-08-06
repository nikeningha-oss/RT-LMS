<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('nike20005.'),
                'role' => 'admin',
                'approval_status' => 'approved',
            ]
        );
        $this->command->info('✅ Admin created: admin@gmail.com.com / nike20005.');

        // Create Driver
        $driverUser = User::updateOrCreate(
            ['email' => 'latifa@gmail.com'],
            [
                'name' => 'Test Driver',
                'password' => Hash::make('nike20005.'),
                'role' => 'driver',
                'approval_status' => 'approved',
            ]
        );

        // Create driver profile
        Driver::updateOrCreate(
            ['user_id' => $driverUser->id],
            [
                'license_number' => 'LIC-12345',
                'phone' => '+237 690000000',
                'is_available' => true,
            ]
        );
        $this->command->info('✅ Driver created: latifa@gmail.com / nike20005.');

        // Create Customer
        $customer = User::updateOrCreate(
            ['email' => 'nike@gmail.com'],
            [
                'name' => 'Test Customer',
                'password' => Hash::make('nike20005.'),
                'role' => 'customer',
                'approval_status' => 'approved',
            ]
        );
        $this->command->info('✅ Customer created: nike@gmail.com / nike20005.');

        // Show all users
        $this->command->info("\n📊 All Users:");
        $users = User::all();
        foreach ($users as $user) {
            $driverInfo = $user->role === 'driver' ? ($user->driver ? '✅ Has driver profile' : '❌ No driver profile') : 'N/A';
            $this->command->line("ID: {$user->id} | {$user->email} | Role: {$user->role} | Status: {$user->approval_status} | {$driverInfo}");
        }
    }
}