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
        Schema::table('cities', function (Blueprint $table) {
            // URL and SEO fields
            $table->string('slug')->unique()->after('name')->index();
            $table->string('tagline')->nullable()->after('slug');
            
            // Images
            $table->string('featured_image')->nullable()->after('images');
            $table->string('hero_image')->nullable()->after('featured_image');
            
            // Descriptions
            $table->text('short_description')->nullable()->after('description');
            $table->text('long_description')->nullable()->after('short_description');
            
            // Location coordinates
            $table->decimal('latitude', 10, 6)->nullable()->after('long_description');
            $table->decimal('longitude', 10, 6)->nullable()->after('latitude');
            
            // Display settings
            $table->integer('display_order')->default(0)->after('longitude')->index();
            $table->boolean('is_featured')->default(false)->after('display_order')->index();
            $table->boolean('is_active')->default(true)->after('is_featured')->index();
            
            // SEO fields
            $table->string('meta_title')->nullable()->after('is_active');
            $table->text('meta_description')->nullable()->after('meta_title');
            
            // Performance cache
            $table->integer('tour_count_cache')->default(0)->after('meta_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn([
                'slug', 'tagline', 'featured_image', 'hero_image',
                'short_description', 'long_description',
                'latitude', 'longitude',
                'display_order', 'is_featured', 'is_active',
                'meta_title', 'meta_description', 'tour_count_cache'
            ]);
        });
    }
};
