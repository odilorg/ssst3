<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    /**
     * Display tour details page
     */
    public function show(string $slug)
    {
        $tour = Tour::where('slug', $slug)
            ->where('is_active', true)
            ->with('city')
            ->firstOrFail();

        return view('tours.show', compact('tour'));
    }

    /**
     * Return reviews section partial for HTMX reload
     */
    public function reviews(string $slug)
    {
        $tour = Tour::where('slug', $slug)
            ->where('is_active', true)
            ->with(['reviews' => function ($query) {
                $query->where('is_approved', true)
                    ->orderBy('created_at', 'desc');
            }])
            ->firstOrFail();

        return view('partials.tours.show.reviews', compact('tour'));
    }
}
