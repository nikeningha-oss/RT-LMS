<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;  // ← Import DB here

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Now you can use DB without the backslash
        $dbName = DB::connection()->getDatabaseName();
        $this->command->info("📊 Using database: " . $dbName);

        // Get existing users and drivers
        $customer = User::where('role', 'customer')->first();
        $driver = Driver::first();

        if (!$customer) {
            $this->command->error('❌ No customer found in ' . $dbName . '!');
            $this->command->info('Please create a customer first.');
            return;
        }

        if (!$driver) {
            $this->command->warn('⚠️ No driver found. Orders will be created without driver assignment.');
        }

        $orders = [
            [
                'customer_id' => $customer->id,
                'driver_id' => $driver ? $driver->id : null,
                'status' => 'in_transit',
                'pickup_address' => '123 Main Street, Douala, Cameroon',
                'delivery_address' => '456 Avenue, Yaoundé, Cameroon',
                'pickup_lat' => 4.0511,
                'pickup_lng' => 9.7679,
                'delivery_lat' => 3.8480,
                'delivery_lng' => 11.5021,
                'distance_km' => 215.5,
                'total_price' => 3500,
                'notes' => 'Electronics: Laptop and accessories - Handle with care',
                'estimated_delivery' => now()->addHours(4),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_id' => $customer->id,
                'driver_id' => $driver ? $driver->id : null,
                'status' => 'delivered',
                'pickup_address' => '789 Road, Buea, Cameroon',
                'delivery_address' => '321 Street, Limbe, Cameroon',
                'pickup_lat' => 4.1527,
                'pickup_lng' => 9.2410,
                'delivery_lat' => 4.0247,
                'delivery_lng' => 9.2063,
                'distance_km' => 22.3,
                'total_price' => 1500,
                'notes' => 'Documents: Legal papers - Urgent delivery',
                'estimated_delivery' => now()->subHours(2),
                'actual_delivery' => now()->subHours(1),
                'created_at' => now()->subHours(4),
                'updated_at' => now(),
            ],
            [
                'customer_id' => $customer->id,
                'driver_id' => null,
                'status' => 'pending',
                'pickup_address' => '555 Lane, Kribi, Cameroon',
                'delivery_address' => '666 Way, Ebolowa, Cameroon',
                'pickup_lat' => 2.9333,
                'pickup_lng' => 9.9167,
                'delivery_lat' => 2.9000,
                'delivery_lng' => 11.1500,
                'distance_km' => 145.8,
                'total_price' => 3000,
                'notes' => 'Food Items: Fresh produce - Perishable items',
                'estimated_delivery' => now()->addHours(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_id' => $customer->id,
                'driver_id' => $driver ? $driver->id : null,
                'status' => 'assigned',
                'pickup_address' => '777 Park, Dschang, Cameroon',
                'delivery_address' => '888 Hill, Bafoussam, Cameroon',
                'pickup_lat' => 5.4333,
                'pickup_lng' => 10.0500,
                'delivery_lat' => 5.4667,
                'delivery_lng' => 10.4167,
                'distance_km' => 42.1,
                'total_price' => 2500,
                'notes' => 'Clothing: Fashion items - Fragile',
                'estimated_delivery' => now()->addHours(2),
                'created_at' => now()->subHours(1),
                'updated_at' => now(),
            ],
            [
                'customer_id' => $customer->id,
                'driver_id' => null,
                'status' => 'pending',
                'pickup_address' => '999 Plaza, Garoua, Cameroon',
                'delivery_address' => '101 Avenue, Maroua, Cameroon',
                'pickup_lat' => 9.3000,
                'pickup_lng' => 13.4000,
                'delivery_lat' => 10.5833,
                'delivery_lng' => 14.3167,
                'distance_km' => 185.2,
                'total_price' => 4000,
                'notes' => 'Building Materials - Heavy items',
                'estimated_delivery' => now()->addHours(6),
                'created_at' => now()->subMinutes(30),
                'updated_at' => now(),
            ],
            [
                'customer_id' => $customer->id,
                'driver_id' => $driver ? $driver->id : null,
                'status' => 'picked_up',
                'pickup_address' => '222 Market, Limbe, Cameroon',
                'delivery_address' => '444 Beach, Kribi, Cameroon',
                'pickup_lat' => 4.0247,
                'pickup_lng' => 9.2063,
                'delivery_lat' => 2.9333,
                'delivery_lng' => 9.9167,
                'distance_km' => 128.5,
                'total_price' => 2800,
                'notes' => 'Seafood - Keep refrigerated',
                'estimated_delivery' => now()->addHours(3),
                'created_at' => now()->subHours(2),
                'updated_at' => now(),
            ],
        ];

        foreach ($orders as $order) {
            // Generate order number using your model's method
            $order['order_number'] = Order::generateOrderNumber();
            
            Order::create($order);
        }

        $this->command->info('✅ Orders seeded successfully in ' . $dbName . '!');
        $this->command->info('Total orders: ' . Order::count());
        
        // Show summary table - Using DB (imported at top)
        $this->command->table(
            ['Status', 'Count'],
            DB::table('orders')
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->map(fn($item) => [$item->status, $item->count])
                ->toArray()
        );
    }
}