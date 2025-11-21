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
        // Add WebP fields to tours table
        Schema::table('tours', function (Blueprint $table) {
            $table->string('hero_image_webp')->nullable()->after('hero_image');
            $table->json('hero_image_sizes')->nullable()->after('hero_image_webp')
                ->comment('Stores paths for different responsive sizes: thumb, medium, large, xlarge');
            $table->enum('image_processing_status', ['pending', 'processing', 'completed', 'failed'])
                ->default('pending')
                ->after('hero_image_sizes')
                ->comment('WebP conversion status');
            $table->text('image_processing_error')->nullable()->after('image_processing_status');

            // Index for querying images that need processing
            $table->index('image_processing_status');
        });

        // Add WebP fields to blog_posts table
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('featured_image_webp')->nullable()->after('featured_image');
            $table->json('featured_image_sizes')->nullable()->after('featured_image_webp')
                ->comment('Stores paths for different responsive sizes: thumb, medium, large, xlarge');
            $table->enum('image_processing_status', ['pending', 'processing', 'completed', 'failed'])
                ->default('pending')
                ->after('featured_image_sizes')
                ->comment('WebP conversion status');
            $table->text('image_processing_error')->nullable()->after('image_processing_status');

            $table->index('image_processing_status');
        });

        // Add WebP fields to cities table
        Schema::table('cities', function (Blueprint $table) {
            $table->string('hero_image_webp')->nullable()->after('hero_image');
            $table->json('hero_image_sizes')->nullable()->after('hero_image_webp')
                ->comment('Stores paths for different responsive sizes: thumb, medium, large, xlarge');
            $table->enum('image_processing_status', ['pending', 'processing', 'completed', 'failed'])
                ->default('pending')
                ->after('hero_image_sizes')
                ->comment('WebP conversion status');
            $table->text('image_processing_error')->nullable()->after('image_processing_status');

            $table->index('image_processing_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove WebP fields from tours table
        Schema::table('tours', function (Blueprint $table) {
            $table->dropIndex(['image_processing_status']);
            $table->dropColumn([
                'hero_image_webp',
                'hero_image_sizes',
                'image_processing_status',
                'image_processing_error'
            ]);
        });

        // Remove WebP fields from blog_posts table
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropIndex(['image_processing_status']);
            $table->dropColumn([
                'featured_image_webp',
                'featured_image_sizes',
                'image_processing_status',
                'image_processing_error'
            ]);
        });

        // Remove WebP fields from cities table
        Schema::table('cities', function (Blueprint $table) {
            $table->dropIndex(['image_processing_status']);
            $table->dropColumn([
                'hero_image_webp',
                'hero_image_sizes',
                'image_processing_status',
                'image_processing_error'
            ]);
        });
    }
};
