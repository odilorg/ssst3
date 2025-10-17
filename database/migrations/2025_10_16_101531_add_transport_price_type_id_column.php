<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column exists first
        $hasColumn = DB::select("
            SELECT COLUMN_NAME 
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'booking_itinerary_item_assignments' 
            AND COLUMN_NAME = 'transport_price_type_id'
        ");

        if (empty($hasColumn)) {
            Schema::table('booking_itinerary_item_assignments', function (Blueprint $table) {
                $table->unsignedBigInteger('transport_price_type_id')->nullable()->after('meal_type_id');
            });
        }

        // Check if foreign key exists
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'booking_itinerary_item_assignments' 
            AND CONSTRAINT_NAME = 'booking_assignment_transport_price_fk'
        ");

        if (empty($foreignKeys)) {
            Schema::table('booking_itinerary_item_assignments', function (Blueprint $table) {
                $table->foreign('transport_price_type_id', 'booking_assignment_transport_price_fk')
                    ->references('id')
                    ->on('transport_prices')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_itinerary_item_assignments', function (Blueprint $table) {
            $table->dropForeign('booking_assignment_transport_price_fk');
            $table->dropColumn('transport_price_type_id');
        });
    }
};


