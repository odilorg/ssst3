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
        Schema::create('tour_departures', function (Blueprint $table) {
            $table->id();

            // Foreign key to tours
            $table->foreignId('tour_id')
                ->constrained('tours')
                ->onDelete('cascade')
                ->comment('Tour this departure belongs to');

            // Departure dates
            $table->date('start_date')
                ->comment('Departure start date');

            $table->date('end_date')
                ->comment('Departure end date');

            // Capacity management
            $table->unsignedInteger('max_pax')
                ->default(12)
                ->comment('Maximum number of participants');

            $table->unsignedInteger('booked_pax')
                ->default(0)
                ->comment('Current number of booked participants');

            $table->unsignedInteger('min_pax')
                ->nullable()
                ->comment('Minimum participants to guarantee departure (null = no minimum)');

            // Pricing override
            $table->decimal('price_per_person', 10, 2)
                ->nullable()
                ->comment('Override tour base price if set');

            // Status
            $table->enum('status', [
                'open',       // Accepting bookings
                'guaranteed', // Minimum reached, departure confirmed
                'full',       // Maximum capacity reached
                'completed',  // Tour has finished
                'cancelled'   // Departure cancelled
            ])->default('open')
                ->comment('Departure status');

            // Departure type
            $table->enum('departure_type', ['group', 'private'])
                ->default('group')
                ->comment('Type of departure');

            // Additional information
            $table->text('notes')
                ->nullable()
                ->comment('Admin notes about this departure');

            $table->timestamps();

            // Indexes for performance
            $table->index(['tour_id', 'start_date'], 'idx_tour_date');
            $table->index('status', 'idx_status');
            $table->index('start_date', 'idx_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_departures');
    }
};
