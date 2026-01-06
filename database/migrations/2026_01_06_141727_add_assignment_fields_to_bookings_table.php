<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('driver_name')->nullable()->after('special_requests');
            $table->string('driver_phone')->nullable()->after('driver_name');
            $table->string('guide_name')->nullable()->after('driver_phone');
            $table->string('guide_phone')->nullable()->after('guide_name');
            $table->string('vehicle_info')->nullable()->after('guide_phone');
            $table->text('internal_notes')->nullable()->after('vehicle_info');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'driver_name',
                'driver_phone', 
                'guide_name',
                'guide_phone',
                'vehicle_info',
                'internal_notes',
            ]);
        });
    }
};
