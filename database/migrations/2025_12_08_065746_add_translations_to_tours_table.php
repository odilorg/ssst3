<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add new JSON columns for translations
        Schema::table('tours', function (Blueprint $table) {
            $table->json('title_translations')->nullable()->after('title');
            $table->json('short_description_translations')->nullable()->after('short_description');
            $table->json('long_description_translations')->nullable()->after('long_description');
            $table->json('seo_title_translations')->nullable()->after('seo_title');
            $table->json('seo_description_translations')->nullable()->after('seo_description');
            $table->json('seo_keywords_translations')->nullable()->after('seo_keywords');
        });

        // Migrate existing data to Russian (ru) since current content is in Russian
        DB::table('tours')->get()->each(function ($tour) {
            DB::table('tours')
                ->where('id', $tour->id)
                ->update([
                    'title_translations' => json_encode(['ru' => $tour->title]),
                    'short_description_translations' => json_encode(['ru' => $tour->short_description]),
                    'long_description_translations' => json_encode(['ru' => $tour->long_description]),
                    'seo_title_translations' => json_encode(['ru' => $tour->seo_title]),
                    'seo_description_translations' => json_encode(['ru' => $tour->seo_description]),
                    'seo_keywords_translations' => json_encode(['ru' => $tour->seo_keywords]),
                ]);
        });

        // Drop old columns after data migration
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'short_description',
                'long_description',
                'seo_title',
                'seo_description',
                'seo_keywords'
            ]);
        });

        // Rename translation columns to original names
        Schema::table('tours', function (Blueprint $table) {
            $table->renameColumn('title_translations', 'title');
            $table->renameColumn('short_description_translations', 'short_description');
            $table->renameColumn('long_description_translations', 'long_description');
            $table->renameColumn('seo_title_translations', 'seo_title');
            $table->renameColumn('seo_description_translations', 'seo_description');
            $table->renameColumn('seo_keywords_translations', 'seo_keywords');
        });
    }

    public function down(): void
    {
        // Reverse: Convert JSON back to single language strings
        Schema::table('tours', function (Blueprint $table) {
            $table->renameColumn('title', 'title_json');
            $table->renameColumn('short_description', 'short_description_json');
            $table->renameColumn('long_description', 'long_description_json');
            $table->renameColumn('seo_title', 'seo_title_json');
            $table->renameColumn('seo_description', 'seo_description_json');
            $table->renameColumn('seo_keywords', 'seo_keywords_json');
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->text('title')->nullable();
            $table->text('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
        });

        // Extract Russian text from JSON
        DB::table('tours')->get()->each(function ($tour) {
            $title = json_decode($tour->title_json, true);
            $shortDesc = json_decode($tour->short_description_json, true);
            $longDesc = json_decode($tour->long_description_json, true);
            $seoTitle = json_decode($tour->seo_title_json, true);
            $seoDesc = json_decode($tour->seo_description_json, true);
            $seoKeys = json_decode($tour->seo_keywords_json, true);

            DB::table('tours')
                ->where('id', $tour->id)
                ->update([
                    'title' => $title['ru'] ?? $title['en'] ?? '',
                    'short_description' => $shortDesc['ru'] ?? $shortDesc['en'] ?? '',
                    'long_description' => $longDesc['ru'] ?? $longDesc['en'] ?? '',
                    'seo_title' => $seoTitle['ru'] ?? $seoTitle['en'] ?? '',
                    'seo_description' => $seoDesc['ru'] ?? $seoDesc['en'] ?? '',
                    'seo_keywords' => $seoKeys['ru'] ?? $seoKeys['en'] ?? '',
                ]);
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'title_json',
                'short_description_json',
                'long_description_json',
                'seo_title_json',
                'seo_description_json',
                'seo_keywords_json'
            ]);
        });
    }
};
