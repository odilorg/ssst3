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
        Schema::create('tour_categories', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Translatable: {"en": "Cultural & Historical", "ru": "...", "fr": "..."}
            $table->string('slug')->unique();
            $table->json('description')->nullable(); // Translatable
            $table->string('icon')->nullable(); // Font Awesome class or emoji
            $table->string('image_path')->nullable(); // Background image for card
            $table->string('hero_image')->nullable(); // Hero image for landing page
            $table->integer('display_order')->default(0); // Order on homepage
            $table->boolean('is_active')->default(true); // Show/hide category
            $table->boolean('show_on_homepage')->default(false); // Show in homepage section (limit to 6)
            $table->json('meta_title')->nullable(); // SEO - translatable
            $table->json('meta_description')->nullable(); // SEO - translatable
            $table->timestamps();
            $table->softDeletes(); // Soft delete instead of hard delete
        });

        // Pivot table for many-to-many relationship
        Schema::create('tour_category_tour', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->foreignId('tour_category_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate assignments
            $table->unique(['tour_id', 'tour_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_category_tour');
        Schema::dropIfExists('tour_categories');
    }
};
