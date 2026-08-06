<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Order;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        // Get first order
        $order = Order::first();

        if (!$order) {
            $this->command->error('❌ No orders found! Please run OrderSeeder first.');
            return;
        }

        $packages = [
            [
                'order_id' => $order->id,
                'description' => 'Laptop and accessories',
                'weight' => 3.5,
                'dimensions' => '40x30x10 cm',
                'quantity' => 1,
                'is_fragile' => true,
                'requires_signature' => true,
                'package_type' => 'parcel',
                'insured_value' => 500000,
                'status' => 'in_transit',
            ],
            [
                'order_id' => $order->id,
                'description' => 'Documents - Legal papers',
                'weight' => 0.5,
                'dimensions' => '25x20x5 cm',
                'quantity' => 1,
                'is_fragile' => false,
                'requires_signature' => true,
                'package_type' => 'document',
                'insured_value' => 100000,
                'status' => 'delivered',
            ],
            [
                'order_id' => $order->id,
                'description' => 'Fresh produce - Fruits',
                'weight' => 10.0,
                'dimensions' => '50x40x30 cm',
                'quantity' => 2,
                'is_fragile' => false,
                'is_perishable' => true,
                'requires_signature' => false,
                'package_type' => 'parcel',
                'insured_value' => 25000,
                'status' => 'pending',
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }

        $this->command->info('✅ Packages seeded successfully!');
        $this->command->info('Total packages: ' . Package::count());
    }
}