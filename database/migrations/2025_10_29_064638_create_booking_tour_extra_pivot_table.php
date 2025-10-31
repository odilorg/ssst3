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
        Schema::create('booking_tour_extra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('tour_extra_id')->constrained()->onDelete('cascade');
            $table->decimal('price_at_booking', 10, 2)->comment('Lock price at time of booking');
            $table->integer('quantity')->default(1);
            $table->timestamps();

            // Prevent duplicate extras in same booking
            $table->unique(['booking_id', 'tour_extra_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_tour_extra');
    }
};
