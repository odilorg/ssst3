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
        Schema::table('tours', function (Blueprint $table) {
            // SEO Meta Tags - Check if columns exist before adding
            if (!Schema::hasColumn('tours', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('title')
                    ->comment('Custom title tag for SEO (max 60 chars recommended)');
            }

            if (!Schema::hasColumn('tours', 'seo_description')) {
                $table->string('seo_description', 160)->nullable()->after('title')
                    ->comment('Meta description for search results (max 160 chars)');
            }

            if (!Schema::hasColumn('tours', 'seo_keywords')) {
                $table->text('seo_keywords')->nullable()->after('title')
                    ->comment('Comma-separated keywords for SEO');
            }

            // Open Graph / Social Media
            if (!Schema::hasColumn('tours', 'og_image')) {
                $table->string('og_image')->nullable()->after('hero_image')
                    ->comment('Custom image for social media sharing (if different from hero_image)');
            }

            // Schema.org / Structured Data
            if (!Schema::hasColumn('tours', 'schema_enabled')) {
                $table->boolean('schema_enabled')->default(true)->after('is_active')
                    ->comment('Enable/disable Schema.org structured data output');
            }

            if (!Schema::hasColumn('tours', 'schema_override')) {
                $table->json('schema_override')->nullable()->after('is_active')
                    ->comment('Custom schema.org data to override auto-generated schema');
            }

            // Note: WebP fields already exist from 2025_11_20_140001_add_webp_fields_to_tables migration
            // - hero_image_webp
            // - hero_image_sizes
            // - image_processing_status
            // - image_processing_error
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'seo_title',
                'seo_description',
                'seo_keywords',
                'og_image',
                'schema_enabled',
                'schema_override',
            ]);
        });
    }
};
