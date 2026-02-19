<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adds booking_type column to separate private vs group pricing tiers.
     * Existing tiers default to 'private' (preserves current behavior).
     */
    public function up(): void
    {
        // Add booking_type column (idempotent check for partial runs)
        if (!Schema::hasColumn('tour_pricing_tiers', 'booking_type')) {
            Schema::table('tour_pricing_tiers', function (Blueprint $table) {
                $table->string('booking_type', 10)->default('private')->after('tour_id');
            });
        }

        // All existing tiers were used for private pricing
        DB::table('tour_pricing_tiers')
            ->whereNull('booking_type')
            ->orWhere('booking_type', '')
            ->update(['booking_type' => 'private']);

        // Add new compound indexes (keep old ones - FK constraint prevents dropping)
        Schema::table('tour_pricing_tiers', function (Blueprint $table) {
            $table->index(['tour_id', 'booking_type', 'is_active'], 'tpt_tour_type_active_idx');
            $table->index(['tour_id', 'booking_type', 'min_guests', 'max_guests'], 'tpt_tour_type_guests_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tour_pricing_tiers', function (Blueprint $table) {
            $table->dropIndex('tpt_tour_type_active_idx');
            $table->dropIndex('tpt_tour_type_guests_idx');
            $table->dropColumn('booking_type');
        });
    }
};
