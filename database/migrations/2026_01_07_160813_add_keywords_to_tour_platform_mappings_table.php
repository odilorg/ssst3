<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_platform_mappings', function (Blueprint $table) {
            $table->json('keywords')->nullable()->after('external_tour_name')
                  ->comment('Keywords for fuzzy matching (e.g., ["shahrisabz", "konigil"])');
            $table->unsignedTinyInteger('match_confidence')->default(100)->after('keywords')
                  ->comment('Confidence threshold for auto-matching (0-100)');
        });
    }

    public function down(): void
    {
        Schema::table('tour_platform_mappings', function (Blueprint $table) {
            $table->dropColumn(['keywords', 'match_confidence']);
        });
    }
};
