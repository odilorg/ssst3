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
        Schema::create('booking_itinerary_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('tour_itinerary_item_id')->nullable()->constrained('itinerary_items')->onDelete('cascade');
            $table->date('date');
            $table->enum('type', ['day', 'stop']);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('title');
            $table->longText('description')->nullable();
            $table->time('planned_start_time')->nullable();
            $table->unsignedInteger('planned_duration_minutes')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('is_custom')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->enum('status', ['planned', 'confirmed', 'completed', 'cancelled'])->default('planned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_itinerary_items');
    }
};
