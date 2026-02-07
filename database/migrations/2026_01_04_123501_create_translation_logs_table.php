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
        Schema::create('translation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained(); // Admin who triggered translation
            $table->string('source_locale', 10)->index();
            $table->string('target_locale', 10)->index();
            $table->json('sections_translated'); // ['title', 'content', 'highlights_json', ...]
            $table->integer('tokens_used')->unsigned();
            $table->decimal('cost_usd', 8, 4);
            $table->string('model', 50); // gpt-4-turbo, gpt-3.5-turbo
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Indexes for querying usage stats
            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_logs');
    }
};
