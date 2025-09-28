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
        Schema::create('itinerary_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('itinerary_items')->onDelete('cascade');
            $table->enum('type', ['day', 'stop']);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('title');
            $table->longText('description')->nullable();
            $table->time('default_start_time')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->json('meta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_items');
    }
};
