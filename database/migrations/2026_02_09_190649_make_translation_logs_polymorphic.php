<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('translation_logs', function (Blueprint $table) {
            // Drop FK constraint on tour_id (Laravel naming convention)
            $table->dropForeign(['tour_id']);

            // Make tour_id nullable (legacy column, kept for backward compat)
            $table->unsignedBigInteger('tour_id')->nullable()->change();

            // Add polymorphic columns
            $table->string('translatable_type')->nullable()->after('id');
            $table->unsignedBigInteger('translatable_id')->nullable()->after('translatable_type');

            $table->index(['translatable_type', 'translatable_id']);
        });

        // Backfill existing rows: set morph fields from tour_id
        DB::table('translation_logs')
            ->whereNotNull('tour_id')
            ->update([
                'translatable_type' => 'App\\Models\\Tour',
                'translatable_id' => DB::raw('tour_id'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore tour_id from morph fields
        DB::table('translation_logs')
            ->where('translatable_type', 'App\\Models\\Tour')
            ->update([
                'tour_id' => DB::raw('translatable_id'),
            ]);

        Schema::table('translation_logs', function (Blueprint $table) {
            $table->dropIndex(['translatable_type', 'translatable_id']);
            $table->dropColumn(['translatable_type', 'translatable_id']);

            // Restore tour_id as NOT NULL with FK
            $table->unsignedBigInteger('tour_id')->nullable(false)->change();
            $table->foreign('tour_id')->references('id')->on('tours')->cascadeOnDelete();
        });
    }
};
