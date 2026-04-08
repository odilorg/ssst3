<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Note: ->after('special_requests') removed — that column never existed in migrations
            if (!Schema::hasColumn('bookings', 'driver_name')) {
                $table->string('driver_name')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'driver_phone')) {
                $table->string('driver_phone')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'guide_name')) {
                $table->string('guide_name')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'guide_phone')) {
                $table->string('guide_phone')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'vehicle_info')) {
                $table->string('vehicle_info')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'internal_notes')) {
                $table->text('internal_notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $toDrop = array_filter(
                ['driver_name', 'driver_phone', 'guide_name', 'guide_phone', 'vehicle_info', 'internal_notes'],
                fn ($col) => Schema::hasColumn('bookings', $col)
            );
            if (!empty($toDrop)) {
                $table->dropColumn(array_values($toDrop));
            }
        });
    }
};
