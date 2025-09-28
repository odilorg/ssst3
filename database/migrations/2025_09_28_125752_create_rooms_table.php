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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->decimal('cost_per_night', 8, 2);
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->json('images')->nullable();
            $table->string('image')->nullable();
            $table->decimal('room_size', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
