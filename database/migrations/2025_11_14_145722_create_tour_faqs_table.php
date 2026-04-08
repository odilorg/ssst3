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
        // tour_faqs was created in 2025_10_29_064515. This migration extends it
        // with is_active and the updated composite index. Guard for idempotency.
        if (!Schema::hasTable('tour_faqs')) {
            Schema::create('tour_faqs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tour_id')->constrained()->onDelete('cascade');
                $table->string('question');
                $table->text('answer');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            Schema::table('tour_faqs', function (Blueprint $table) {
                if (!Schema::hasColumn('tour_faqs', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('sort_order');
                }
            });
        }

        // Add the composite index if not present
        $hasIndex = collect(Schema::getIndexes('tour_faqs'))->contains(
            fn ($idx) => $idx['columns'] === ['tour_id', 'is_active', 'sort_order']
        );
        if (!$hasIndex) {
            Schema::table('tour_faqs', function (Blueprint $table) {
                $table->index(['tour_id', 'is_active', 'sort_order']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tour_faqs', function (Blueprint $table) {
            if (Schema::hasColumn('tour_faqs', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
