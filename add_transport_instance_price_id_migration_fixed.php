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
            $table->foreignId('transport_instance_price_id')->nullable()->after('transport_price_type_id')
                ->constrained('transport_instance_prices', 'id', 'booking_assignment_transport_instance_price_fk')
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
            $table->dropColumn('transport_instance_price_id');
        });
    }
};
