<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\Driver;

class VehicleSeeder extends Seeder
{
    public function run()
    {
        // ============================================
        // 1. CREATE DRIVERS (Assuming users already exist)
        // ============================================
        $drivers = [
            [
                'user_id' => 1,  // Make sure this user exists in your users table
                'phone' => '+237 691234567',
                'is_available' => true,
                'license_number' => 'LIC-001-2024',
                'current_lat' => 3.8480,
                'current_lng' => 11.5021,
            ],
            [
                'user_id' => 2,
                'phone' => '+237 692345678',
                'is_available' => true,
                'license_number' => 'LIC-002-2024',
                'current_lat' => 3.8580,
                'current_lng' => 11.5121,
            ],
            [
                'user_id' => 3,
                'phone' => '+237 693456789',
                'is_available' => false,
                'license_number' => 'LIC-003-2024',
                'current_lat' => 3.8380,
                'current_lng' => 11.4921,
            ],
            [
                'user_id' => 4,
                'phone' => '+237 694567890',
                'is_available' => true,
                'license_number' => 'LIC-004-2024',
                'current_lat' => null,
                'current_lng' => null,
            ],
            [
                'user_id' => 5,
                'phone' => '+237 695678901',
                'is_available' => true,
                'license_number' => 'LIC-005-2024',
                'current_lat' => null,
                'current_lng' => null,
            ],
        ];

        foreach ($drivers as $driver) {
            // Check if driver already exists for this user
            Driver::updateOrCreate(
                ['user_id' => $driver['user_id']],
                $driver
            );
        }

        $this->command->info('✅ Drivers created/updated successfully!');

        // ============================================
        // 2. CREATE VEHICLES
        // ============================================
        $vehicles = [
            [
                'plate_number' => 'LT-2291',
                'registration_number' => 'REG-001',
                'make' => 'Toyota',
                'model' => 'Hilux',
                'color' => 'White',
                'year' => 2022,
                'capacity' => 1500,
                'type' => 'truck',
                'status' => 'on_delivery',
                'current_latitude' => 3.8480,
                'current_longitude' => 11.5021,
                'driver_id' => 1, // This will be the ID in drivers table
                'is_active' => true,
                'mileage' => 45000,
                'fuel_type' => 'diesel',
                'last_service_date' => '2024-01-15',
                'insurance_expiry' => '2025-12-31',
                'last_known_location_at' => now(),
            ],
            [
                'plate_number' => 'LT-2290',
                'registration_number' => 'REG-002',
                'make' => 'Mercedes',
                'model' => 'Sprinter',
                'color' => 'Silver',
                'year' => 2023,
                'capacity' => 2000,
                'type' => 'van',
                'status' => 'available',
                'current_latitude' => 3.8580,
                'current_longitude' => 11.5121,
                'driver_id' => null, // No driver assigned yet
                'is_active' => true,
                'mileage' => 12000,
                'fuel_type' => 'diesel',
                'last_service_date' => '2024-03-10',
                'insurance_expiry' => '2025-06-30',
                'last_known_location_at' => now(),
            ],
            [
                'plate_number' => 'LT-2289',
                'registration_number' => 'REG-003',
                'make' => 'Honda',
                'model' => 'Breeze',
                'color' => 'Red',
                'year' => 2024,
                'capacity' => 200,
                'type' => 'motorcycle',
                'status' => 'idle',
                'current_latitude' => 3.8380,
                'current_longitude' => 11.4921,
                'driver_id' => 2, // This will be the ID in drivers table
                'is_active' => true,
                'mileage' => 5000,
                'fuel_type' => 'petrol',
                'last_service_date' => '2024-05-20',
                'insurance_expiry' => '2025-09-15',
                'last_known_location_at' => now(),
            ],
            [
                'plate_number' => 'LT-2288',
                'registration_number' => 'REG-004',
                'make' => 'Toyota',
                'model' => 'Hiace',
                'color' => 'Blue',
                'year' => 2021,
                'capacity' => 1800,
                'type' => 'van',
                'status' => 'maintenance',
                'current_latitude' => null,
                'current_longitude' => null,
                'driver_id' => null,
                'is_active' => false,
                'mileage' => 78000,
                'fuel_type' => 'diesel',
                'last_service_date' => '2023-12-01',
                'insurance_expiry' => '2024-10-15',
                'last_known_location_at' => null,
            ],
        ];

        foreach ($vehicles as $vehicle) {
            // Check if vehicle already exists
            Vehicle::updateOrCreate(
                ['plate_number' => $vehicle['plate_number']],
                $vehicle
            );
        }

        $this->command->info('✅ Vehicles created/updated successfully!');

        // ============================================
        // 3. UPDATE DRIVERS WITH VEHICLE ASSIGNMENTS
        // ============================================
        // Update drivers with their vehicle assignments
        Driver::where('id', 1)->update(['vehicle_id' => 1]); // Driver 1 -> Vehicle 1 (Hilux)
        Driver::where('id', 2)->update(['vehicle_id' => 3]); // Driver 2 -> Vehicle 3 (Breeze)

        $this->command->info('✅ Vehicle assignments updated successfully!');
        $this->command->info('====================================');
        $this->command->info('📊 Summary:');
        $this->command->info('Total Drivers: ' . Driver::count());
        $this->command->info('Total Vehicles: ' . Vehicle::count());
        $this->command->info('====================================');
    }
}