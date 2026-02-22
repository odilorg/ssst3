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
        Schema::table('tour_faqs', function (Blueprint $table) {
            $table->text('question_old')->nullable()->default(null)->change();
            $table->text('answer_old')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tour_faqs', function (Blueprint $table) {
            $table->text('question_old')->nullable(false)->change();
            $table->text('answer_old')->nullable(false)->change();
        });
    }
};
