<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->json('title_translations')->nullable()->after('title');
            $table->json('excerpt_translations')->nullable()->after('excerpt');
            $table->json('content_translations')->nullable()->after('content');
            $table->json('meta_title_translations')->nullable()->after('meta_title');
            $table->json('meta_description_translations')->nullable()->after('meta_description');
        });

        DB::table('blog_posts')->get()->each(function ($post) {
            DB::table('blog_posts')
                ->where('id', $post->id)
                ->update([
                    'title_translations' => json_encode(['ru' => $post->title]),
                    'excerpt_translations' => json_encode(['ru' => $post->excerpt]),
                    'content_translations' => json_encode(['ru' => $post->content]),
                    'meta_title_translations' => json_encode(['ru' => $post->meta_title]),
                    'meta_description_translations' => json_encode(['ru' => $post->meta_description]),
                ]);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['title', 'excerpt', 'content', 'meta_title', 'meta_description']);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->renameColumn('title_translations', 'title');
            $table->renameColumn('excerpt_translations', 'excerpt');
            $table->renameColumn('content_translations', 'content');
            $table->renameColumn('meta_title_translations', 'meta_title');
            $table->renameColumn('meta_description_translations', 'meta_description');
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->renameColumn('title', 'title_json');
            $table->renameColumn('excerpt', 'excerpt_json');
            $table->renameColumn('content', 'content_json');
            $table->renameColumn('meta_title', 'meta_title_json');
            $table->renameColumn('meta_description', 'meta_description_json');
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
        });

        DB::table('blog_posts')->get()->each(function ($post) {
            $title = json_decode($post->title_json, true);
            $excerpt = json_decode($post->excerpt_json, true);
            $content = json_decode($post->content_json, true);
            $metaTitle = json_decode($post->meta_title_json, true);
            $metaDesc = json_decode($post->meta_description_json, true);

            DB::table('blog_posts')->where('id', $post->id)->update([
                'title' => $title['ru'] ?? $title['en'] ?? '',
                'excerpt' => $excerpt['ru'] ?? $excerpt['en'] ?? '',
                'content' => $content['ru'] ?? $content['en'] ?? '',
                'meta_title' => $metaTitle['ru'] ?? $metaTitle['en'] ?? '',
                'meta_description' => $metaDesc['ru'] ?? $metaDesc['en'] ?? '',
            ]);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['title_json', 'excerpt_json', 'content_json', 'meta_title_json', 'meta_description_json']);
        });
    }
};
