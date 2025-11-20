<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\BlogPost;
use App\Models\City;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display homepage
     *
     * @return View
     */
    public function index(): View
    {
        // Get homepage categories (uses static method on TourCategory model)
        $categories = TourCategory::getHomepageCategories();

        // Get latest blog posts
        $blogPosts = BlogPost::published()->take(3)->get();

        // Get homepage cities (uses static method on City model)
        $cities = City::getHomepageCities();

        // Get 5-star approved reviews
        $reviews = Review::approved()->where('rating', 5)->take(7)->get();

        // Get top 6 featured tours using new query scopes
        $featuredTours = Tour::active()
            ->withReviews()
            ->withFrontendRelations()
            ->popular() // Uses scopePopular() - orderBy rating & review_count
            ->take(6)
            ->get();

        return view('pages.home', compact(
            'categories',
            'blogPosts',
            'cities',
            'reviews',
            'featuredTours'
        ));
    }
}
