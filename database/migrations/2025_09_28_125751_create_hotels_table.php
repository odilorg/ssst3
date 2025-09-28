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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->enum('category', ['bed_breakfast', '3_star', '4_star', '5_star']);
            $table->enum('type', ['bed_breakfast', '3_star', '4_star', '5_star']);
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('phone');
            $table->string('email');
            $table->json('images')->nullable();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
