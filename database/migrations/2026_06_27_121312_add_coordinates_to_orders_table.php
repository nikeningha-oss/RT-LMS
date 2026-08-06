<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'pickup_lat')) {
                $table->decimal('pickup_lat', 10, 8)->nullable()->after('pickup_address');
                $table->decimal('pickup_lng', 11, 8)->nullable()->after('pickup_lat');
                $table->decimal('delivery_lat', 10, 8)->nullable()->after('delivery_address');
                $table->decimal('delivery_lng', 11, 8)->nullable()->after('delivery_lat');
                $table->decimal('distance_km', 8, 2)->nullable()->after('delivery_lng');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['pickup_lat', 'pickup_lng', 'delivery_lat', 'delivery_lng', 'distance_km']);
        });
    }
};