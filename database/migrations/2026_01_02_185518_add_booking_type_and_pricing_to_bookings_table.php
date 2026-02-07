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
        Schema::table('bookings', function (Blueprint $table) {
            // Booking type (private or group)
            $table->enum('type', ['private', 'group'])->default('private')->after('tour_id');

            // Group departure reference (nullable for private bookings)
            $table->foreignId('group_departure_id')->nullable()->after('type')->constrained('tour_departures')->onDelete('set null');

            // Pricing breakdown
            $table->decimal('price_per_person', 10, 2)->nullable()->after('total_price');
            $table->unsignedSmallInteger('guests_count')->default(1)->after('pax_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['group_departure_id']);
            $table->dropColumn([
                'type',
                'group_departure_id',
                'price_per_person',
                'guests_count',
            ]);
        });
    }
};
