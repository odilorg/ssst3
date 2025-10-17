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
        Schema::table('booking_itinerary_item_assignments', function (Blueprint $table) {
            $table->foreign('transport_instance_price_id', 'booking_assignment_transport_instance_price_fk')
                ->references('id')
                ->on('transport_instance_prices')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_itinerary_item_assignments', function (Blueprint $table) {
            $table->dropForeign('booking_assignment_transport_instance_price_fk');
        });
    }
};
