<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            
            // Basic Info
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->text('short_description')->nullable();
            $table->text('long_description')->nullable();
            
            // Master/Artisan Info
            $table->string('master_name');
            $table->string('master_title')->nullable(); // e.g., "7th Generation Potter"
            $table->text('master_bio')->nullable();
            $table->string('master_image')->nullable();
            
            // Craft Info
            $table->string('craft_type'); // ceramics, textiles, woodwork, etc.
            $table->string('craft_tradition')->nullable(); // e.g., "Gijduvan Blue"
            $table->json('craft_highlights')->nullable(); // What makes it special
            
            // Location
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('address')->nullable();
            $table->string('location_description')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Duration & Schedule
            $table->string('duration_text')->nullable(); // "2-3 hours"
            $table->integer('duration_minutes')->nullable();
            $table->string('operating_hours')->nullable(); // "Mon-Fri, 8AM-6PM"
            $table->integer('advance_booking_days')->default(2);
            
            // Capacity
            $table->integer('min_guests')->default(1);
            $table->integer('max_guests')->default(8);
            $table->string('group_size_text')->nullable(); // "Intimate groups of up to 8"
            
            // Pricing
            $table->decimal('price_per_person', 10, 2)->nullable();
            $table->decimal('private_session_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->text('pricing_notes')->nullable();
            
            // Content (JSON arrays)
            $table->json('what_you_will_do')->nullable(); // Step-by-step experience
            $table->json('included_items')->nullable();
            $table->json('excluded_items')->nullable();
            $table->json('who_is_it_for')->nullable(); // Target audience tags
            $table->json('practical_info')->nullable();
            $table->json('faqs')->nullable();
            $table->json('languages')->nullable(); // ["en", "ru", "uz"]
            
            // Related Tours/Journeys
            $table->json('related_tour_ids')->nullable();
            
            // Images
            $table->string('hero_image')->nullable();
            $table->json('gallery_images')->nullable();
            
            // SEO
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('og_image')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            
            // Ratings
            $table->decimal('rating', 2, 1)->nullable();
            $table->integer('review_count')->default(0);
            
            // Accommodation option
            $table->boolean('has_guesthouse')->default(false);
            $table->text('guesthouse_description')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('craft_type');
            $table->index('is_active');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshops');
    }
};
