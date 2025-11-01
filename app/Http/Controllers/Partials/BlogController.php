<?php

namespace App\Http\Controllers\Partials;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Get the hero section for a blog post
     * Cached for 1 hour (3600 seconds)
     */
    public function hero(string $slug): View
    {
        $post = Cache::remember("blog.{$slug}.hero", 3600, function () use ($slug) {
            return BlogPost::with(['category', 'tags'])
                ->where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail();
        });

        return view('partials.blog.hero', compact('post'));
    }

    /**
     * Get the main content section for a blog post
     * Post data cached for 1 hour, view count incremented separately
     */
    public function content(string $slug): View
    {
        // Cache the post data retrieval
        $post = Cache::remember("blog.{$slug}.content", 3600, function () use ($slug) {
            return BlogPost::with(['category', 'tags'])
                ->where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail();
        });

        // Increment view count (not cached, runs every time)
        // Using a separate database query to avoid caching issues
        BlogPost::where('id', $post->id)->increment('views');

        return view('partials.blog.content', compact('post'));
    }

    /**
     * Get the sidebar section for a blog post
     * Popular/recent posts cached for 10 minutes (600 seconds)
     * Categories and tags cached for 1 hour (3600 seconds)
     */
    public function sidebar(string $slug): View
    {
        $post = Cache::remember("blog.{$slug}.sidebar.post", 3600, function () use ($slug) {
            return BlogPost::where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail();
        });

        // Get popular posts (excluding current post) - cached for 10 minutes
        $popularPosts = Cache::remember("blog.sidebar.popular.{$post->id}", 600, function () use ($post) {
            return BlogPost::published()
                ->where('id', '!=', $post->id)
                ->popular(5)
                ->get();
        });

        // Get recent posts (excluding current post) - cached for 10 minutes
        $recentPosts = Cache::remember("blog.sidebar.recent.{$post->id}", 600, function () use ($post) {
            return BlogPost::published()
                ->where('id', '!=', $post->id)
                ->recent(5)
                ->get();
        });

        // Get all categories with post count - cached for 1 hour
        $categories = Cache::remember('blog.sidebar.categories', 3600, function () {
            return \App\Models\BlogCategory::withCount(['posts' => function ($query) {
                $query->where('is_published', true);
            }])
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();
        });

        // Get all tags with post count - cached for 1 hour
        $tags = Cache::remember('blog.sidebar.tags', 3600, function () {
            return \App\Models\BlogTag::withCount(['posts' => function ($query) {
                $query->where('is_published', true);
            }])
            ->having('posts_count', '>', 0)
            ->orderBy('name')
            ->get();
        });

        return view('partials.blog.sidebar', compact('popularPosts', 'recentPosts', 'categories', 'tags'));
    }

    /**
     * Get related posts section
     * Cached for 1 hour (3600 seconds)
     */
    public function related(string $slug): View
    {
        $post = Cache::remember("blog.{$slug}.related.post", 3600, function () use ($slug) {
            return BlogPost::with(['category', 'tags'])
                ->where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail();
        });

        // Cache the related posts query
        $relatedPosts = Cache::remember("blog.{$slug}.related.posts", 3600, function () use ($post) {
            // Get related posts from the same category
            $relatedPosts = BlogPost::published()
                ->where('category_id', $post->category_id)
                ->where('id', '!=', $post->id)
                ->recent(3)
                ->get();

            // If not enough posts in the same category, fill with other recent posts
            if ($relatedPosts->count() < 3) {
                $additionalPosts = BlogPost::published()
                    ->where('id', '!=', $post->id)
                    ->whereNotIn('id', $relatedPosts->pluck('id'))
                    ->recent(3 - $relatedPosts->count())
                    ->get();

                $relatedPosts = $relatedPosts->merge($additionalPosts);
            }

            return $relatedPosts;
        });

        return view('partials.blog.related', compact('relatedPosts'));
    }
}
