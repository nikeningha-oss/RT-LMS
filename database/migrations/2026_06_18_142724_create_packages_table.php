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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            
            // Order relationship (nullable)
            $table->foreignId('order_id')->nullable();
            
            // Package identification
            $table->string('tracking_number')->nullable()->unique();
            
            // Package details
            $table->string('description');
            $table->decimal('weight', 8, 2)->nullable(); // in kg
            $table->string('dimensions')->nullable(); // e.g., "30x20x15 cm"
            $table->integer('quantity')->default(1);
            
            // Package specifications
            $table->boolean('is_fragile')->default(false);
            $table->boolean('is_flammable')->default(false);
            $table->boolean('is_perishable')->default(false);
            $table->boolean('requires_signature')->default(false);
            
            // Package status
            $table->enum('status', [
                'pending',
                'packed',
                'picked_up',
                'in_transit',
                'delivered',
                'damaged',
                'lost'
            ])->default('pending');
            
            // Special instructions
            $table->text('special_instructions')->nullable();
            
            // Insurance
            $table->decimal('insured_value', 10, 2)->nullable();
            
            // Photos (JSON array of image paths)
            $table->json('photos')->nullable();
            
            // Package type
            $table->enum('package_type', [
                'document',
                'parcel',
                'pallet',
                'crate',
                'envelope',
                'bag',
                'other'
            ])->default('parcel');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('order_id');
            $table->index('tracking_number');
            $table->index('status');
            $table->index('package_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};