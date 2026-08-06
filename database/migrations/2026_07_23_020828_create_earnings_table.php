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
        Schema::create('earnings', function (Blueprint $table) {
            $table->id();
            
            // Driver relationship
            $table->foreignId('driver_id')
                ->constrained('drivers')
                ->onDelete('cascade');
            
            // Order relationship (nullable for other types of earnings)
            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->onDelete('set null');
            
            // Earnings details
            $table->decimal('amount', 10, 2);
            $table->string('type')->default('delivery'); // delivery, bonus, adjustment, etc.
            $table->string('status')->default('pending'); // pending, paid, cancelled
            
            // Description
            $table->text('description')->nullable();
            
            // When the earning was earned
            $table->timestamp('earned_at');
            
            $table->timestamps();
            
            // Indexes
            $table->index('driver_id');
            $table->index('order_id');
            $table->index('status');
            $table->index('earned_at');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('earnings');
    }
};