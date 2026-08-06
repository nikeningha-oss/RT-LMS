<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Check if columns exist before adding
            
            // Weight column
            if (!Schema::hasColumn('orders', 'weight_kg')) {
                $table->decimal('weight_kg', 8, 2)->nullable()->after('distance_km');
            }
            
            // Pricing breakdown columns
            if (!Schema::hasColumn('orders', 'base_fare')) {
                $table->decimal('base_fare', 10, 2)->default(0)->after('total_price');
            }
            
            if (!Schema::hasColumn('orders', 'distance_charge')) {
                $table->decimal('distance_charge', 10, 2)->default(0)->after('base_fare');
            }
            
            if (!Schema::hasColumn('orders', 'weight_charge')) {
                $table->decimal('weight_charge', 10, 2)->default(0)->after('distance_charge');
            }
            
            if (!Schema::hasColumn('orders', 'service_fee')) {
                $table->decimal('service_fee', 10, 2)->default(0)->after('weight_charge');
            }
            
            if (!Schema::hasColumn('orders', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(0)->after('service_fee');
            }
            
            if (!Schema::hasColumn('orders', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate');
            }
            
            // Earnings columns
            if (!Schema::hasColumn('orders', 'driver_earning')) {
                $table->decimal('driver_earning', 10, 2)->default(0)->after('tax_amount');
            }
            
            if (!Schema::hasColumn('orders', 'driver_commission_rate')) {
                $table->decimal('driver_commission_rate', 5, 2)->default(50)->after('driver_earning');
            }
            
            if (!Schema::hasColumn('orders', 'platform_fee')) {
                $table->decimal('platform_fee', 10, 2)->default(0)->after('driver_commission_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'weight_kg',
                'base_fare',
                'distance_charge',
                'weight_charge',
                'service_fee',
                'tax_rate',
                'tax_amount',
                'driver_earning',
                'driver_commission_rate',
                'platform_fee'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};