<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('type')->default('delivery');
            $table->string('status')->default('pending');
            $table->text('description')->nullable();
            $table->timestamp('earned_at');
            $table->timestamps();
            
            $table->index(['driver_id', 'status']);
            $table->index(['earned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
};