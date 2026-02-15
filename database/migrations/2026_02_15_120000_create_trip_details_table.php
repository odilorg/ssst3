<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();

            // Accommodation (mini + long tours)
            $table->string('hotel_name')->nullable();
            $table->string('hotel_address')->nullable();

            // Communication (mini + long tours)
            $table->string('whatsapp_number')->nullable();

            // Travel logistics (long tours only)
            $table->date('arrival_date')->nullable();
            $table->string('arrival_flight')->nullable();
            $table->string('arrival_time')->nullable();
            $table->date('departure_date')->nullable();
            $table->string('departure_flight')->nullable();
            $table->string('departure_time')->nullable();

            // Preferences
            $table->string('language_preference')->nullable();

            // Marketing attribution
            $table->string('referral_source')->nullable();

            // Additional info
            $table->text('additional_notes')->nullable();

            // Tracking
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_details');
    }
};
