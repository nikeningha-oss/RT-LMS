<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegisterMigrations extends Command
{
    protected $signature = 'migrate:register';
    protected $description = 'Register migrations manually in the migrations table';

    public function handle()
    {
        $migrationFiles = [
            '2024_01_01_000000_create_users_table',
            '2024_01_01_000001_create_drivers_table',
            '2024_01_01_000002_create_vehicles_table',
            '2024_01_01_000003_create_orders_table',
        ];

        foreach ($migrationFiles as $file) {
            DB::table('migrations')->updateOrInsert(
                ['migration' => $file],
                ['batch' => 1]
            );
            $this->info("Registered: $file");
        }

        $this->info('✅ All migrations registered successfully!');
    }
}