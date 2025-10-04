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
        Schema::table('guide_spoken_language', function (Blueprint $table) {
            $table->enum('proficiency_level', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2', 'Native'])->nullable()->after('spoken_language_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_spoken_language', function (Blueprint $table) {
            $table->dropColumn('proficiency_level');
        });
    }
};
