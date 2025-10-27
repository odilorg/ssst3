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
        Schema::create('lead_enrichments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('action_id')->nullable()->constrained('lead_ai_actions')->onDelete('set null');
            $table->json('fields_before')->comment('Snapshot before enrichment');
            $table->json('fields_after')->comment('Snapshot after enrichment');
            $table->json('fields_changed')->comment('Array of field names that changed');
            $table->text('ai_insights')->nullable()->comment('Additional insights from AI');
            $table->enum('source', ['website_analysis', 'manual_ai', 'auto_enrich'])->default('website_analysis');
            $table->timestamps();

            $table->index(['lead_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_enrichments');
    }
};
