<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Booking source tracking
            $table->string('source', 50)->default('direct')->after('id'); // direct, gyg, viator, klook, manual
            $table->string('external_reference')->nullable()->after('source'); // OTA booking ID
            $table->json('external_platform_data')->nullable()->after('external_reference'); // Raw parsed data
            
            // Track when imported from OTA
            $table->timestamp('imported_at')->nullable()->after('external_platform_data');
            $table->string('imported_from_email_id')->nullable()->after('imported_at'); // Gmail message ID
            
            // Index for duplicate checking
            $table->index(['source', 'external_reference'], 'booking_external_ref');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('booking_external_ref');
            $table->dropColumn([
                'source',
                'external_reference',
                'external_platform_data',
                'imported_at',
                'imported_from_email_id',
            ]);
        });
    }
};
