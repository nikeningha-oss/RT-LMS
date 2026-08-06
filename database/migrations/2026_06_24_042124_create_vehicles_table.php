<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            
            // Vehicle identification
            $table->string('plate_number')->unique();
            $table->string('registration_number')->nullable()->unique();
            $table->string('vin')->nullable()->unique(); // Vehicle Identification Number
            
            // Vehicle details
            $table->string('make')->nullable();
            $table->string('model');
            $table->string('color')->nullable();
            $table->integer('year')->nullable();
            $table->decimal('capacity', 10, 2)->nullable();
            
            // Vehicle type
            $table->enum('type', [
                'truck', 
                'van', 
                'motorcycle', 
                'bicycle', 
                'car'
            ])->default('van');
            
            // Vehicle status
            $table->enum('status', [
                'available',
                'on_delivery',
                'maintenance',
                'idle',
                'offline'
            ])->default('available');
            
            // Driver assignment
            $table->foreignId('driver_id')->nullable();
            
            // GPS Tracking
            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            $table->timestamp('last_known_location_at')->nullable();
            
            // GPS device info
            $table->string('gps_device_id')->nullable();
            $table->string('tracking_number')->nullable();
            
            // Fuel information
            $table->enum('fuel_type', [
                'petrol', 
                'diesel', 
                'electric', 
                'hybrid'
            ])->default('diesel');
            $table->decimal('fuel_consumption', 8, 2)->nullable();
            
            // Maintenance & Insurance
            $table->integer('mileage')->default(0);
            $table->date('last_service_date')->nullable();
            $table->date('next_service_date')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->string('insurance_provider')->nullable();
            $table->string('insurance_policy_number')->nullable();
            
            // Additional features
            $table->boolean('has_refrigeration')->default(false);
            $table->boolean('has_liftgate')->default(false);
            $table->boolean('has_gps_tracking')->default(true);
            
            // Operational
            $table->boolean('is_active')->default(true);
            
            // Cost tracking
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->decimal('daily_rental_rate', 10, 2)->nullable();
            $table->decimal('cost_per_km', 10, 2)->nullable();
            
            // Documents
            $table->string('photo')->nullable();
            $table->string('insurance_document')->nullable();
            $table->string('registration_document')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('plate_number');
            $table->index('driver_id');
            $table->index('status');
            $table->index('type');
            $table->index('is_active');
            $table->index(['is_active', 'status']);
            $table->index('registration_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};