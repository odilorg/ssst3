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
        Schema::table('transport_instance_prices', function (Blueprint $table) {
            // Add unique constraint on (transport_id, price_type)
            // This prevents duplicate price types for the same transport
            $table->unique(['transport_id', 'price_type'], 'transport_price_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_instance_prices', function (Blueprint $table) {
            $table->dropUnique('transport_price_type_unique');
        });
    }
};
