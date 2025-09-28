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
        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('plate_number')->nullable();
            $table->string('model')->nullable();
            $table->integer('number_of_seat')->nullable();
            $table->enum('category', ['bus', 'car', 'mikro_bus', 'mini_van', 'air', 'rail']);
            $table->foreignId('transport_type_id')->constrained()->onDelete('cascade');
            $table->time('departure_time')->nullable();
            $table->time('arrival_time')->nullable();
            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('set null');
            $table->json('images')->nullable();
            $table->enum('fuel_type', ['diesel', 'benzin/propane', 'natural_gaz'])->nullable();
            $table->integer('oil_change_interval_months')->nullable();
            $table->integer('oil_change_interval_km')->nullable();
            $table->decimal('fuel_consumption', 8, 2)->nullable();
            $table->decimal('fuel_remaining_liter', 8, 2)->nullable();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transports');
    }
};
