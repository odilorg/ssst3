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
        Schema::create('booking_travelers', function (Blueprint $table) {
            $table->id();

            // Foreign key to booking
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->onDelete('cascade')
                ->comment('Booking this traveler belongs to');

            // Personal information
            $table->string('full_name', 255)
                ->comment('Traveler full name (as per passport)');

            $table->date('date_of_birth')
                ->nullable()
                ->comment('Date of birth');

            $table->string('nationality', 100)
                ->nullable()
                ->comment('Nationality');

            // Passport details (for international tours, visas, tickets)
            $table->string('passport_number', 50)
                ->nullable()
                ->comment('Passport number');

            $table->date('passport_expiry')
                ->nullable()
                ->comment('Passport expiration date');

            // Special requirements
            $table->text('dietary_requirements')
                ->nullable()
                ->comment('Food allergies, vegetarian, halal, etc.');

            $table->text('special_needs')
                ->nullable()
                ->comment('Mobility issues, medical conditions, etc.');

            $table->timestamps();

            // Index for queries
            $table->index('booking_id', 'idx_booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_travelers');
    }
};
