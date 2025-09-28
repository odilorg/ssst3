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
        Schema::create('transport_prices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('transport_type_id')->constrained()->onDelete('cascade');
            $table->string('price_type');
            $table->decimal('cost', 8, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_prices');
    }
};
