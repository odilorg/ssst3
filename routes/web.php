<?php

use Illuminate\Support\Facades\Route;
use App\Models\Booking;
use App\Http\Controllers\BookingEstimateController;
use App\Services\SupplierRequestService;

// ============================================
// SEO REDIRECTS: Old site → New site (301)
// ============================================
// Must load BEFORE main routes so old URLs get caught first
require __DIR__ . '/redirects.php';

// ============================================
// LOCALIZED ROUTES (Phase 1 - Parallel Routes)
// ============================================
// Only load when multilang is enabled AND routes phase is active
// These routes run IN PARALLEL with existing routes - nothing breaks
if (config('multilang.enabled') && config('multilang.phases.routes')) {
    require __DIR__ . '/web_localized.php';
}

// CSRF Token endpoint for frontend
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

// Sitemap XML for SEO
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Locale-specific sitemaps (only when SEO phase enabled)
Route::get('/sitemap-{locale}.xml', [\App\Http\Controllers\SitemapController::class, 'locale'])
    ->name('sitemap.locale')
    ->where('locale', '[a-z]{2}');

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Mini Journeys - 1-2 day experiences including overnight camping
Route::get('/mini-journeys', [\App\Http\Controllers\TourListingController::class, 'miniJourneys'])->name('mini-journeys.index');

// Craft Journeys - 3+ day multi-day boutique tours
Route::get('/craft-journeys', [\App\Http\Controllers\TourListingController::class, 'craftJourneys'])->name('craft-journeys.index');

// Legacy /tours route - 301 redirect to /craft-journeys
Route::get('/tours', function() {
    return redirect()->route('craft-journeys.index', [], 301);
})->name('tours.index');

// Category landing page - SEO-friendly URL with server-side meta tag injection
Route::get('/tours/category/{slug}', [\App\Http\Controllers\CategoryLandingController::class, 'show'])->name('tours.category');

// Tour comparison page
Route::get('/tours/compare', function () {
    return view('pages.tour-comparison');
})->name('tours.compare');

// Tour details page - SEO-friendly URL with Blade template (must be LAST to avoid conflicts)
// Tour PDF download
Route::get("/tours/{slug}/download-pdf", [\App\Http\Controllers\TourPdfController::class, "download"])->name("tours.download-pdf");
Route::get("/tours/{slug}/view-pdf", [\App\Http\Controllers\TourPdfController::class, "stream"])->name("tours.view-pdf");

Route::get('/tours/{slug}', [\App\Http\Controllers\TourDetailController::class, 'show'])->name('tours.show');

// About page
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

// Contact page
Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

// Legal pages
Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');

Route::get('/cookies', function () {
    return view('pages.cookies');
})->name('cookies');

// Blog listing page
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');


// Blog tag landing pages
Route::get('/blog/tag/{slug}', [\App\Http\Controllers\BlogController::class, 'tagPage'])
    ->name('blog.tag')
    ->where('slug', '[a-z0-9-]+');
// Blog article page
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])
    ->name('blog.show')
    ->where('slug', '[a-z0-9-]+');


// Artisan Workshop pages
Route::get("/workshops", [\App\Http\Controllers\WorkshopController::class, "index"])->name("workshops.index");
Route::get("/workshops/{slug}", [\App\Http\Controllers\WorkshopController::class, "show"])->name("workshops.show")->where("slug", "[a-z0-9-]+");
// Contact form submission
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])
    ->name('contact.store');

// Blog comments
Route::post('/comments', [\App\Http\Controllers\CommentController::class, 'store'])
    ->name('comments.store');

Route::post('/comments/{comment}/flag', [\App\Http\Controllers\CommentController::class, 'flag'])
    ->name('comments.flag');

// Tour reviews
Route::post('/tours/{slug}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])
    ->name('reviews.store');

Route::post('/reviews/{review}/flag', [\App\Http\Controllers\ReviewController::class, 'flag'])
    ->name('reviews.flag');

// Printable booking estimate route — admin only
Route::get('/booking/{booking}/estimate/print', [BookingEstimateController::class, 'show'])
    ->name('booking.estimate.print');

// Generate supplier requests for a booking
Route::post('/booking/{booking}/generate-requests', function (Booking $booking) {
    try {
        $requestService = app(SupplierRequestService::class);
        
        // Generate requests for all assigned suppliers
        $requests = $requestService->generateRequestsForBooking($booking);
        
        if (empty($requests)) {
            return response()->json([
                'success' => false,
                'message' => 'Нет назначенных поставщиков для генерации заявок'
            ], 400);
        }
        
        // Prepare response data
        $responseData = [];
        foreach ($requests as $request) {
            $responseData[] = [
                'id' => $request->id,
                'supplier_type' => $request->supplier_type,
                'supplier_type_label' => $request->supplier_type_label,
                'supplier_type_icon' => $request->supplier_type_icon,
                'supplier_name' => $request->supplier?->name ?? 'Неизвестный поставщик',
                'status' => $request->status,
                'status_label' => $request->status_label,
                'expires_at' => $request->expires_at?->format('d.m.Y H:i'),
                'pdf_url' => $requestService->getDownloadUrl($request->pdf_path),
                'pdf_path' => $request->pdf_path,
            ];
        }
        
        return response()->json([
            'success' => true,
            'message' => "Сгенерировано " . count($requests) . " заявок",
            'requests' => $responseData
        ]);
        
    } catch (\Exception $e) {
        \Log::error("Failed to generate supplier requests: " . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Ошибка при генерации заявок: ' . $e->getMessage()
        ], 500);
    }
})->name('booking.generate.requests');

// Download individual supplier request PDF — admin only
Route::get('/supplier-request/{request}/download', function (\App\Models\SupplierRequest $request) {
    abort_unless(auth()->check() && auth()->user()->can('download', $request), 403);

    if (!$request->pdf_path) {
        abort(404, 'PDF файл не найден');
    }

    $requestService = app(SupplierRequestService::class);
    $filePath = storage_path('app/public/' . $request->pdf_path);

    if (!file_exists($filePath)) {
        abort(404, 'PDF файл не найден на диске');
    }

    return response()->download($filePath, "Заявка_{$request->booking->reference}_{$request->supplier_type_label}.pdf");
})->name('supplier.request.download');

// ============================================
// PUBLIC PARTIAL ROUTES
// Serve HTML partials for frontend consumption
// ============================================

use App\Http\Controllers\Partials\TourController;
use App\Http\Controllers\Partials\BookingController;
use App\Http\Controllers\Partials\SearchController;
use App\Http\Controllers\Partials\BlogController;
use App\Http\Controllers\Partials\CategoryController;
use App\Http\Controllers\Partials\CityController;

Route::prefix('partials')->name('partials.')->group(function () {

    // -------- CATEGORIES --------
    Route::get('/categories/homepage', [CategoryController::class, 'homepage'])
        ->name('categories.homepage');

    Route::get('/categories/related', [CategoryController::class, 'related'])
        ->name('categories.related');

    Route::get('/categories/{slug}/data', [CategoryController::class, 'data'])
        ->name('categories.data');

    // -------- CITIES / DESTINATIONS --------
    Route::get('/cities/related', [CityController::class, 'related'])
        ->name('cities.related');

    Route::get('/cities/{slug}/data', [CityController::class, 'data'])
        ->name('cities.data');

    // -------- TOUR LIST --------
    Route::get('/tours', [TourController::class, 'list'])
        ->name('tours.list');

    // -------- TOUR SEARCH/FILTER --------
    Route::get('/tours/search', [SearchController::class, 'search'])
        ->name('tours.search');

    // -------- TOUR DETAIL SECTIONS --------
    Route::get('/tours/{slug}/hero', [TourController::class, 'hero'])
        ->name('tours.hero');
    
    Route::get('/tours/{slug}/gallery', [TourController::class, 'gallery'])
        ->name('tours.gallery');

    Route::get('/tours/{slug}/overview', [TourController::class, 'overview'])
        ->name('tours.overview');

    Route::get('/tours/{slug}/highlights', [TourController::class, 'highlights'])
        ->name('tours.highlights');

    Route::get('/tours/{slug}/itinerary', [TourController::class, 'itinerary'])
        ->name('tours.itinerary');

    Route::get('/tours/{slug}/included-excluded', [TourController::class, 'includedExcluded'])
        ->name('tours.included-excluded');

    Route::get('/tours/{slug}/requirements', [TourController::class, 'requirements'])
        ->name('tours.requirements');
    Route::get('/tours/{slug}/cancellation', [TourController::class, 'cancellation'])
        ->name('tours.cancellation');
    Route::get('/tours/{slug}/meeting-point', [TourController::class, 'meetingPoint'])
        ->name('tours.meeting-point');


    Route::get('/tours/{slug}/faqs', [TourController::class, 'faqs'])
        ->name('tours.faqs');

    Route::get('/tours/{slug}/extras', [TourController::class, 'extras'])
        ->name('tours.extras');

    Route::get('/tours/{slug}/reviews', [TourController::class, 'reviews'])
        ->name('tours.reviews');
    Route::get('/tours/{slug}/related', [TourController::class, 'relatedTours'])
        ->name('tours.related');

    // -------- BOOKING --------
    Route::get('/bookings/form/{tour_slug}', [BookingController::class, 'form'])
        ->name('bookings.form');

    Route::post('/bookings', [BookingController::class, 'store'])
        ->name('bookings.store');

    // Simple inquiry submission (3 fields only: name, email, message)
    Route::post('/inquiries', [BookingController::class, 'store'])
        ->name('inquiries.store');

    // -------- BLOG POST SECTIONS --------
    Route::get('/blog/{slug}/hero', [BlogController::class, 'hero'])
        ->name('blog.hero');

    Route::get('/blog/{slug}/content', [BlogController::class, 'content'])
        ->name('blog.content');

    Route::get('/blog/{slug}/sidebar', [BlogController::class, 'sidebar'])
        ->name('blog.sidebar');

    Route::get('/blog/{slug}/related', [BlogController::class, 'related'])
        ->name('blog.related');

    Route::get('/blog/{slug}/comments', [BlogController::class, 'comments'])
        ->name('blog.comments');
    Route::get('/blog/{slug}/related-tours', [BlogController::class, 'relatedTours'])
        ->name('blog.related-tours');

    // -------- BLOG LISTING (HTMX) --------
    Route::get('/blog/listing', [BlogController::class, 'listing'])
        ->name('blog.listing');
});

// ============================================
// BOOKING PREVIEW (Dynamic pricing calculation)
// ============================================
Route::post('/bookings/preview', [\App\Http\Controllers\BookingPreviewController::class, 'preview'])
    ->name('bookings.preview');

// ============================================
// BOOKING CONFIRMATION PAGE (Public-facing)
// ============================================
Route::get('/booking/confirmation/{reference}', [\App\Http\Controllers\Partials\BookingController::class, 'confirmation'])
    ->name('booking.confirmation');

// City/Destination landing page - SEO-friendly URL with server-side meta tag injection
// Destinations index page
Route::get('/destinations/', [\App\Http\Controllers\DestinationController::class, 'index'])->name('destinations.index');

Route::get('/destinations/{slug}', [\App\Http\Controllers\DestinationController::class, 'show'])->name('city.show');

// ============================================
// TEMPORARY: Blade Refactor Testing
// ============================================

// Phase 1: Test layout system
Route::get('/test-layout', function () {
    return view('test-layout');
})->name('test.layout');

// Test home.blade.php template
Route::get('/test-home', function () {
    $categories = \App\Models\TourCategory::getHomepageCategories();
    $blogPosts = \App\Models\BlogPost::published()->take(3)->get();
    $cities = \App\Models\City::getHomepageCities();
    $reviews = \App\Models\Review::approved()->where('rating', 5)->take(7)->get();

    return view('pages.home', compact('categories', 'blogPosts', 'cities', 'reviews', 'featuredTours'));
})->name('test.home');

// TEMPORARY: Card Design Comparison Page (for testing UI options)
Route::get('/card-comparison', function () {
    $tours = \App\Models\Tour::with('city')->where('is_active', true)->take(6)->get();
    return view('card-comparison', ['tours' => $tours]);
})->name('card.comparison');

// ============================================
// TRIP DETAILS (Guest-facing, token-based access)
// ============================================
Route::get('/trip-details/{token}', [\App\Http\Controllers\TripDetailController::class, 'show'])
    ->name('trip-details.show');

Route::post('/trip-details/{token}', [\App\Http\Controllers\TripDetailController::class, 'store'])
    ->name('trip-details.store');

Route::get('/trip-details/{token}/confirm', [\App\Http\Controllers\TripDetailController::class, 'confirm'])
    ->name('trip-details.confirm');

Route::get('/trip-details/{token}/itinerary.pdf', [\App\Http\Controllers\ItineraryPdfController::class, 'download'])
    ->name('trip-details.itinerary-pdf');

// ============================================
// PAYMENT ROUTES
// ============================================

// Payment result page (user return from Octobank)
Route::get('/payment/result', [\App\Http\Controllers\PaymentController::class, 'result'])->name('payment.result');

// Octobank callback (webhook) - uses web route like working app
Route::post('/octo/callback', [\App\Http\Controllers\PaymentController::class, 'webhook'])->name('octo.callback');
