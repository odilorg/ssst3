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
        Schema::table('itinerary_items', function (Blueprint $table) {
            // Multi-day tour fields
            $table->unsignedInteger('day_number')->nullable()->after('meta');
            $table->foreignId('city_id')->nullable()->after('day_number')->constrained()->onDelete('set null');
            $table->string('meals', 255)->nullable()->after('city_id');
            $table->string('accommodation', 255)->nullable()->after('meals');
            $table->string('transport', 255)->nullable()->after('accommodation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itinerary_items', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropColumn(['day_number', 'city_id', 'meals', 'accommodation', 'transport']);
        });
    }
};
