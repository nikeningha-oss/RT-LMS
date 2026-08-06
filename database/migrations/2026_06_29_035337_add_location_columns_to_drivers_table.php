<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('drivers', 'current_speed')) {
                $table->decimal('current_speed', 8, 2)->nullable()->after('current_lng');
            }
            if (!Schema::hasColumn('drivers', 'last_known_location_at')) {
                $table->timestamp('last_known_location_at')->nullable()->after('current_speed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['current_speed', 'last_known_location_at']);
        });
    }
};