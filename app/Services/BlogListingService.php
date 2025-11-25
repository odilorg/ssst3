<?php

namespace App\Services;

use App\Models\BlogPost;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BlogListingService
{
    /**
     * Build the blog listing query with shared filters/sorting.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPosts(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = BlogPost::published()
            ->with(['category', 'tags'])
            ->select([
                'id',
                'slug',
                'title',
                'excerpt',
                'featured_image',
                'featured_image_webp',
                'featured_image_sizes',
                'image_processing_status',
                'category_id',
                'author_name',
                'author_image',
                'reading_time',
                'view_count',
                'published_at',
            ]);

        if (!empty($filters['category'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        if (!empty($filters['tag'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->where('slug', $filters['tag']);
            });
        }

        if (!empty($filters['search'])) {
            $query->whereFullText(['title', 'excerpt', 'content'], $filters['search']);
        }

        $sort = $filters['sort'] ?? 'latest';
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

        $perPage = max(1, $perPage);

        return $query->paginate($perPage)->withQueryString();
    }
}
