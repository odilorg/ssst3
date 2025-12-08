<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Language;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Convert existing tour text fields to JSON format for translations
     */
    public function up(): void
    {
        // Get default language
        $defaultLanguage = Language::getDefault();
        $defaultLocale = $defaultLanguage ? $defaultLanguage->code : 'en';

        // Change translatable columns to JSON type
        Schema::table('tours', function (Blueprint $table) {
            $table->json('title')->change();
            $table->json('short_description')->nullable()->change();
            $table->json('long_description')->nullable()->change();
            $table->json('seo_title')->nullable()->change();
            $table->json('seo_description')->nullable()->change();
            $table->json('seo_keywords')->nullable()->change();
            $table->json('meeting_point_address')->nullable()->change();
            $table->json('meeting_instructions')->nullable()->change();
            $table->json('cancellation_policy')->nullable()->change();
        });

        // Convert existing string data to JSON format with default locale
        $tours = DB::table('tours')->get();

        foreach ($tours as $tour) {
            $updates = [];

            // For each translatable field, convert to JSON with default locale
            $translatableFields = [
                'title',
                'short_description',
                'long_description',
                'seo_title',
                'seo_description',
                'seo_keywords',
                'meeting_point_address',
                'meeting_instructions',
                'cancellation_policy',
            ];

            foreach ($translatableFields as $field) {
                if (!empty($tour->$field)) {
                    // Convert to JSON format: {"en": "value"}
                    $updates[$field] = json_encode([$defaultLocale => $tour->$field]);
                }
            }

            if (!empty($updates)) {
                DB::table('tours')
                    ->where('id', $tour->id)
                    ->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * Convert JSON fields back to text
     */
    public function down(): void
    {
        // Get default language
        $defaultLanguage = Language::getDefault();
        $defaultLocale = $defaultLanguage ? $defaultLanguage->code : 'en';

        // Extract default locale values from JSON
        $tours = DB::table('tours')->get();

        foreach ($tours as $tour) {
            $updates = [];

            $translatableFields = [
                'title',
                'short_description',
                'long_description',
                'seo_title',
                'seo_description',
                'seo_keywords',
                'meeting_point_address',
                'meeting_instructions',
                'cancellation_policy',
            ];

            foreach ($translatableFields as $field) {
                if (!empty($tour->$field)) {
                    $decoded = json_decode($tour->$field, true);
                    $updates[$field] = $decoded[$defaultLocale] ?? null;
                }
            }

            if (!empty($updates)) {
                DB::table('tours')
                    ->where('id', $tour->id)
                    ->update($updates);
            }
        }

        // Change columns back to text/string type
        Schema::table('tours', function (Blueprint $table) {
            $table->string('title')->change();
            $table->text('short_description')->nullable()->change();
            $table->longText('long_description')->nullable()->change();
            $table->string('seo_title')->nullable()->change();
            $table->text('seo_description')->nullable()->change();
            $table->string('seo_keywords')->nullable()->change();
            $table->string('meeting_point_address')->nullable()->change();
            $table->text('meeting_instructions')->nullable()->change();
            $table->text('cancellation_policy')->nullable()->change();
        });
    }
};
