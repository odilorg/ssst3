<?php

namespace App\Http\Controllers\Partials;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Get the hero section for a blog post
     */
    public function hero(string $slug): View
    {
        $post = BlogPost::with(['category', 'tags'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('partials.blog.hero', compact('post'));
    }

    /**
     * Get the main content section for a blog post
     */
    public function content(string $slug): View
    {
        $post = BlogPost::with(['category', 'tags'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Increment view count
        $post->incrementViews();

        return view('partials.blog.content', compact('post'));
    }

    /**
     * Get the sidebar section for a blog post
     */
    public function sidebar(string $slug): View
    {
        $post = BlogPost::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Get popular posts (excluding current post)
        $popularPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->popular(5)
            ->get();

        // Get recent posts (excluding current post)
        $recentPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->recent(5)
            ->get();

        // Get all categories with post count
        $categories = \App\Models\BlogCategory::withCount(['posts' => function ($query) {
            $query->where('is_published', true);
        }])
        ->where('is_active', true)
        ->orderBy('display_order')
        ->get();

        // Get all tags with post count
        $tags = \App\Models\BlogTag::withCount(['posts' => function ($query) {
            $query->where('is_published', true);
        }])
        ->having('posts_count', '>', 0)
        ->orderBy('name')
        ->get();

        return view('partials.blog.sidebar', compact('popularPosts', 'recentPosts', 'categories', 'tags'));
    }

    /**
     * Get related posts section
     */
    public function related(string $slug): View
    {
        $post = BlogPost::with(['category', 'tags'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

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

        return view('partials.blog.related', compact('relatedPosts'));
    }
}
