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
        Schema::create('contract_service_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_service_id')->constrained()->onDelete('cascade');
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->json('price_data'); // Flexible structure: {rooms: {1: 50, 2: 80}} or {meal_types: {1: 25}}
            $table->string('amendment_number')->nullable(); // e.g., "Доп. соглашение №1"
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['contract_service_id', 'effective_from']);
            $table->index(['effective_from', 'effective_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_service_prices');
    }
};
