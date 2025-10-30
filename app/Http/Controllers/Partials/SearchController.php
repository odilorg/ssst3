<?php

namespace App\Http\Controllers\Partials;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search and filter tours
     * Returns: Filtered tour cards HTML
     *
     * Will be fully implemented in Phase 2
     */
    public function search(Request $request)
    {
        $query = Tour::query()->where('is_active', true);

        // Search by keyword
        if ($request->filled('q')) {
            $keyword = $request->get('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('short_description', 'like', "%{$keyword}%");
            });
        }

        // Filter by duration (basic implementation)
        if ($request->filled('duration')) {
            $duration = $request->get('duration');
            if ($duration === '1') {
                $query->where('duration_days', 1);
            } elseif ($duration === '2-5') {
                $query->whereBetween('duration_days', [2, 5]);
            } elseif ($duration === '6+') {
                $query->where('duration_days', '>=', 6);
            }
        }

        // Sort
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price_per_person', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price_per_person', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $tours = $query->with('city')->get();

        return view('partials.tours.list', compact('tours'));
    }
}
