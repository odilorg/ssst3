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
}
