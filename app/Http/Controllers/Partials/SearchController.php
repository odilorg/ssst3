<?php

namespace App\Http\Controllers\Partials;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search and filter tours
     * Returns: Filtered tour cards HTML with pagination
     *
     * Query params:
     * - q: Search keyword (searches title, short_description, long_description)
     * - category: Category slug to filter by
     * - duration: Duration filter (1, 2-5, 6+, or empty for all)
     * - sort: Sort order (latest, price_low, price_high, rating, popular)
     * - per_page: Results per page (default: 12, min: 6, max: 50)
     * - page: Current page (default: 1)
     * - append: If true, returns only cards without wrapper
     */
    public function search(Request $request)
    {
        // Get parameters
        $keyword = $request->get('q');
        $categorySlug = $request->get('category');
        $duration = $request->get('duration');
        $sortBy = $request->get('sort', 'latest');
        $perPage = $request->get('per_page', 12);
        $isAppend = $request->boolean('append', false);

        // Validate per_page (prevent abuse: min 6, max 50)
        $perPage = min(max($perPage, 6), 50);

        // Build query
        $query = Tour::query()
            ->with(['city'])
            ->where('is_active', true);

        // Apply category filter
        if (!empty($categorySlug)) {
            $query->whereHas('categories', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug)
                  ->where('is_active', true);
            });
        }

        // Apply search filter
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('short_description', 'like', "%{$keyword}%")
                  ->orWhere('long_description', 'like', "%{$keyword}%");
            });
        }

        // Apply duration filter
        if (!empty($duration)) {
            switch ($duration) {
                case '1':
                    $query->where('duration_days', 1);
                    break;
                case '2-5':
                    $query->whereBetween('duration_days', [2, 5]);
                    break;
                case '6+':
                    $query->where('duration_days', '>=', 6);
                    break;
            }
        }

        // Apply sorting
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price_per_person', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price_per_person', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc')
                      ->orderBy('review_count', 'desc');
                break;
            case 'popular':
                $query->orderBy('review_count', 'desc')
                      ->orderBy('rating', 'desc');
                break;
            default: // 'latest'
                $query->orderBy('created_at', 'desc');
        }

        // Execute query with pagination
        $tours = $query->paginate($perPage);

        return view('partials.tours.list', compact('tours', 'isAppend'));
    }
}
