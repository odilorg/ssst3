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
            $table->foreignId('contract_service_id')
                ->nullable()
                ->after('assignable_id')
                ->constrained('contract_services')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_itinerary_item_assignments', function (Blueprint $table) {
            $table->dropForeign(['contract_service_id']);
            $table->dropColumn('contract_service_id');
        });
    }
};
