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
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            
            // Driver relationship
            $table->foreignId('driver_id')
                ->constrained('drivers')
                ->onDelete('cascade');
            
            // Amount details
            $table->decimal('amount', 10, 2);
            $table->decimal('fee', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            
            // Payment details
            $table->string('payment_method'); // mtn, orange, bank
            $table->string('account_details');
            
            // Status
            $table->string('status')->default('pending'); // pending, approved, completed, rejected
            
            // Admin who processed
            $table->foreignId('admin_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            
            // Notes
            $table->text('admin_note')->nullable();
            
            // Timestamps
            $table->timestamp('requested_at');
            $table->timestamp('processed_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('driver_id');
            $table->index('status');
            $table->index('requested_at');
            $table->index('processed_at');
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};