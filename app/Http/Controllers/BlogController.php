<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use App\Services\BlogListingService;

class BlogController extends Controller
{
    protected BlogListingService $blogListingService;

    public function __construct(BlogListingService $blogListingService)
    {
        $this->blogListingService = $blogListingService;
    }

    /**
     * Display blog listing page with filters and pagination
     *
     * Query Parameters:
     * - category: Filter by category slug
     * - tag: Filter by tag slug
     * - search: Search in title and excerpt
     * - sort: Sort by 'latest', 'popular', 'oldest'
     * - page: Pagination page number
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // Validate query parameters
        $validated = $request->validate([
            'category' => 'nullable|string|max:100',
            'tag' => 'nullable|string|max:100',
            'search' => 'nullable|string|max:200',
            'sort' => 'nullable|in:latest,popular,oldest',
            'page' => 'nullable|integer|min:1',
        ]);

        // Build cache key based on filters
        $cacheKey = $this->buildCacheKey($request);

        // Cache the query results for 10 minutes
        $data = Cache::remember($cacheKey, 600, function () use ($request, $validated) {
            return $this->fetchBlogData($request, $validated);
        });

        return view('blog.index', $data);
    }

    /**
     * Display single blog article page
     *
     * @param string $slug
     * @return Response
     */
    public function show(string $slug): Response
    {
        // For localized routes ({locale}/blog/{slug}), the $slug parameter
        // may receive the locale prefix due to positional injection.
        // Always use the named route parameter to get the correct slug.
        $slug = request()->route('slug', $slug);

        // Validate slug format
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            abort(404, 'Invalid blog post URL');
        }

        $post = Cache::remember("blog.page.{$slug}", 3600, function () use ($slug) {
            return BlogPost::where('slug', $slug)
                ->where('is_published', true)
                ->first();
        });

        if (!$post) {
            abort(404, 'Blog post not found or not published');
        }

        $pageTitle = $post->meta_title ?? $post->title;
        $metaDescription = $post->meta_description
            ?? $post->excerpt
            ?? Str::limit(strip_tags($post->content ?? ''), 160, '');
        $ogImage = $post->featured_image_url ?? asset('images/og-default.jpg');
        $canonical = url('/blog/' . $post->slug);
        $schemaDescription = $post->meta_description
            ?? strip_tags($post->excerpt ?? $post->content ?? '');

        return response()->view('blog.article', compact(
            'post',
            'pageTitle',
            'metaDescription',
            'ogImage',
            'canonical',
            'schemaDescription'
        ));
    }

    /**
     * Fetch blog data with filters applied
     *
     * @param Request $request
     * @param array $validated
     * @return array
     */
    private function fetchBlogData(Request $request, array $validated): array
    {
        $posts = $this->blogListingService->getPosts($validated, 12);

        // Get all categories for filter dropdown
        $categories = Cache::remember('blog.categories.all', 3600, function () {
            return BlogCategory::where('is_active', true)
                ->withCount(['posts' => function ($q) {
                    $q->where('is_published', true);
                }])
                ->orderBy('display_order')
                ->get();
        });

        // Get all tags for filter
        $tags = Cache::remember('blog.tags.all', 3600, function () {
            return BlogTag::withCount(['posts' => function ($q) {
                    $q->where('is_published', true);
                }])
                ->having('posts_count', '>', 0)
                ->orderBy('name')
                ->get();
        });

        // Get featured posts for sidebar (if needed later)
        $featuredPosts = Cache::remember('blog.featured', 600, function () {
            return BlogPost::published()
                ->where('is_featured', true)
                ->latest('published_at')
                ->limit(3)
                ->get(['id', 'slug', 'title', 'featured_image', 'published_at']);
        });

        // If no results and filters are applied, provide suggestions
        $suggestions = [];
        if ($posts->isEmpty() && ($validated['search'] ?? $validated['category'] ?? $validated['tag'])) {
            $suggestions = BlogPost::published()
                ->orderBy('view_count', 'desc')
                ->limit(3)
                ->get(['id', 'slug', 'title']);
        }

        return compact('posts', 'categories', 'tags', 'featuredPosts', 'suggestions');
    }

    /**
     * Build cache key from request parameters
     *
     * @param Request $request
     * @return string
     */
    private function buildCacheKey(Request $request): string
    {
        $params = [
            'page' => $request->input('page', 1),
            'category' => $request->input('category'),
            'tag' => $request->input('tag'),
            'search' => $request->input('search'),
            'sort' => $request->input('sort', 'latest'),
        ];

        // Remove null values
        $params = array_filter($params, fn($value) => !is_null($value));

        // Create cache key
        return 'blog.listing.' . md5(json_encode($params));
    }

    /**
     * Get related tours partial for a blog post
     *
     * @param string $slug
     * @return View
     */
    public function relatedTours(string $slug): View
    {
        $slug = request()->route('slug', $slug);

        // Validate slug format
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            abort(404, 'Invalid blog post URL');
        }

        // Get the blog post
        $post = Cache::remember("blog.post.{$slug}", 3600, function () use ($slug) {
            return BlogPost::where('slug', $slug)
                ->where('is_published', true)
                ->with('city')
                ->first();
        });

        if (!$post) {
            abort(404, 'Blog post not found');
        }

        // Get related tours using the model method
        $tours = $post->getRelatedTours(3);

        return view('partials.blog.related-tours', compact('tours'));
    }

    /**
     * Display tag landing page
     *
     * @param string $slug
     * @return View
     */
    public function tagPage(string $slug): View
    {
        $slug = request()->route('slug', $slug);

        // Validate slug format
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            abort(404, 'Invalid tag URL');
        }

        // Get the tag with post count
        $tag = Cache::remember("blog.tag.{$slug}", 3600, function () use ($slug) {
            return BlogTag::where('slug', $slug)
                ->where('is_active', true)
                ->withCount(['posts' => function ($q) {
                    $q->where('is_published', true);
                }])
                ->first();
        });

        if (!$tag) {
            abort(404, 'Tag not found');
        }

        // Build cache key for tag posts
        $page = request()->input('page', 1);
        $sort = request()->input('sort', 'latest');
        $cacheKey = "blog.tag.{$slug}.posts.{$page}.{$sort}";

        // Get posts with this tag
        $posts = Cache::remember($cacheKey, 600, function () use ($slug, $sort) {
            $filters = [
                'tag' => $slug,
                'sort' => $sort,
            ];
            return $this->blogListingService->getPosts($filters, 12);
        });

        // Get all tags for filter
        $tags = Cache::remember('blog.tags.all', 3600, function () {
            return BlogTag::withCount(['posts' => function ($q) {
                    $q->where('is_published', true);
                }])
                ->having('posts_count', '>', 0)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        });

        // Get all categories for filter
        $categories = Cache::remember('blog.categories.all', 3600, function () {
            return BlogCategory::where('is_active', true)
                ->withCount(['posts' => function ($q) {
                    $q->where('is_published', true);
                }])
                ->orderBy('display_order')
                ->get();
        });

        return view('blog.tag', compact('tag', 'posts', 'tags', 'categories'));
    }
}
