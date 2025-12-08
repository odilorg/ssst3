<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->json('name_translations')->nullable()->after('name');
            $table->json('tagline_translations')->nullable()->after('tagline');
            $table->json('description_translations')->nullable()->after('description');
            $table->json('short_description_translations')->nullable()->after('short_description');
            $table->json('long_description_translations')->nullable()->after('long_description');
            $table->json('meta_title_translations')->nullable()->after('meta_title');
            $table->json('meta_description_translations')->nullable()->after('meta_description');
        });

        DB::table('cities')->get()->each(function ($city) {
            DB::table('cities')->where('id', $city->id)->update([
                'name_translations' => json_encode(['ru' => $city->name]),
                'tagline_translations' => json_encode(['ru' => $city->tagline]),
                'description_translations' => json_encode(['ru' => $city->description]),
                'short_description_translations' => json_encode(['ru' => $city->short_description]),
                'long_description_translations' => json_encode(['ru' => $city->long_description]),
                'meta_title_translations' => json_encode(['ru' => $city->meta_title]),
                'meta_description_translations' => json_encode(['ru' => $city->meta_description]),
            ]);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['name', 'tagline', 'description', 'short_description', 'long_description', 'meta_title', 'meta_description']);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->renameColumn('name_translations', 'name');
            $table->renameColumn('tagline_translations', 'tagline');
            $table->renameColumn('description_translations', 'description');
            $table->renameColumn('short_description_translations', 'short_description');
            $table->renameColumn('long_description_translations', 'long_description');
            $table->renameColumn('meta_title_translations', 'meta_title');
            $table->renameColumn('meta_description_translations', 'meta_description');
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->renameColumn('name', 'name_json');
            $table->renameColumn('tagline', 'tagline_json');
            $table->renameColumn('description', 'description_json');
            $table->renameColumn('short_description', 'short_description_json');
            $table->renameColumn('long_description', 'long_description_json');
            $table->renameColumn('meta_title', 'meta_title_json');
            $table->renameColumn('meta_description', 'meta_description_json');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
        });

        DB::table('cities')->get()->each(function ($city) {
            $name = json_decode($city->name_json, true);
            $tagline = json_decode($city->tagline_json, true);
            $desc = json_decode($city->description_json, true);
            $shortDesc = json_decode($city->short_description_json, true);
            $longDesc = json_decode($city->long_description_json, true);
            $metaTitle = json_decode($city->meta_title_json, true);
            $metaDesc = json_decode($city->meta_description_json, true);

            DB::table('cities')->where('id', $city->id)->update([
                'name' => $name['ru'] ?? $name['en'] ?? '',
                'tagline' => $tagline['ru'] ?? $tagline['en'] ?? '',
                'description' => $desc['ru'] ?? $desc['en'] ?? '',
                'short_description' => $shortDesc['ru'] ?? $shortDesc['en'] ?? '',
                'long_description' => $longDesc['ru'] ?? $longDesc['en'] ?? '',
                'meta_title' => $metaTitle['ru'] ?? $metaTitle['en'] ?? '',
                'meta_description' => $metaDesc['ru'] ?? $metaDesc['en'] ?? '',
            ]);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['name_json', 'tagline_json', 'description_json', 'short_description_json', 'long_description_json', 'meta_title_json', 'meta_description_json']);
        });
    }
};
