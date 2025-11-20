<?php

namespace App\Http\Controllers\Partials;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Services\BlogListingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class BlogController extends Controller
{
    protected BlogListingService $blogListingService;

    public function __construct(BlogListingService $blogListingService)
    {
        $this->blogListingService = $blogListingService;
    }

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
        BlogPost::where('id', $post->id)->increment('view_count');

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

    /**
     * Get comments section for a blog post
     * Cached for 10 minutes (600 seconds)
     */
    public function comments(string $slug): View
    {
        // Get blog post
        $post = Cache::remember("blog.{$slug}.comments.post", 3600, function () use ($slug) {
            return BlogPost::where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail();
        });

        // Get approved top-level comments with their approved replies
        // Cached for 10 minutes to show new comments relatively quickly
        $comments = Cache::remember("blog.{$slug}.comments.data", 600, function () use ($post) {
            return $post->topLevelComments()
                ->with(['replies' => function ($query) {
                    $query->where('status', 'approved')->oldest();
                }])
                ->get();
        });

        // Get comment count (only approved comments)
        $commentCount = Cache::remember("blog.{$slug}.comments.count", 600, function () use ($post) {
            return $post->comments()->where('status', 'approved')->count();
        });

        return view('partials.blog.comments.comments', compact('post', 'comments', 'commentCount'));
    }

    /**
     * Get blog listing partial for HTMX load more
     * Used for infinite scroll and dynamic filtering
     *
     * Query Parameters:
     * - category: Filter by category slug
     * - tag: Filter by tag slug
     * - search: Search term
     * - sort: Sort order
     * - page: Page number
     *
     * @param Request $request
     * @return View
     */
    public function listing(Request $request): View
    {
        // Validate query parameters
        $validated = $request->validate([
            'category' => 'nullable|string|max:100',
            'tag' => 'nullable|string|max:100',
            'search' => 'nullable|string|max:200',
            'sort' => 'nullable|in:latest,popular,oldest',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:24',
        ]);

        $perPage = $validated['per_page'] ?? 12;
        $filters = $validated;
        unset($filters['per_page']);

        $posts = $this->blogListingService->getPosts($filters, $perPage);

        return view('partials.blog.listing', compact('posts'));
    }

    /**
     * Get related tours section for a blog post
     * Cached for 1 hour (3600 seconds)
     */
    public function relatedTours(string $slug): View
    {
        // Get the blog post
        $post = Cache::remember("blog.{$slug}.related-tours.post", 3600, function () use ($slug) {
            return BlogPost::where('slug', $slug)
                ->where('is_published', true)
                ->with('city')
                ->firstOrFail();
        });

        // Get related tours (not cached due to pivot serialization issues)
        $tours = $post->getRelatedTours(3);

        return view('partials.blog.related-tours', compact('tours'));
    }
}
