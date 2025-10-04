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
        Schema::table('guides', function (Blueprint $table) {
            if (Schema::hasColumn('guides', 'language')) {
                $table->dropColumn('language');
            }
            if (Schema::hasColumn('guides', 'daily_rate')) {
                $table->dropColumn('daily_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guides', function (Blueprint $table) {
            $table->string('language')->nullable();
            $table->decimal('daily_rate', 10, 2)->nullable();
        });
    }
};
