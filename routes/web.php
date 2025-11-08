<?php

use Illuminate\Support\Facades\Route;
use App\Models\Booking;
use App\Services\PricingService;
use App\Services\SupplierRequestService;

// CSRF Token endpoint for frontend
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

Route::get('/', function () {
    // Get homepage categories from database
    $categories = \App\Models\TourCategory::getHomepageCategories();

    // Get latest blog posts for homepage (3 posts)
    $blogPosts = \App\Models\BlogPost::published()
        ->with('category')
        ->orderBy('published_at', 'desc')
        ->take(3)
        ->get();

    // Get featured cities for homepage
    $cities = \App\Models\City::getHomepageCities();

    // Read static HTML
    $html = file_get_contents(public_path('index.html'));

    // Generate dynamic category cards HTML
    $categoriesHtml = '';
    foreach ($categories as $category) {
        $tourCount = $category->cached_tour_count;
        $tourText = $tourCount === 1 ? 'tour' : 'tours';

        // Image priority: 1) Category image, 2) Featured tour hero image, 3) Placeholder
        $imageUrl = null;
        if ($category->image_path) {
            $imageUrl = asset('storage/' . $category->image_path);
        } else {
            // Get a featured tour from this category to use its hero image
            $featuredTour = $category->tours()
                ->where('is_active', true)
                ->whereNotNull('hero_image')
                ->first();

            if ($featuredTour && $featuredTour->hero_image) {
                $imageUrl = asset('storage/' . $featuredTour->hero_image);
            } else {
                $imageUrl = 'https://placehold.co/400x300/0D4C92/FFFFFF?text=' . urlencode($category->translated_name);
            }
        }

        $categoriesHtml .= <<<HTML
          <!-- Card: {$category->translated_name} -->
          <a href="/tours/category/{$category->slug}/" class="activity-card" aria-label="Explore {$category->translated_name} tours">
            <div class="activity-card__media">
              <img src="{$imageUrl}"
                   alt="{$category->translated_description}"
                   width="400"
                   height="300"
                   loading="lazy"
                   decoding="async">
              <div class="activity-card__overlay"></div>
              <span class="activity-card__badge">{$tourCount} {$tourText}</span>
            </div>
            <div class="activity-card__content">
              <i class="activity-card__icon {$category->icon}" aria-hidden="true"></i>
              <h3 class="activity-card__title">{$category->translated_name}</h3>
              <p class="activity-card__description">{$category->translated_description}</p>
              <span class="activity-card__link">Explore Tours <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
            </div>
          </a>

HTML;
    }

    // Generate dynamic blog post cards HTML
    $blogPostsHtml = '';
    foreach ($blogPosts as $post) {
        $featuredImage = $post->featured_image
            ? asset('storage/' . $post->featured_image)
            : asset('images/default-blog.svg');

        $categoryName = $post->category?->name ?? 'Uncategorized';
        $publishedDate = $post->published_at->format('M d, Y');
        $publishedDatetime = $post->published_at->format('Y-m-d');
        $readingTime = $post->reading_time ?? 5;

        $blogPostsHtml .= <<<HTML
          <!-- Blog Card: {$post->title} -->
          <article class="blog-card">
            <a href="/blog/{$post->slug}" class="blog-card__link">
              <div class="blog-card__media">
                <img
                  src="{$featuredImage}"
                  alt="{$post->title}"
                  width="800"
                  height="450"
                  loading="lazy"
                  fetchpriority="low"
                  decoding="async">
                <span class="blog-card__category" data-category="{$post->category?->slug}">{$categoryName}</span>
              </div>
              <div class="blog-card__content">
                <h3 class="blog-card__title">{$post->title}</h3>
                <p class="blog-card__excerpt">
                  {$post->excerpt}
                </p>
                <div class="blog-card__meta">
                  <time class="blog-card__date" datetime="{$publishedDatetime}">{$publishedDate}</time>
                  <span class="blog-card__reading-time" aria-label="Reading time">
                    <i class="far fa-clock" aria-hidden="true"></i> {$readingTime} min read
                  </span>
                </div>
              </div>
            </a>
          </article>

HTML;
    }

    // Generate dynamic city cards HTML
    $citiesHtml = '';
    foreach ($cities as $city) {
        $tourCount = $city->tour_count;
        $tourText = $tourCount === 1 ? 'tour' : 'tours';
        
        // Get city image - use featured_image or placeholder
        $imageUrl = $city->featured_image_url ?? 'https://placehold.co/400x533/0D4C92/FFFFFF?text=' . urlencode($city->name);
        
        // Get tagline or use default
        $tagline = $city->tagline ?? '';
        
        // Get city name
        $cityName = htmlspecialchars($city->name);
        $citySlug = $city->slug;
        
        // Short description for alt text
        $altText = $city->short_description 
            ? htmlspecialchars(strip_tags($city->short_description))
            : "{$cityName}, Uzbekistan";

        $citiesHtml .= <<<HTML
          <!-- City Card: {$cityName} -->
          <article class="city-card">
            <a href="/destinations/{$citySlug}/" class="city-card__link" aria-label="Discover {$cityName}">
              <div class="city-card__media">
                <img
                  src="{$imageUrl}"
                  alt="{$altText}"
                  width="400"
                  height="533"
                  loading="lazy"
                  decoding="async"
                >
              </div>
              <div class="city-card__content">
                <h3 class="city-card__title">{$cityName}</h3>
                <p class="city-card__tagline">{$tagline}</p>
                <p class="city-card__stats">
                  <i class="fas fa-route" aria-hidden="true"></i>
                  {$tourCount} {$tourText} available
                </p>
                <span class="city-card__cta">
                  Discover {$cityName}
                  <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </span>
              </div>
            </a>
          </article>

HTML;
    }

    // Get 5-star reviews for homepage carousel (7 reviews, mix from different tours)
    $reviews = \App\Models\Review::approved()
        ->where('rating', 5)
        ->with('tour')
        ->orderBy('created_at', 'desc')
        ->take(7)
        ->get();

    // Generate dynamic review cards HTML
    $reviewsHtml = '';
    foreach ($reviews as $review) {
        $avatarUrl = $review->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($review->reviewer_name) . '&size=60&background=0D4C92&color=fff';
        $location = $review->reviewer_location ?? 'Travel Enthusiast';
        $date = $review->created_at->format('F Y');
        $sourceIcon = $review->source === 'tripadvisor' ? 'fab fa-tripadvisor' : 'fas fa-star';
        $sourceName = ucfirst($review->source ?? 'Website');
        $reviewerName = htmlspecialchars($review->reviewer_name);
        $reviewTitle = htmlspecialchars($review->title);
        $reviewContent = htmlspecialchars($review->content);

        $reviewsHtml .= <<<HTML
          <!-- Review Slide: {$reviewerName} -->
          <div class="swiper-slide">
            <article class="review-card">
            <div class="review-card__header">
              <img src="{$avatarUrl}" alt="{$reviewerName}" class="review-card__avatar" width="60" height="60" loading="lazy">
              <div class="review-card__author">
                <h3 class="review-card__name">{$reviewerName}</h3>
                <p class="review-card__location"><i class="fas fa-map-marker-alt"></i> {$location}</p>
              </div>
            </div>
            <div class="review-card__rating">
              <div class="stars" aria-label="Rated 5 out of 5 stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
              </div>
              <p class="review-card__date">Traveled in {$date}</p>
            </div>
            <div class="review-card__content">
              <i class="fas fa-quote-left review-card__quote"></i>
              <h4 class="review-card__title">{$reviewTitle}</h4>
              <p class="review-card__text">{$reviewContent}</p>
              <p class="review-card__source"><i class="{$sourceIcon}"></i> Reviewed on {$sourceName}</p>
            </div>
          </article>
            </div>

HTML;
    }

    // Replace the activities__grid content with dynamic categories
    // Match from opening activities__grid to its closing </div>
    $html = preg_replace(
        '/(<div class="activities__grid">).*?(<\/div>\s*\n\s*<!-- View All Tours Link -->)/s',
        '$1' . "\n" . $categoriesHtml . "\n        $2",
        $html
    );

    // Replace the blog-grid content with dynamic blog posts
    // Match from opening blog-grid to its closing </div> before Section Footer CTA
    $html = preg_replace(
        '/(<div class="blog-grid">).*?(<\/div>\s*\n\s*<!-- Section Footer CTA -->)/s',
        '$1' . "\n" . $blogPostsHtml . "\n        $2",
        $html
    );

    // Replace the places__grid content with dynamic city cards
    // Match from opening places__grid to its closing </div> before View All Destinations CTA
    $html = preg_replace(
        '/(<div class="places__grid">).*?(<\/div>\s*\n\s*<!-- View All Destinations CTA -->)/s',
        '$1' . "\n" . $citiesHtml . "\n        $2",
        $html
    );

    // Replace the swiper-wrapper content with dynamic review slides
    // Match from opening swiper-wrapper to its closing </div>
    $html = preg_replace(
        '/(<div class="swiper-wrapper">).*?(<\/div>\s*
\s*<!-- Swiper Navigation -->)/s',
        '$1' . "
" . $reviewsHtml . "
          " . '$2',
        $html
    );

    return response($html)->header('Content-Type', 'text/html');
});

// Tours listing page - SEO-friendly URL (must come BEFORE /tours/{slug} to avoid conflicts)Route::get('/tours', function (Request $request) {    $cityId = $request->get('city');    return view('tours', compact('cityId'));})->name('tours.index');

// Category landing page - SEO-friendly URL with server-side meta tag injection
Route::get('/tours/category/{slug}', function ($slug) {
    // Find category or 404
    $category = \App\Models\TourCategory::where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();

    // Read the static HTML template
    $html = file_get_contents(public_path('category-landing.html'));

    // Prepare SEO-friendly data
    $locale = app()->getLocale();

    $pageTitle = $category->meta_title[$locale] ?? null;
    if (!$pageTitle) {
        $categoryName = $category->name[$locale] ?? $category->name['en'] ?? 'Category';
        $pageTitle = $categoryName . ' Tours in Uzbekistan | Jahongir Travel';
    }

    $metaDescription = $category->meta_description[$locale] ?? $category->description[$locale] ?? '';
    $metaDescription = substr($metaDescription, 0, 160); // Limit to 160 chars

    $ogImage = $category->hero_image
        ? asset('storage/' . $category->hero_image)
        : asset('images/default-category.jpg');

    $canonicalUrl = url('/tours/category/' . $category->slug);

    // Replace hardcoded meta tags with category-specific ones
    $html = preg_replace(
        '/<title>.*?<\/title>/',
        '<title>' . htmlspecialchars($pageTitle) . '</title>',
        $html
    );

    $html = preg_replace(
        '/<meta name="description" content=".*?">/',
        '<meta name="description" content="' . htmlspecialchars($metaDescription) . '">',
        $html
    );

    // Update canonical URL
    $html = preg_replace(
        '/<link rel="canonical" href=".*?">/',
        '<link rel="canonical" href="' . $canonicalUrl . '">',
        $html
    );

    // Update Open Graph tags
    $html = preg_replace(
        '/<meta property="og:title" content=".*?">/',
        '<meta property="og:title" content="' . htmlspecialchars($pageTitle) . '">',
        $html
    );

    $html = preg_replace(
        '/<meta property="og:description" content=".*?">/',
        '<meta property="og:description" content="' . htmlspecialchars($metaDescription) . '">',
        $html
    );

    $html = preg_replace(
        '/<meta property="og:image" content=".*?">/',
        '<meta property="og:image" content="' . $ogImage . '">',
        $html
    );

    $html = preg_replace(
        '/<meta property="og:url" content=".*?">/',
        '<meta property="og:url" content="' . $canonicalUrl . '">',
        $html
    );

    // Update Twitter Card tags
    $html = preg_replace(
        '/<meta name="twitter:title" content=".*?">/',
        '<meta name="twitter:title" content="' . htmlspecialchars($pageTitle) . '">',
        $html
    );

    $html = preg_replace(
        '/<meta name="twitter:description" content=".*?">/',
        '<meta name="twitter:description" content="' . htmlspecialchars($metaDescription) . '">',
        $html
    );

    $html = preg_replace(
        '/<meta name="twitter:image" content=".*?">/',
        '<meta name="twitter:image" content="' . $ogImage . '">',
        $html
    );

    return response($html)->header('Content-Type', 'text/html');
})->name('tours.category');

// Tour details page - SEO-friendly URL with server-side meta tag injection
Route::get('/tours/{slug}', function ($slug) {
    // Find tour or 404
    $tour = \App\Models\Tour::where('slug', $slug)->firstOrFail();

    // Read the static HTML template
    $html = file_get_contents(public_path('tour-details.html'));

    // Prepare SEO-friendly data
    $pageTitle = $tour->seo_title ?? ($tour->title . ' | Jahongir Travel');
    $metaDescription = $tour->seo_description ?? strip_tags($tour->short_description ?? $tour->description ?? '');
    $metaDescription = substr($metaDescription, 0, 160); // Limit to 160 chars

    $ogImage = $tour->hero_image
        ? asset('storage/' . $tour->hero_image)
        : 'https://jahongirtravel.com/images/tours/default-tour.webp';

    $canonicalUrl = url('/tours/' . $tour->slug);

    // Replace hardcoded meta tags with tour-specific ones
    $html = preg_replace(
        '/<title>.*?<\/title>/',
        '<title>' . htmlspecialchars($pageTitle) . '</title>',
        $html
    );

    $html = preg_replace(
        '/<meta name="description" content=".*?">/',
        '<meta name="description" content="' . htmlspecialchars($metaDescription) . '">',
        $html
    );

    // Update canonical URL
    $html = preg_replace(
        '/<link rel="canonical" href=".*?">/',
        '<link rel="canonical" href="' . $canonicalUrl . '">',
        $html
    );

    // Update Open Graph tags
    $html = preg_replace(
        '/<meta property="og:title" content=".*?">/',
        '<meta property="og:title" content="' . htmlspecialchars($pageTitle) . '">',
        $html
    );

    $html = preg_replace(
        '/<meta property="og:description" content=".*?">/',
        '<meta property="og:description" content="' . htmlspecialchars($metaDescription) . '">',
        $html
    );

    $html = preg_replace(
        '/<meta property="og:image" content=".*?">/',
        '<meta property="og:image" content="' . $ogImage . '">',
        $html
    );

    $html = preg_replace(
        '/<meta property="og:url" content=".*?">/',
        '<meta property="og:url" content="' . $canonicalUrl . '">',
        $html
    );

    // Update Twitter Card tags
    $html = preg_replace(
        '/<meta name="twitter:title" content=".*?">/',
        '<meta name="twitter:title" content="' . htmlspecialchars($pageTitle) . '">',
        $html
    );

    $html = preg_replace(
        '/<meta name="twitter:description" content=".*?">/',
        '<meta name="twitter:description" content="' . htmlspecialchars($metaDescription) . '">',
        $html
    );

    $html = preg_replace(
        '/<meta name="twitter:image" content=".*?">/',
        '<meta name="twitter:image" content="' . $ogImage . '">',
        $html
    );

    // Update JSON-LD structured data
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Tour',
        'name' => $tour->title,
        'description' => strip_tags($tour->description ?? ''),
        'image' => $ogImage,
        'provider' => [
            '@type' => 'Organization',
            'name' => 'Jahongir Travel',
            'url' => url('/'),
        ],
        'url' => $canonicalUrl,
    ];

    if ($tour->price_per_person) {
        $jsonLd['offers'] = [
            '@type' => 'Offer',
            'price' => $tour->price_per_person,
            'priceCurrency' => 'USD',
        ];
    }

    // Replace JSON-LD script (find the script tag and replace its content)
    $html = preg_replace(
        '/<script type="application\/ld\+json">.*?<\/script>/s',
        '<script type="application/ld+json">' . json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>',
        $html,
        1 // Only replace the first occurrence
    );

    return response($html)->header('Content-Type', 'text/html');
})->name('tours.show');

// About page
Route::get('/about', function () {
    return response()->file(public_path('about.html'));
})->name('about');

// Contact page
Route::get('/contact', function () {
    return response()->file(public_path('contact.html'));
})->name('contact');

// Blog listing page
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');

// Blog article page
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])
    ->name('blog.show')
    ->where('slug', '[a-z0-9-]+');

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

// Printable booking estimate route
Route::get('/booking/{booking}/estimate/print', function (Booking $booking) {
    $pricingService = app(PricingService::class);

    // Get all itinerary items for this booking, ordered by date and sort_order
    $itineraryItems = $booking->itineraryItems()
        ->with([
            'assignments.assignable',
            'assignments.transportPrice',
            'assignments.transportInstancePrice',
            'tourItineraryItem'
        ])
        ->orderBy('date')
        ->orderBy('sort_order')
        ->get();

    // Group itinerary items by date (day)
    $groupedByDay = $itineraryItems->groupBy(function ($item) {
        return $item->date ? $item->date->format('Y-m-d') : 'no-date';
    });

    $dayBreakdown = [];
    $totalCost = 0;
    $categoryTotals = [
        'hotel' => 0,
        'transport' => 0,
        'restaurant' => 0,
        'guide' => 0,
        'monument' => 0,
        'other' => 0
    ];

    foreach ($groupedByDay as $date => $dayItems) {
        $dayTotal = 0;
        $dayCategories = [];

        foreach ($dayItems as $item) {
            $assignments = $item->assignments;

            foreach ($assignments as $assignment) {
                $assignable = $assignment->assignable;
                
                // For monuments, use booking's pax_total (total number of people)
                // For other services, use assignment quantity
                $quantity = match($assignment->assignable_type) {
                    \App\Models\Monument::class => $booking->pax_total ?? 1,
                    default => $assignment->quantity ?? 1,
                };
                
                $itemName = '';
                $category = '';

                // Get pricing using the PricingService
                $subServiceId = match($assignment->assignable_type) {
                    \App\Models\Hotel::class => $assignment->room_id,
                    \App\Models\Restaurant::class => $assignment->meal_type_id,
                    \App\Models\Transport::class => $assignment->transport_instance_price_id ?? $assignment->transport_price_type_id,
                    default => null,
                };
                
                $pricing = $pricingService->getPricingBreakdown(
                    $assignment->assignable_type,
                    $assignment->assignable_id,
                    $subServiceId,
                    $booking->start_date
                );

                $unitPrice = $pricing['final_price'] ?? 0;

                switch ($assignment->assignable_type) {
                    case \App\Models\Guide::class:
                        $itemName = $assignable?->name ?? 'Гид удален';
                        $category = 'guide';
                        break;

                    case \App\Models\Restaurant::class:
                        if ($assignment->meal_type_id) {
                            $mealType = \App\Models\MealType::find($assignment->meal_type_id);
                            $itemName = $assignable?->name . ' - ' . $mealType?->name ?? 'Ресторан удален';
                        } else {
                            $itemName = $assignable?->name ?? 'Ресторан удален';
                        }
                        $category = 'restaurant';
                        break;

                    case \App\Models\Hotel::class:
                        if ($assignment->room_id) {
                            $room = \App\Models\Room::find($assignment->room_id);
                            $itemName = $assignable?->name . ' - ' . $room?->name ?? 'Гостиница удалена';
                        } else {
                            $itemName = $assignable?->name ?? 'Гостиница удалена';
                        }
                        $category = 'hotel';
                        break;

                    case \App\Models\Transport::class:
                        try {
                            $itemName = \App\Models\Transport::getEstimateLabel($assignment);
                        } catch (\Exception $e) {
                            // Log error and throw to prevent rendering bad data
                            \Log::error("Failed to generate transport label: " . $e->getMessage());
                            throw new \Exception("Cannot generate estimate: " . $e->getMessage());
                        }
                        $category = 'transport';
                        break;

                    case \App\Models\Monument::class:
                        $itemName = $assignable?->name ?? 'Монумент удален';
                        $category = 'monument';
                        // $unitPrice is already set by PricingService above
                        break;

                    default:
                        $unitPrice = 0;
                        $itemName = 'Неизвестный поставщик';
                        $category = 'other';
                }

                $itemTotal = $unitPrice * $quantity;
                $dayTotal += $itemTotal;

                // Group by category within the day
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
                
                // Accumulate category totals for the summary
                $categoryTotals[$category] += $itemTotal;
            }
        }

        // Sort categories by predefined order
        $categoryOrder = ['hotel', 'transport', 'restaurant', 'guide', 'monument', 'other'];
        $sortedCategories = [];
        foreach ($categoryOrder as $cat) {
            if (isset($dayCategories[$cat])) {
                $sortedCategories[$cat] = $dayCategories[$cat];
            }
        }

        // Get the day title from the first itinerary item of the day
        $dayTitle = 'День ' . (\Carbon\Carbon::parse($date)->diffInDays($booking->start_date) + 1);
        if ($dayItems->isNotEmpty()) {
            $firstItem = $dayItems->first();
            // Try to get title from BookingItineraryItem first, then from TourItineraryItem
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

    // Calculate category percentages
    $categorySummary = [];
    foreach ($categoryTotals as $category => $total) {
        if ($total > 0) {
            $percentage = $totalCost > 0 ? round(($total / $totalCost) * 100, 1) : 0;
            $categorySummary[] = [
                'category' => $category,
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
                },
                'total' => $total,
                'percentage' => $percentage
            ];
        }
    }

    // Sort by total amount (descending)
    usort($categorySummary, function($a, $b) {
        return $b['total'] <=> $a['total'];
    });

    return view('booking-print-estimate', [
        'record' => $booking,
        'dayBreakdown' => $dayBreakdown,
        'categorySummary' => $categorySummary,
        'totalCost' => $totalCost,
    ]);
})->name('booking.estimate.print');

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

// Download individual supplier request PDF
Route::get('/supplier-request/{request}/download', function (\App\Models\SupplierRequest $request) {
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

    // -------- BLOG LISTING (HTMX) --------
    Route::get('/blog/listing', [BlogController::class, 'listing'])
        ->name('blog.listing');
});

// ============================================
// BOOKING CONFIRMATION PAGE (Public-facing)
// ============================================
Route::get('/booking/confirmation/{reference}', [\App\Http\Controllers\Partials\BookingController::class, 'confirmation'])
    ->name('booking.confirmation');

// City/Destination landing page - SEO-friendly URL with server-side meta tag injection
// Destinations index page
Route::get('/destinations/', function () {
    return response()->file(public_path('destinations.html'));
})->name('destinations.index');

Route::get('/destinations/{slug}', function ($slug) {
    // Find city or 404
    $city = \App\Models\City::where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();

    // Read the static HTML template
    $html = file_get_contents(public_path('destination-landing.html'));

    // Prepare SEO-friendly data
    $pageTitle = $city->meta_title ?? ($city->name . ' Tours & Travel Guide | Jahongir Travel');
    $metaDescription = $city->meta_description ?? ($city->short_description ?? '');
    $metaDescription = substr($metaDescription, 0, 160);

    $ogImage = $city->hero_image_url ?? $city->featured_image_url ?? asset('images/default-city.jpg');
    $canonicalUrl = url('/destinations/' . $city->slug);

    // Replace DEFAULT_CITY_ID with actual city ID
    $html = str_replace('DEFAULT_CITY_ID', $city->id, $html);

    // Replace hardcoded meta tags
    $html = preg_replace('/<title>.*?<\/title>/', '<title>' . htmlspecialchars($pageTitle) . '</title>', $html);
    $html = preg_replace('/<meta name="description" content=".*?">/', '<meta name="description" content="' . htmlspecialchars($metaDescription) . '">', $html);
    $html = preg_replace('/<link rel="canonical" href=".*?">/', '<link rel="canonical" href="' . $canonicalUrl . '">', $html);

    return response($html)->header('Content-Type', 'text/html');
})->name('city.show');
