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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reviewer_name');
            $table->string('reviewer_email')->nullable();
            $table->string('reviewer_location')->nullable()->comment('e.g., "London, UK"');
            $table->tinyInteger('rating')->comment('1-5 stars');
            $table->string('title')->nullable();
            $table->text('content');
            $table->string('avatar_url', 500)->nullable();
            $table->string('source', 50)->default('website')->comment('website, tripadvisor, google, etc.');
            $table->boolean('is_verified')->default(false)->comment('Linked to confirmed booking');
            $table->boolean('is_approved')->default(false)->comment('Moderation flag');
            $table->timestamps();

            // Indexes
            $table->index(['tour_id', 'is_approved', 'created_at']);
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
