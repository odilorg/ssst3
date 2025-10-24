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
        Schema::table('drivers', function (Blueprint $table) {
            // Add license categories (Uzbek DL has A, B, C, D, E categories and their subcategories)
            $table->json('license_categories')->nullable()->after('license_number');

            // Add city relationship
            $table->foreignId('city_id')->nullable()->after('address')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropColumn(['license_categories', 'city_id']);
        });
    }
};
