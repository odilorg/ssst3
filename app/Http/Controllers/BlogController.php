<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller
{
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
        // Validate slug format
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            abort(404, 'Invalid blog post URL');
        }

        // Check if blog post exists and is published
        $exists = Cache::remember("blog.exists.{$slug}", 3600, function () use ($slug) {
            return BlogPost::where('slug', $slug)
                ->where('is_published', true)
                ->exists();
        });

        if (!$exists) {
            abort(404, 'Blog post not found or not published');
        }

        // Check if blog-article.html exists
        $filePath = public_path('blog-article.html');
        if (!file_exists($filePath)) {
            abort(500, 'Blog template file not found');
        }

        // Serve the static HTML file
        return response()->file($filePath);
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
        // Start query
        $query = BlogPost::published()
            ->with(['category', 'tags'])
            ->select(['id', 'slug', 'title', 'excerpt', 'featured_image',
                     'category_id', 'author_name', 'author_image',
                     'reading_time', 'view_count', 'published_at']);

        // Apply category filter
        if (!empty($validated['category'])) {
            $query->whereHas('category', function ($q) use ($validated) {
                $q->where('slug', $validated['category']);
            });
        }

        // Apply tag filter
        if (!empty($validated['tag'])) {
            $query->whereHas('tags', function ($q) use ($validated) {
                $q->where('slug', $validated['tag']);
            });
        }

        // Apply search filter
        if (!empty($validated['search'])) {
            $searchTerm = $validated['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('excerpt', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }

        // Apply sorting
        $sort = $validated['sort'] ?? 'latest';
        switch ($sort) {
            case 'popular':
                $query->orderBy('view_count', 'desc');
                break;
            case 'oldest':
                $query->orderBy('published_at', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('published_at', 'desc');
                break;
        }

        // Get paginated results (12 per page)
        $posts = $query->paginate(12)->withQueryString();

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
}
