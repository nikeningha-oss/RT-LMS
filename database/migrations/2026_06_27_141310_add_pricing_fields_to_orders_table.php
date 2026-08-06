<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Pricing breakdown
            $table->decimal('base_fare', 10, 2)->default(0)->after('total_price');
            $table->decimal('distance_charge', 10, 2)->default(0)->after('base_fare');
            $table->decimal('weight_charge', 10, 2)->default(0)->after('distance_charge');
            $table->decimal('service_fee', 10, 2)->default(0)->after('weight_charge');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('service_fee');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate');
            
            // Earnings breakdown (50/50 split)
            $table->decimal('driver_earning', 10, 2)->default(0)->after('tax_amount');
            $table->decimal('driver_commission_rate', 5, 2)->default(50)->after('driver_earning');
            $table->decimal('platform_fee', 10, 2)->default(0)->after('driver_commission_rate');
            
            // Add weight column
            $table->decimal('weight_kg', 8, 2)->nullable()->after('distance_km');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'base_fare',
                'distance_charge',
                'weight_charge',
                'service_fee',
                'tax_rate',
                'tax_amount',
                'driver_earning',
                'driver_commission_rate',
                'platform_fee',
                'weight_kg'
            ]);
        });
    }
};