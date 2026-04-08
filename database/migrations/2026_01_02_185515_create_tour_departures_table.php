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
        if (Schema::hasTable('tour_departures')) {
            return;
        }

        Schema::create('tour_departures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedSmallInteger('max_pax')->default(20);
            $table->unsignedSmallInteger('booked_pax')->default(0);
            $table->unsignedSmallInteger('min_pax')->default(1);
            $table->decimal('price_per_person', 10, 2)->nullable();
            $table->enum('status', ['open', 'guaranteed', 'full', 'completed', 'cancelled'])->default('open');
            $table->enum('departure_type', ['group', 'private'])->default('group');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tour_id', 'status']);
            $table->index(['start_date', 'status']);
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
