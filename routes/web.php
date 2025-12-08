<?php

use Illuminate\Support\Facades\Route;
use App\Models\Booking;
use App\Services\PricingService;
use App\Services\SupplierRequestService;
use App\Models\Tour;

// Global routes (no locale prefix)
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Redirect root to default locale
Route::get('/', function () {
    return redirect(app()->getLocale());
});

// Localized routes
Route::group(['prefix' => '{locale?}', 'where' => ['locale' => 'en|ru|uz']], function () {

    // Home
    Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Tours
    Route::get('/tours', [\App\Http\Controllers\TourListingController::class, 'index'])->name('tours.index');
    Route::get('/tours/category/{slug}', [\App\Http\Controllers\CategoryLandingController::class, 'show'])->name('tours.category');
    Route::get('/tours/{slug}', [\App\Http\Controllers\TourDetailController::class, 'show'])->name('tours.show');

    // Static pages
    Route::get('/about', function () {
        return view('pages.about');
    })->name('about');

    Route::get('/contact', function () {
        return view('pages.contact');
    })->name('contact');

    Route::get('/privacy', function () {
        return view('pages.privacy');
    })->name('privacy');

    Route::get('/terms', function () {
        return view('pages.terms');
    })->name('terms');

    Route::get('/cookies', function () {
        return view('pages.cookies');
    })->name('cookies');

    // Blog
    Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/tag/{slug}', [\App\Http\Controllers\BlogController::class, 'tagPage'])->name('blog.tag')->where('slug', '[a-z0-9-]+');
    Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show')->where('slug', '[a-z0-9-]+');

    // Destinations
    Route::get('/destinations/', [\App\Http\Controllers\DestinationController::class, 'index'])->name('destinations.index');
    Route::get('/destinations/{slug}', [\App\Http\Controllers\DestinationController::class, 'show'])->name('city.show');

    // Forms
    Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');
    Route::post('/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::post('/comments/{comment}/flag', [\App\Http\Controllers\CommentController::class, 'flag'])->name('comments.flag');
    Route::post('/tours/{slug}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/reviews/{review}/flag', [\App\Http\Controllers\ReviewController::class, 'flag'])->name('reviews.flag');
});

// Admin routes (no locale prefix) - Keep existing functionality
Route::get('/booking/{booking}/estimate/print', function (Booking $booking) {
    $pricingService = app(PricingService::class);
    $itineraryItems = $booking->itineraryItems()
        ->with(['assignments.assignable', 'assignments.transportPrice', 'assignments.transportInstancePrice', 'tourItineraryItem'])
        ->orderBy('date')
        ->orderBy('sort_order')
        ->get();

    $groupedByDay = $itineraryItems->groupBy(function ($item) {
        return $item->date ? $item->date->format('Y-m-d') : 'no-date';
    });

    $dayBreakdown = [];
    $totalCost = 0;
    $categoryTotals = ['hotel' => 0, 'transport' => 0, 'restaurant' => 0, 'guide' => 0, 'monument' => 0, 'other' => 0];

    foreach ($groupedByDay as $date => $dayItems) {
        $dayTotal = 0;
        $dayCategories = [];

        foreach ($dayItems as $item) {
            foreach ($item->assignments as $assignment) {
                $assignable = $assignment->assignable;
                $quantity = match($assignment->assignable_type) {
                    \App\Models\Monument::class => $booking->pax_total ?? 1,
                    default => $assignment->quantity ?? 1,
                };

                $subServiceId = match($assignment->assignable_type) {
                    \App\Models\Hotel::class => $assignment->room_id,
                    \App\Models\Restaurant::class => $assignment->meal_type_id,
                    \App\Models\Transport::class => $assignment->transport_instance_price_id ?? $assignment->transport_price_type_id,
                    default => null,
                };

                $pricing = $pricingService->getPricingBreakdown($assignment->assignable_type, $assignment->assignable_id, $subServiceId, $booking->start_date);
                $unitPrice = $pricing['final_price'] ?? 0;

                $itemName = match($assignment->assignable_type) {
                    \App\Models\Guide::class => $assignable?->name ?? 'Гид удален',
                    \App\Models\Restaurant::class => $assignable?->name . ($assignment->meal_type_id ? ' - ' . \App\Models\MealType::find($assignment->meal_type_id)?->name : '') ?? 'Ресторан удален',
                    \App\Models\Hotel::class => $assignable?->name . ($assignment->room_id ? ' - ' . \App\Models\Room::find($assignment->room_id)?->name : '') ?? 'Гостиница удалена',
                    \App\Models\Transport::class => \App\Models\Transport::getEstimateLabel($assignment),
                    \App\Models\Monument::class => $assignable?->name ?? 'Монумент удален',
                    default => 'Неизвестный поставщик',
                };

                $category = match($assignment->assignable_type) {
                    \App\Models\Guide::class => 'guide',
                    \App\Models\Restaurant::class => 'restaurant',
                    \App\Models\Hotel::class => 'hotel',
                    \App\Models\Transport::class => 'transport',
                    \App\Models\Monument::class => 'monument',
                    default => 'other',
                };

                if (!isset($dayCategories[$category])) {
                    $dayCategories[$category] = [
                        'items' => [],
                        'subtotal' => 0,
                        'category_name' => match($category) {
                            'guide' => 'Гид',
                            'restaurant' => 'Ресторан',
                            'hotel' => 'Гостиница',
                            'transport' => 'Транспорт',
                            'monument' => 'Достопримечательности',
                            default => 'Другое'
                        },
                        'category_icon' => match($category) {
                            'guide' => '👨‍🏫',
                            'restaurant' => '🍽️',
                            'hotel' => '🏨',
                            'transport' => '🚗',
                            'monument' => '📍',
                            default => '📋'
                        }
                    ];
                }

                $itemTotal = $unitPrice * $quantity;
                $dayCategories[$category]['items'][] = [
                    'name' => $itemName,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $itemTotal,
                    'has_contract' => $pricing['has_contract'] ?? false,
                    'savings' => $pricing['savings'] ?? 0,
                    'savings_percentage' => $pricing['savings_percentage'] ?? 0,
                ];

                $dayCategories[$category]['subtotal'] += $itemTotal;
                $categoryTotals[$category] += $itemTotal;
                $dayTotal += $itemTotal;
            }
        }

        $categoryOrder = ['hotel', 'transport', 'restaurant', 'guide', 'monument', 'other'];
        $sortedCategories = [];
        foreach ($categoryOrder as $cat) {
            if (isset($dayCategories[$cat])) {
                $sortedCategories[$cat] = $dayCategories[$cat];
            }
        }

        $dayTitle = 'День ' . (\Carbon\Carbon::parse($date)->diffInDays($booking->start_date) + 1);
        if ($dayItems->isNotEmpty()) {
            $firstItem = $dayItems->first();
            if ($firstItem->title) {
                $dayTitle = $firstItem->title;
            } elseif ($firstItem->tourItineraryItem && $firstItem->tourItineraryItem->title) {
                $dayTitle = $firstItem->tourItineraryItem->title;
            }
        }

        $dayBreakdown[] = [
            'date' => $date,
            'formatted_date' => $date !== 'no-date' ? \Carbon\Carbon::parse($date)->format('d.m.Y') : 'Без даты',
            'day_title' => $dayTitle,
            'categories' => $sortedCategories,
            'day_total' => $dayTotal,
            'item_count' => $dayItems->count()
        ];

        $totalCost += $dayTotal;
    }

    $categorySummary = [];
    foreach ($categoryTotals as $category => $total) {
        if ($total > 0) {
            $categorySummary[] = [
                'category' => $category,
                'total' => $total,
                'percentage' => $totalCost > 0 ? round(($total / $totalCost) * 100, 1) : 0,
            ];
        }
    }

    return view('booking-print-estimate', [
        'record' => $booking,
        'dayBreakdown' => $dayBreakdown,
        'categorySummary' => $categorySummary,
        'totalCost' => $totalCost,
    ]);
})->name('booking.estimate.print');

Route::post('/booking/{booking}/generate-requests', function (Booking $booking) {
    try {
        $requestService = app(SupplierRequestService::class);
        $requests = $requestService->generateRequestsForBooking($booking);
        
        if (empty($requests)) {
            return response()->json(['success' => false, 'message' => 'Нет назначенных поставщиков для генерации заявок'], 400);
        }
        
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
        
        return response()->json(['success' => true, 'message' => "Сгенерировано " . count($requests) . " заявок", 'requests' => $responseData]);
    } catch (\Exception $e) {
        \Log::error("Failed to generate supplier requests: " . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Ошибка при генерации заявок: ' . $e->getMessage()], 500);
    }
})->name('booking.generate.requests');

Route::get('/supplier-request/{request}/download', function (\App\Models\SupplierRequest $request) {
    if (!$request->pdf_path) {
        abort(404, 'PDF файл не найден');
    }

    $filePath = storage_path('app/public/' . $request->pdf_path);
    if (!file_exists($filePath)) {
        abort(404, 'PDF файл не найден на диске');
    }

    return response()->download($filePath, "Заявка_{$request->booking->reference}_{$request->supplier_type_label}.pdf");
})->name('supplier.request.download');

// Public partials (no locale for HTMX)
use App\Http\Controllers\Partials\TourController;
use App\Http\Controllers\Partials\BookingController;
use App\Http\Controllers\Partials\SearchController;
use App\Http\Controllers\Partials\BlogController;
use App\Http\Controllers\Partials\CategoryController;
use App\Http\Controllers\Partials\CityController;

Route::prefix('partials')->name('partials.')->group(function () {
    Route::get('/categories/homepage', [CategoryController::class, 'homepage'])->name('categories.homepage');
    Route::get('/categories/related', [CategoryController::class, 'related'])->name('categories.related');
    Route::get('/categories/{slug}/data', [CategoryController::class, 'data'])->name('categories.data');
    
    Route::get('/cities/related', [CityController::class, 'related'])->name('cities.related');
    Route::get('/cities/{slug}/data', [CityController::class, 'data'])->name('cities.data');
    
    Route::get('/tours', [TourController::class, 'list'])->name('tours.list');
    Route::get('/tours/search', [SearchController::class, 'search'])->name('tours.search');
    Route::get('/tours/{slug}/hero', [TourController::class, 'hero'])->name('tours.hero');
    Route::get('/tours/{slug}/gallery', [TourController::class, 'gallery'])->name('tours.gallery');
    Route::get('/tours/{slug}/overview', [TourController::class, 'overview'])->name('tours.overview');
    Route::get('/tours/{slug}/highlights', [TourController::class, 'highlights'])->name('tours.highlights');
    Route::get('/tours/{slug}/itinerary', [TourController::class, 'itinerary'])->name('tours.itinerary');
    Route::get('/tours/{slug}/included-excluded', [TourController::class, 'includedExcluded'])->name('tours.included-excluded');
    Route::get('/tours/{slug}/requirements', [TourController::class, 'requirements'])->name('tours.requirements');
    Route::get('/tours/{slug}/cancellation', [TourController::class, 'cancellation'])->name('tours.cancellation');
    Route::get('/tours/{slug}/meeting-point', [TourController::class, 'meetingPoint'])->name('tours.meeting-point');
    Route::get('/tours/{slug}/faqs', [TourController::class, 'faqs'])->name('tours.faqs');
    Route::get('/tours/{slug}/extras', [TourController::class, 'extras'])->name('tours.extras');
    Route::get('/tours/{slug}/reviews', [TourController::class, 'reviews'])->name('tours.reviews');
    Route::get('/tours/{slug}/related', [TourController::class, 'relatedTours'])->name('tours.related');
    
    Route::get('/bookings/form/{tour_slug}', [BookingController::class, 'form'])->name('bookings.form');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/inquiries', [BookingController::class, 'store'])->name('inquiries.store');
    
    Route::get('/blog/{slug}/hero', [BlogController::class, 'hero'])->name('blog.hero');
    Route::get('/blog/{slug}/content', [BlogController::class, 'content'])->name('blog.content');
    Route::get('/blog/{slug}/sidebar', [BlogController::class, 'sidebar'])->name('blog.sidebar');
    Route::get('/blog/{slug}/related', [BlogController::class, 'related'])->name('blog.related');
    Route::get('/blog/{slug}/comments', [BlogController::class, 'comments'])->name('blog.comments');
    Route::get('/blog/{slug}/related-tours', [BlogController::class, 'relatedTours'])->name('blog.related-tours');
    Route::get('/blog/listing', [BlogController::class, 'listing'])->name('blog.listing');
});

Route::get('/booking/confirmation/{reference}', [\App\Http\Controllers\Partials\BookingController::class, 'confirmation'])->name('booking.confirmation');

// Testing routes
Route::get('/test-layout', function () {
    return view('test-layout');
})->name('test.layout');

Route::get('/test-home', function () {
    $categories = \App\Models\TourCategory::getHomepageCategories();
    $blogPosts = \App\Models\BlogPost::published()->take(3)->get();
    $cities = \App\Models\City::getHomepageCities();
    $reviews = \App\Models\Review::approved()->where('rating', 5)->take(7)->get();
    return view('pages.home', compact('categories', 'blogPosts', 'cities', 'reviews'));
})->name('test.home');
