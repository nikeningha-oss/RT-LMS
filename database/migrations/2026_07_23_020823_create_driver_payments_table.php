<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_payments', function (Blueprint $table) {
            $table->id();
            
            // Driver relationship
            $table->foreignId('driver_id')
                ->constrained('drivers')
                ->onDelete('cascade');
            
            // Payment details
            $table->decimal('amount', 10, 2);
            $table->string('month'); // e.g., "January 2024"
            
            // Admin who processed the payment
            $table->foreignId('paid_by')
                ->constrained('users')
                ->onDelete('cascade');
            
            // Timestamps
            $table->timestamp('paid_at');
            $table->text('notes')->nullable();
            $table->string('status')->default('completed');
            
            $table->timestamps();
            
            // Indexes
            $table->index('driver_id');
            $table->index('month');
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_payments');
    }
};