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
        Schema::table('leads', function (Blueprint $table) {
            // Uzbekistan Partnership Tracking
            $table->boolean('has_uzbekistan_partner')->default(false)->after('certifications');
            $table->string('uzbekistan_partner_name')->nullable()->after('has_uzbekistan_partner');
            $table->enum('uzbekistan_partnership_status', [
                'active',       // Currently working together
                'inactive',     // Not working currently
                'expired',      // Contract expired
                'seasonal',     // Seasonal partnership
                'pending',      // New partnership pending
            ])->nullable()->after('uzbekistan_partner_name');
            $table->text('uzbekistan_partnership_notes')->nullable()->after('uzbekistan_partnership_status');

            // Company Working Status
            $table->enum('working_status', [
                'active',           // Currently operational
                'inactive',         // Not operating
                'seasonal',         // Seasonal operation
                'temporary_pause',  // Temporarily paused
                'unknown',          // Status unknown
            ])->default('active')->after('uzbekistan_partnership_notes');

            // Add index for filtering
            $table->index('has_uzbekistan_partner');
            $table->index('working_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['has_uzbekistan_partner']);
            $table->dropIndex(['working_status']);

            $table->dropColumn([
                'has_uzbekistan_partner',
                'uzbekistan_partner_name',
                'uzbekistan_partnership_status',
                'uzbekistan_partnership_notes',
                'working_status',
            ]);
        });
    }
};
