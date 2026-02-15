<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\Controller;
use App\Models\BlogAIGeneration;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\BlogTag;
use App\Services\BlogAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class BlogAIGenerateController extends Controller
{
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'topic' => 'required|string|max:500',
            'tone' => 'nullable|in:informative,storytelling,practical,inspirational,listicle',
            'word_count' => 'nullable|integer|min:300|max:5000',
            'keywords' => 'nullable|string|max:500',
            'target_audience' => 'nullable|string|max:255',
            'category_slug' => 'nullable|string|exists:blog_categories,slug',
            'additional_notes' => 'nullable|string|max:1000',
            'auto_save' => 'nullable|boolean',
        ]);

        // Rate limit: 10 per hour
        $key = 'internal-blog-ai-generate:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'ok' => false,
                'errors' => [['field' => 'rate_limit', 'message' => "Rate limit exceeded. Try again in {$seconds} seconds."]],
            ], 429);
        }
        RateLimiter::hit($key, 3600);

        Log::info('Blog AI generation requested', [
            'topic' => $request->input('topic'),
            'tone' => $request->input('tone', 'informative'),
            'ip' => $request->ip(),
        ]);

        try {
            $service = app(BlogAIService::class);
            $postData = $service->generateBlogPost($request->only([
                'topic', 'tone', 'word_count', 'keywords',
                'target_audience', 'additional_notes',
            ]));

            $result = [
                'ok' => true,
                'post_data' => $postData,
            ];

            if ($request->boolean('auto_save')) {
                $saved = $this->savePost($postData, $request->all());
                $result['saved'] = true;
                $result['post_id'] = $saved['post_id'];
                $result['slug'] = $saved['slug'];
                $result['url'] = url('/blog/' . $saved['slug']);
            }

            Log::info('Blog AI generation completed', [
                'title' => $postData['title'] ?? 'unknown',
                'tokens' => $postData['_meta']['tokens_used'] ?? 0,
                'auto_saved' => $request->boolean('auto_save'),
            ]);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Blog AI generation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'ok' => false,
                'errors' => [['field' => 'ai', 'message' => $e->getMessage()]],
            ], 500);
        }
    }

    protected function savePost(array $postData, array $inputParams): array
    {
        return DB::transaction(function () use ($postData, $inputParams) {
            $slug = Str::slug($postData['title'] ?? 'ai-blog-post');

            // Ensure unique slug
            $baseSlug = $slug;
            $counter = 1;
            while (BlogPost::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            // Find category
            $categoryId = null;
            if (!empty($inputParams['category_slug'])) {
                $category = BlogCategory::where('slug', $inputParams['category_slug'])->first();
                $categoryId = $category?->id;
            }

            // Estimate reading time
            $wordCount = str_word_count(strip_tags($postData['content'] ?? ''));
            $readingTime = max(1, (int) ceil($wordCount / 200));

            $post = BlogPost::create([
                'title' => $postData['title'] ?? 'AI Generated Post',
                'slug' => $slug,
                'excerpt' => $postData['excerpt'] ?? null,
                'content' => $postData['content'] ?? '',
                'category_id' => $categoryId,
                'author_name' => 'Jahongir Travel',
                'reading_time' => $postData['reading_time'] ?? $readingTime,
                'meta_title' => $postData['meta_title'] ?? null,
                'meta_description' => $postData['meta_description'] ?? null,
                'is_published' => false, // Draft — needs review
                'is_featured' => false,
                'view_count' => 0,
            ]);

            // Create English translation
            BlogPostTranslation::create([
                'blog_post_id' => $post->id,
                'locale' => 'en',
                'title' => $postData['title'] ?? $post->title,
                'slug' => $slug,
                'excerpt' => $postData['excerpt'] ?? null,
                'content' => $postData['content'] ?? '',
                'seo_title' => $postData['meta_title'] ?? null,
                'seo_description' => $postData['meta_description'] ?? null,
            ]);

            // Attach tags
            $tags = $postData['suggested_tags'] ?? $postData['tags'] ?? [];
            foreach ($tags as $tagName) {
                $tag = BlogTag::firstOrCreate(
                    ['slug' => Str::slug($tagName)],
                    ['name' => $tagName]
                );
                $post->tags()->attach($tag->id);
            }

            // Track in AI generations table
            BlogAIGeneration::create([
                'user_id' => 1,
                'blog_post_id' => $post->id,
                'status' => 'completed',
                'input_parameters' => $inputParams,
                'ai_response' => $postData,
                'tokens_used' => $postData['_meta']['tokens_used'] ?? 0,
                'cost' => $postData['_meta']['cost'] ?? 0,
                'completed_at' => now(),
            ]);

            return [
                'post_id' => $post->id,
                'slug' => $post->slug,
            ];
        });
    }
}
