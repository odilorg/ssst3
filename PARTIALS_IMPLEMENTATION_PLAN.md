# PARTIALS IMPLEMENTATION PLAN
**Server-Side Rendered Partials Architecture**

Based on answers from `partials-answers.txt`

---

## 📋 Architecture Summary

| Aspect | Decision | Implementation |
|--------|----------|----------------|
| **Integration** | Keep frontend separate | `jahongir-custom-website` + Laravel partials |
| **Routing** | Partial-specific routes | `/partials/tours/*` |
| **Pages** | Both list and details | Tour list + Tour details |
| **Booking** | Simple forms with AJAX | HTMX form submission |
| **Caching** | Partial-level caching | Different TTLs per section |
| **JS Framework** | HTMX | Declarative partial loading |
| **Search/Filter** | Hybrid | Server initial, client refine |
| **Admin** | Keep Filament /admin | No changes to admin |
| **Beds24** | Later phase | Mock data for now |
| **CORS** | Localhost dev only | Configure for development |

---

## 🗂️ Project Structure

```
D:/xampp82/htdocs/ssst3/  (Backend - Laravel + Filament)
├── app/
│   └── Http/
│       └── Controllers/
│           └── Partials/
│               ├── TourController.php           ← Main controller
│               ├── BookingController.php        ← Booking forms
│               └── SearchController.php         ← Search/filter
├── resources/
│   └── views/
│       └── partials/
│           ├── tours/
│           │   ├── list.blade.php               ← Tour cards grid
│           │   ├── list-item.blade.php          ← Single tour card
│           │   ├── show/
│           │   │   ├── hero.blade.php           ← Hero section
│           │   │   ├── overview.blade.php       ← Overview tab
│           │   │   ├── highlights.blade.php     ← Highlights
│           │   │   ├── itinerary.blade.php      ← Itinerary (if multi-day)
│           │   │   ├── included-excluded.blade.php
│           │   │   ├── faqs.blade.php           ← FAQ accordion
│           │   │   ├── extras.blade.php         ← Add-ons
│           │   │   └── reviews.blade.php        ← Customer reviews
│           ├── bookings/
│           │   ├── form.blade.php               ← Booking form
│           │   ├── confirmation.blade.php       ← Success message
│           │   └── error.blade.php              ← Validation errors
│           └── search/
│               ├── filters.blade.php            ← Filter controls
│               └── results.blade.php            ← Filtered tour cards
└── routes/
    └── web.php                                   ← Partial routes

D:/xampp82/htdocs/jahongir-custom-website/  (Frontend - Static HTML)
├── tour-details.html                             ← Tour detail page shell
├── tours.html                                    ← Tour list page shell
├── assets/
│   ├── js/
│   │   ├── partials-loader.js                   ← Partial loading logic
│   │   └── htmx.min.js                          ← HTMX library
│   └── css/
│       └── (existing styles)
```

---

## 🎯 Implementation Phases

### **Phase 1: Setup & Configuration** (1-2 hours)
- [ ] Install HTMX in frontend
- [ ] Configure Laravel CORS
- [ ] Create partial routes
- [ ] Create base controller

### **Phase 2: Tour List Page** (3-4 hours)
- [ ] Create tour list partial
- [ ] Create tour card component
- [ ] Implement search/filter partials
- [ ] Connect frontend to partials

### **Phase 3: Tour Detail Page** (4-5 hours)
- [ ] Create hero partial
- [ ] Create overview partial
- [ ] Create highlights partial
- [ ] Create itinerary partial
- [ ] Create included/excluded partial
- [ ] Create FAQs partial
- [ ] Create extras partial
- [ ] Create reviews partial

### **Phase 4: Booking System** (3-4 hours)
- [ ] Create booking form partial
- [ ] Create confirmation partial
- [ ] Handle form validation
- [ ] Store booking in database

### **Phase 5: Caching & Optimization** (2-3 hours)
- [ ] Implement partial-level caching
- [ ] Add cache invalidation
- [ ] Performance testing

### **Phase 6: Testing & Polish** (2-3 hours)
- [ ] Manual testing
- [ ] SEO verification
- [ ] Accessibility check
- [ ] Cross-browser testing

**Total Estimated Time: 15-21 hours**

---

## 📝 Detailed Implementation Steps

---

## Phase 1: Setup & Configuration

### Step 1.1: Install HTMX in Frontend

**File:** `D:/xampp82/htdocs/jahongir-custom-website/assets/js/htmx.min.js`

Download HTMX from: https://unpkg.com/htmx.org@1.9.10/dist/htmx.min.js

Or add CDN to your HTML:
```html
<!-- Add before closing </body> tag -->
<script src="https://unpkg.com/htmx.org@1.9.10"></script>
```

---

### Step 1.2: Configure Laravel CORS

**File:** `D:/xampp82/htdocs/ssst3/config/cors.php`

Update CORS configuration:
```php
<?php

return [
    'paths' => [
        'api/*',
        'partials/*',  // ← Add this
        'sanctum/csrf-cookie'
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost',
        'http://localhost:3000',
        'http://127.0.0.1',
        // Add your local frontend URL if different
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
```

**Test CORS:**
```bash
cd D:/xampp82/htdocs/ssst3
php artisan config:cache
```

---

### Step 1.3: Create Partial Routes

**File:** `D:/xampp82/htdocs/ssst3/routes/web.php`

Add partial routes:
```php
<?php

use App\Http\Controllers\Partials\TourController;
use App\Http\Controllers\Partials\BookingController;
use App\Http\Controllers\Partials\SearchController;
use Illuminate\Support\Facades\Route;

// Existing admin routes stay untouched
// ...

// ============================================
// PUBLIC PARTIAL ROUTES
// ============================================

Route::prefix('partials')->name('partials.')->group(function () {

    // Tour List
    Route::get('/tours', [TourController::class, 'list'])
        ->name('tours.list');

    Route::get('/tours/search', [SearchController::class, 'search'])
        ->name('tours.search');

    // Tour Detail Sections
    Route::get('/tours/{slug}/hero', [TourController::class, 'hero'])
        ->name('tours.hero');

    Route::get('/tours/{slug}/overview', [TourController::class, 'overview'])
        ->name('tours.overview');

    Route::get('/tours/{slug}/highlights', [TourController::class, 'highlights'])
        ->name('tours.highlights');

    Route::get('/tours/{slug}/itinerary', [TourController::class, 'itinerary'])
        ->name('tours.itinerary');

    Route::get('/tours/{slug}/included-excluded', [TourController::class, 'includedExcluded'])
        ->name('tours.included-excluded');

    Route::get('/tours/{slug}/faqs', [TourController::class, 'faqs'])
        ->name('tours.faqs');

    Route::get('/tours/{slug}/extras', [TourController::class, 'extras'])
        ->name('tours.extras');

    Route::get('/tours/{slug}/reviews', [TourController::class, 'reviews'])
        ->name('tours.reviews');

    // Booking
    Route::get('/bookings/form/{tour_slug}', [BookingController::class, 'form'])
        ->name('bookings.form');

    Route::post('/bookings', [BookingController::class, 'store'])
        ->name('bookings.store');
});
```

---

### Step 1.4: Create Base Controller

**File:** `D:/xampp82/htdocs/ssst3/app/Http/Controllers/Partials/TourController.php`

```php
<?php

namespace App\Http\Controllers\Partials;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TourController extends Controller
{
    /**
     * Tour list partial
     */
    public function list(Request $request)
    {
        $tours = Cache::remember('tours.list', 3600, function () {
            return Tour::with('city')
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get();
        });

        return view('partials.tours.list', compact('tours'));
    }

    /**
     * Hero section
     */
    public function hero(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.hero', compact('tour'));
    }

    /**
     * Overview section
     */
    public function overview(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.overview', compact('tour'));
    }

    /**
     * Highlights section
     */
    public function highlights(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.highlights', compact('tour'));
    }

    /**
     * Itinerary section
     */
    public function itinerary(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.itinerary', compact('tour'));
    }

    /**
     * Included/Excluded section
     */
    public function includedExcluded(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.included-excluded', compact('tour'));
    }

    /**
     * FAQs section
     */
    public function faqs(string $slug)
    {
        $tour = Tour::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $faqs = Cache::remember("tour.{$slug}.faqs", 86400, function () use ($tour) {
            return $tour->faqs()->orderBy('sort_order')->get();
        });

        return view('partials.tours.show.faqs', compact('tour', 'faqs'));
    }

    /**
     * Extras section
     */
    public function extras(string $slug)
    {
        $tour = Tour::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $extras = Cache::remember("tour.{$slug}.extras", 3600, function () use ($tour) {
            return $tour->activeExtras()->orderBy('sort_order')->get();
        });

        return view('partials.tours.show.extras', compact('tour', 'extras'));
    }

    /**
     * Reviews section
     */
    public function reviews(string $slug, Request $request)
    {
        $tour = Tour::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $page = $request->get('page', 1);

        $reviews = Cache::remember("tour.{$slug}.reviews.page.{$page}", 300, function () use ($tour) {
            return $tour->approvedReviews()
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        });

        return view('partials.tours.show.reviews', compact('tour', 'reviews'));
    }

    /**
     * Helper: Get cached tour
     */
    protected function getCachedTour(string $slug): Tour
    {
        return Cache::remember("tour.{$slug}", 3600, function () use ($slug) {
            return Tour::where('slug', $slug)
                ->where('is_active', true)
                ->with('city')
                ->firstOrFail();
        });
    }
}
```

---

## Phase 2: Tour List Page

### Step 2.1: Create Tour List Partial

**File:** `D:/xampp82/htdocs/ssst3/resources/views/partials/tours/list.blade.php`

```blade
{{-- Tour Cards Grid --}}
<div class="tours-grid">
    @forelse ($tours as $tour)
        @include('partials.tours.list-item', ['tour' => $tour])
    @empty
        <div class="no-tours">
            <p>No tours available at the moment.</p>
        </div>
    @endforelse
</div>

@if ($tours->isEmpty())
    {{-- Show empty state --}}
@endif
```

---

### Step 2.2: Create Tour Card Component

**File:** `D:/xampp82/htdocs/ssst3/resources/views/partials/tours/list-item.blade.php`

```blade
{{-- Single Tour Card --}}
<article class="tour-card" data-tour-id="{{ $tour->id }}">
    {{-- Hero Image --}}
    <div class="tour-card__image">
        @if ($tour->hero_image)
            <img src="{{ asset($tour->hero_image) }}"
                 alt="{{ $tour->title }}"
                 loading="lazy">
        @else
            <img src="{{ asset('images/placeholder-tour.jpg') }}"
                 alt="{{ $tour->title }}">
        @endif

        {{-- Tour Type Badge --}}
        <span class="tour-card__badge tour-card__badge--{{ $tour->tour_type }}">
            {{ ucfirst($tour->tour_type) }}
        </span>
    </div>

    {{-- Content --}}
    <div class="tour-card__content">
        {{-- Title --}}
        <h3 class="tour-card__title">
            <a href="/tour-details.html?slug={{ $tour->slug }}">
                {{ $tour->title }}
            </a>
        </h3>

        {{-- Meta Info --}}
        <div class="tour-card__meta">
            @if ($tour->city)
                <span class="tour-card__location">
                    <i class="icon-location"></i>
                    {{ $tour->city->name }}
                </span>
            @endif

            <span class="tour-card__duration">
                <i class="icon-clock"></i>
                {{ $tour->duration_text ?? $tour->duration_days . ' days' }}
            </span>

            @if ($tour->rating > 0)
                <span class="tour-card__rating">
                    <i class="icon-star"></i>
                    {{ number_format($tour->rating, 1) }}
                    <small>({{ $tour->review_count }})</small>
                </span>
            @endif
        </div>

        {{-- Short Description --}}
        <p class="tour-card__description">
            {{ Str::limit($tour->short_description, 120) }}
        </p>

        {{-- Price & CTA --}}
        <div class="tour-card__footer">
            <div class="tour-card__price">
                <span class="tour-card__price-label">From</span>
                <span class="tour-card__price-amount">
                    ${{ number_format($tour->price_per_person, 0) }}
                </span>
                <span class="tour-card__price-unit">per person</span>
            </div>

            <a href="/tour-details.html?slug={{ $tour->slug }}"
               class="btn btn--primary">
                View Details
            </a>
        </div>
    </div>
</article>
```

---

### Step 2.3: Update Frontend Tour List Page

**File:** `D:/xampp82/htdocs/jahongir-custom-website/tours.html`

Add this where tour cards should appear:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tours | Jahongir Travel</title>
    <!-- Your existing CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <!-- Your existing header -->
    </header>

    <!-- Main Content -->
    <main>
        <section class="tours-section">
            <div class="container">
                <h1>Our Tours</h1>

                {{-- Search/Filter Section --}}
                <div class="filters-container">
                    {{-- Will add in Step 2.4 --}}
                </div>

                {{-- Tour Cards Grid (Loaded via HTMX) --}}
                <div id="tours-list-container"
                     hx-get="http://localhost/ssst3/partials/tours"
                     hx-trigger="load"
                     hx-swap="innerHTML">

                    {{-- Loading Placeholder --}}
                    <div class="loading">
                        <p>Loading tours...</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <!-- Your existing footer -->
    </footer>

    <!-- Scripts -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
```

---

### Step 2.4: Create Search/Filter Partials

**File:** `D:/xampp82/htdocs/ssst3/app/Http/Controllers/Partials/SearchController.php`

```php
<?php

namespace App\Http\Controllers\Partials;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = Tour::query()->where('is_active', true);

        // Search by keyword
        if ($request->filled('q')) {
            $keyword = $request->get('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('short_description', 'like', "%{$keyword}%")
                  ->orWhere('long_description', 'like', "%{$keyword}%");
            });
        }

        // Filter by duration
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

        // Filter by price range
        if ($request->filled('price')) {
            $price = $request->get('price');
            if ($price === '0-100') {
                $query->where('price_per_person', '<=', 100);
            } elseif ($price === '100-500') {
                $query->whereBetween('price_per_person', [100, 500]);
            } elseif ($price === '500+') {
                $query->where('price_per_person', '>=', 500);
            }
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city_id', $request->get('city'));
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
```

**File:** `D:/xampp82/htdocs/ssst3/resources/views/partials/search/filters.blade.php`

```blade
<form id="tour-filters"
      hx-get="/partials/tours/search"
      hx-trigger="change, submit"
      hx-target="#tours-list-container"
      hx-swap="innerHTML">

    {{-- Search Input --}}
    <div class="filter-group">
        <label for="search-query">Search</label>
        <input type="text"
               id="search-query"
               name="q"
               placeholder="Search tours...">
    </div>

    {{-- Duration Filter --}}
    <div class="filter-group">
        <label for="duration">Duration</label>
        <select id="duration" name="duration">
            <option value="">All Durations</option>
            <option value="1">1 Day</option>
            <option value="2-5">2-5 Days</option>
            <option value="6+">6+ Days</option>
        </select>
    </div>

    {{-- Price Filter --}}
    <div class="filter-group">
        <label for="price">Price Range</label>
        <select id="price" name="price">
            <option value="">All Prices</option>
            <option value="0-100">Under $100</option>
            <option value="100-500">$100 - $500</option>
            <option value="500+">$500+</option>
        </select>
    </div>

    {{-- Sort --}}
    <div class="filter-group">
        <label for="sort">Sort By</label>
        <select id="sort" name="sort">
            <option value="latest">Latest</option>
            <option value="price_low">Price: Low to High</option>
            <option value="price_high">Price: High to Low</option>
            <option value="rating">Highest Rated</option>
        </select>
    </div>

    {{-- Submit Button (optional with HTMX) --}}
    <button type="submit" class="btn btn--primary">
        Apply Filters
    </button>
</form>
```

---

## Phase 3: Tour Detail Page

### Step 3.1: Create Hero Partial

**File:** `D:/xampp82/htdocs/ssst3/resources/views/partials/tours/show/hero.blade.php`

```blade
{{-- Tour Hero Section --}}
<section class="tour-hero">
    {{-- Background Image --}}
    @if ($tour->hero_image)
        <div class="tour-hero__image"
             style="background-image: url('{{ asset($tour->hero_image) }}')">
        </div>
    @endif

    {{-- Content Overlay --}}
    <div class="tour-hero__content">
        <div class="container">
            {{-- Breadcrumbs --}}
            <nav class="breadcrumbs">
                <a href="/">Home</a>
                <span>/</span>
                <a href="/tours.html">Tours</a>
                <span>/</span>
                <span>{{ $tour->title }}</span>
            </nav>

            {{-- Title --}}
            <h1 class="tour-hero__title">{{ $tour->title }}</h1>

            {{-- Meta Info --}}
            <div class="tour-hero__meta">
                @if ($tour->city)
                    <span class="meta-item">
                        <i class="icon-location"></i>
                        {{ $tour->city->name }}
                    </span>
                @endif

                <span class="meta-item">
                    <i class="icon-clock"></i>
                    {{ $tour->duration_text ?? $tour->duration_days . ' days' }}
                </span>

                @if ($tour->rating > 0)
                    <span class="meta-item">
                        <i class="icon-star"></i>
                        {{ number_format($tour->rating, 1) }}
                        ({{ $tour->review_count }} reviews)
                    </span>
                @endif

                <span class="meta-item meta-item--badge">
                    {{ ucfirst($tour->tour_type) }} Tour
                </span>
            </div>

            {{-- Price & CTA --}}
            <div class="tour-hero__booking">
                <div class="price">
                    <span class="price__label">From</span>
                    <span class="price__amount">
                        ${{ number_format($tour->price_per_person, 0) }}
                    </span>
                    <span class="price__unit">per person</span>
                </div>

                <button class="btn btn--primary btn--lg"
                        hx-get="/partials/bookings/form/{{ $tour->slug }}"
                        hx-target="#booking-modal-content"
                        hx-swap="innerHTML"
                        onclick="openBookingModal()">
                    Book Now
                </button>
            </div>
        </div>
    </div>
</section>
```

---

### Step 3.2: Create Overview Partial

**File:** `D:/xampp82/htdocs/ssst3/resources/views/partials/tours/show/overview.blade.php`

```blade
{{-- Tour Overview Section --}}
<section class="tour-overview">
    {{-- Short Description --}}
    @if ($tour->short_description)
        <p class="tour-overview__intro">
            {{ $tour->short_description }}
        </p>
    @endif

    {{-- Long Description --}}
    @if ($tour->long_description)
        <div class="tour-overview__description">
            {!! nl2br(e($tour->long_description)) !!}
        </div>
    @endif

    {{-- Quick Info Grid --}}
    <div class="tour-overview__info-grid">
        <div class="info-card">
            <i class="icon-users"></i>
            <h4>Group Size</h4>
            <p>{{ $tour->min_guests }}-{{ $tour->max_guests }} people</p>
        </div>

        @if (!empty($tour->languages))
            <div class="info-card">
                <i class="icon-language"></i>
                <h4>Languages</h4>
                <p>{{ implode(', ', $tour->languages) }}</p>
            </div>
        @endif

        @if ($tour->has_hotel_pickup)
            <div class="info-card">
                <i class="icon-car"></i>
                <h4>Hotel Pickup</h4>
                <p>Available ({{ $tour->pickup_radius_km }}km radius)</p>
            </div>
        @endif

        <div class="info-card">
            <i class="icon-calendar"></i>
            <h4>Booking</h4>
            <p>Book {{ $tour->min_booking_hours }}h in advance</p>
        </div>
    </div>
</section>
```

---

### Step 3.3: Create Highlights Partial

**File:** `D:/xampp82/htdocs/ssst3/resources/views/partials/tours/show/highlights.blade.php`

```blade
{{-- Tour Highlights Section --}}
@if (!empty($tour->highlights) && is_array($tour->highlights))
    <section class="tour-highlights">
        <h2>Tour Highlights</h2>

        <ul class="highlights-list">
            @foreach ($tour->highlights as $highlight)
                <li class="highlight-item">
                    <i class="icon-check"></i>
                    <span>{{ $highlight }}</span>
                </li>
            @endforeach
        </ul>
    </section>
@endif
```

---

### Step 3.4: Create Itinerary Partial

**File:** `D:/xampp82/htdocs/ssst3/resources/views/partials/tours/show/itinerary.blade.php`

```blade
{{-- Tour Itinerary Section --}}
@if ($tour->duration_days > 1)
    <section class="tour-itinerary">
        <h2>Day by Day Itinerary</h2>

        {{-- Use existing itineraryItems relationship if available --}}
        @if ($tour->itineraryItems && $tour->itineraryItems->isNotEmpty())
            <div class="itinerary-timeline">
                @foreach ($tour->itineraryItems as $item)
                    <div class="itinerary-day">
                        <div class="itinerary-day__number">
                            Day {{ $loop->iteration }}
                        </div>
                        <div class="itinerary-day__content">
                            <h3>{{ $item->title }}</h3>
                            @if ($item->description)
                                <p>{{ $item->description }}</p>
                            @endif
                            @if ($item->start_time)
                                <span class="time">
                                    <i class="icon-clock"></i>
                                    {{ $item->start_time }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Show placeholder if no itinerary yet --}}
            <p>Detailed itinerary coming soon. Please contact us for more information.</p>
        @endif
    </section>
@endif
```

---

### Step 3.5: Create Included/Excluded Partial

**File:** `D:/xampp82/htdocs/ssst3/resources/views/partials/tours/show/included-excluded.blade.php`

```blade
{{-- What's Included / Excluded Section --}}
<section class="tour-inclusions">
    <div class="inclusions-grid">
        {{-- What's Included --}}
        @if (!empty($tour->included_items) && is_array($tour->included_items))
            <div class="inclusions-column">
                <h3 class="inclusions-title inclusions-title--included">
                    <i class="icon-check-circle"></i>
                    What's Included
                </h3>
                <ul class="inclusions-list inclusions-list--included">
                    @foreach ($tour->included_items as $item)
                        <li>
                            <i class="icon-check"></i>
                            <span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- What's Not Included --}}
        @if (!empty($tour->excluded_items) && is_array($tour->excluded_items))
            <div class="inclusions-column">
                <h3 class="inclusions-title inclusions-title--excluded">
                    <i class="icon-x-circle"></i>
                    What's Not Included
                </h3>
                <ul class="inclusions-list inclusions-list--excluded">
                    @foreach ($tour->excluded_items as $item)
                        <li>
                            <i class="icon-x"></i>
                            <span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- Requirements Section --}}
    @if (!empty($tour->requirements) && is_array($tour->requirements))
        <div class="tour-requirements">
            <h3 class="requirements-title">
                <i class="icon-info"></i>
                Requirements & Recommendations
            </h3>
            <ul class="requirements-list">
                @foreach ($tour->requirements as $requirement)
                    <li>{{ $requirement }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</section>
```

---

### Step 3.6: Create FAQs Partial

**File:** `D:/xampp82/htdocs/ssst3/resources/views/partials/tours/show/faqs.blade.php`

```blade
{{-- Tour FAQs Section --}}
@if ($faqs->isNotEmpty())
    <section class="tour-faqs">
        <h2>Frequently Asked Questions</h2>

        <div class="faq-accordion">
            @foreach ($faqs as $faq)
                <div class="faq-item" data-faq-id="{{ $faq->id }}">
                    <button class="faq-question"
                            onclick="toggleFaq({{ $faq->id }})">
                        <span>{{ $faq->question }}</span>
                        <i class="icon-chevron-down"></i>
                    </button>
                    <div class="faq-answer" id="faq-answer-{{ $faq->id }}">
                        <p>{{ $faq->answer }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif

<script>
function toggleFaq(id) {
    const answer = document.getElementById('faq-answer-' + id);
    const item = answer.closest('.faq-item');

    // Close all other FAQs
    document.querySelectorAll('.faq-item').forEach(el => {
        if (el !== item) {
            el.classList.remove('active');
        }
    });

    // Toggle current FAQ
    item.classList.toggle('active');
}
</script>
```

---

### Step 3.7: Create Extras Partial

**File:** `D:/xampp82/htdocs/ssst3/resources/views/partials/tours/show/extras.blade.php`

```blade
{{-- Tour Extras (Add-ons) Section --}}
@if ($extras->isNotEmpty())
    <section class="tour-extras">
        <h2>Add Extra Services</h2>
        <p class="tour-extras__intro">
            Enhance your experience with these optional add-ons
        </p>

        <div class="extras-grid">
            @foreach ($extras as $extra)
                <div class="extra-card">
                    @if ($extra->icon)
                        <div class="extra-card__icon">
                            <i class="icon-{{ $extra->icon }}"></i>
                        </div>
                    @endif

                    <div class="extra-card__content">
                        <h3 class="extra-card__title">{{ $extra->name }}</h3>

                        @if ($extra->description)
                            <p class="extra-card__description">
                                {{ $extra->description }}
                            </p>
                        @endif

                        <div class="extra-card__footer">
                            <div class="extra-card__price">
                                <span class="price-amount">
                                    ${{ number_format($extra->price, 0) }}
                                </span>
                                <span class="price-unit">
                                    {{ str_replace('_', ' ', $extra->price_unit) }}
                                </span>
                            </div>

                            <button class="btn btn--secondary btn--sm"
                                    data-extra-id="{{ $extra->id }}"
                                    onclick="addExtra({{ $extra->id }})">
                                Add to Booking
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif
```

---

### Step 3.8: Create Reviews Partial

**File:** `D:/xampp82/htdocs/ssst3/resources/views/partials/tours/show/reviews.blade.php`

```blade
{{-- Tour Reviews Section --}}
<section class="tour-reviews">
    <div class="reviews-header">
        <h2>Customer Reviews</h2>

        @if ($tour->rating > 0)
            <div class="reviews-summary">
                <div class="rating-display">
                    <span class="rating-number">{{ number_format($tour->rating, 1) }}</span>
                    <div class="rating-stars">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= floor($tour->rating))
                                <i class="icon-star-filled"></i>
                            @elseif ($i - 0.5 <= $tour->rating)
                                <i class="icon-star-half"></i>
                            @else
                                <i class="icon-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="rating-count">
                        Based on {{ $tour->review_count }}
                        {{ Str::plural('review', $tour->review_count) }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    {{-- Reviews List --}}
    @if ($reviews->isNotEmpty())
        <div class="reviews-list">
            @foreach ($reviews as $review)
                <article class="review-card">
                    {{-- Review Header --}}
                    <div class="review-card__header">
                        <div class="reviewer-info">
                            <div class="reviewer-avatar">
                                {{ substr($review->reviewer_name, 0, 1) }}
                            </div>
                            <div class="reviewer-details">
                                <h4 class="reviewer-name">
                                    {{ $review->reviewer_name }}
                                    @if ($review->is_verified)
                                        <span class="verified-badge"
                                              title="Verified booking">
                                            <i class="icon-check-circle"></i>
                                        </span>
                                    @endif
                                </h4>
                                @if ($review->reviewer_location)
                                    <span class="reviewer-location">
                                        {{ $review->reviewer_location }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="review-meta">
                            <div class="review-rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review->rating)
                                        <i class="icon-star-filled"></i>
                                    @else
                                        <i class="icon-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <time class="review-date">
                                {{ $review->created_at->diffForHumans() }}
                            </time>
                        </div>
                    </div>

                    {{-- Review Content --}}
                    <div class="review-card__body">
                        @if ($review->title)
                            <h5 class="review-title">{{ $review->title }}</h5>
                        @endif
                        <p class="review-content">{{ $review->content }}</p>
                    </div>

                    {{-- Review Source --}}
                    @if ($review->source !== 'website')
                        <div class="review-card__footer">
                            <span class="review-source">
                                Originally posted on
                                <strong>{{ ucfirst($review->source) }}</strong>
                            </span>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($reviews->hasPages())
            <div class="reviews-pagination">
                @if ($reviews->previousPageUrl())
                    <button hx-get="{{ $reviews->previousPageUrl() }}"
                            hx-target=".reviews-list"
                            hx-swap="outerHTML"
                            class="btn btn--secondary">
                        Previous
                    </button>
                @endif

                <span class="pagination-info">
                    Page {{ $reviews->currentPage() }} of {{ $reviews->lastPage() }}
                </span>

                @if ($reviews->nextPageUrl())
                    <button hx-get="{{ $reviews->nextPageUrl() }}"
                            hx-target=".reviews-list"
                            hx-swap="outerHTML"
                            class="btn btn--secondary">
                        Next
                    </button>
                @endif
            </div>
        @endif
    @else
        <div class="no-reviews">
            <p>No reviews yet. Be the first to review this tour!</p>
        </div>
    @endif
</section>
```

---

### Step 3.9: Update Frontend Tour Details Page

**File:** `D:/xampp82/htdocs/jahongir-custom-website/tour-details.html`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tour Details | Jahongir Travel</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <!-- Your existing header -->
    </header>

    <!-- Main Content -->
    <main>
        {{-- Hero Section --}}
        <div id="tour-hero"
             hx-get=""
             hx-trigger="load"
             hx-swap="innerHTML">
            <div class="loading">Loading tour...</div>
        </div>

        {{-- Tour Content --}}
        <div class="container">
            {{-- Tabs Navigation --}}
            <div class="tour-tabs">
                <button class="tab-button active" data-tab="overview">Overview</button>
                <button class="tab-button" data-tab="highlights">Highlights</button>
                <button class="tab-button" data-tab="itinerary">Itinerary</button>
                <button class="tab-button" data-tab="included">What's Included</button>
                <button class="tab-button" data-tab="faqs">FAQs</button>
                <button class="tab-button" data-tab="extras">Add-ons</button>
                <button class="tab-button" data-tab="reviews">Reviews</button>
            </div>

            {{-- Tab Content Panels --}}
            <div class="tour-content">
                <div id="tab-overview" class="tab-panel active"
                     hx-get=""
                     hx-trigger="load"
                     hx-swap="innerHTML">
                    <div class="loading">Loading...</div>
                </div>

                <div id="tab-highlights" class="tab-panel"
                     hx-get=""
                     hx-trigger="revealed"
                     hx-swap="innerHTML">
                    <div class="loading">Loading...</div>
                </div>

                <div id="tab-itinerary" class="tab-panel"
                     hx-get=""
                     hx-trigger="revealed"
                     hx-swap="innerHTML">
                    <div class="loading">Loading...</div>
                </div>

                <div id="tab-included" class="tab-panel"
                     hx-get=""
                     hx-trigger="revealed"
                     hx-swap="innerHTML">
                    <div class="loading">Loading...</div>
                </div>

                <div id="tab-faqs" class="tab-panel"
                     hx-get=""
                     hx-trigger="revealed"
                     hx-swap="innerHTML">
                    <div class="loading">Loading...</div>
                </div>

                <div id="tab-extras" class="tab-panel"
                     hx-get=""
                     hx-trigger="revealed"
                     hx-swap="innerHTML">
                    <div class="loading">Loading...</div>
                </div>

                <div id="tab-reviews" class="tab-panel"
                     hx-get=""
                     hx-trigger="revealed"
                     hx-swap="innerHTML">
                    <div class="loading">Loading...</div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <!-- Your existing footer -->
    </footer>

    <!-- Booking Modal -->
    <div id="booking-modal" class="modal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeBookingModal()">×</button>
            <div id="booking-modal-content"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    <script src="assets/js/tour-details.js"></script>
</body>
</html>
```

**File:** `D:/xampp82/htdocs/jahongir-custom-website/assets/js/tour-details.js`

```javascript
// Get tour slug from URL
function getTourSlug() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('slug') || 'samarkand-city-tour';
}

// Initialize tour partials on page load
document.addEventListener('DOMContentLoaded', function() {
    const tourSlug = getTourSlug();
    const baseUrl = 'http://localhost/ssst3/partials/tours';

    // Set HTMX URLs dynamically
    document.querySelector('#tour-hero').setAttribute('hx-get', `${baseUrl}/${tourSlug}/hero`);
    document.querySelector('#tab-overview').setAttribute('hx-get', `${baseUrl}/${tourSlug}/overview`);
    document.querySelector('#tab-highlights').setAttribute('hx-get', `${baseUrl}/${tourSlug}/highlights`);
    document.querySelector('#tab-itinerary').setAttribute('hx-get', `${baseUrl}/${tourSlug}/itinerary`);
    document.querySelector('#tab-included').setAttribute('hx-get', `${baseUrl}/${tourSlug}/included-excluded`);
    document.querySelector('#tab-faqs').setAttribute('hx-get', `${baseUrl}/${tourSlug}/faqs`);
    document.querySelector('#tab-extras').setAttribute('hx-get', `${baseUrl}/${tourSlug}/extras`);
    document.querySelector('#tab-reviews').setAttribute('hx-get', `${baseUrl}/${tourSlug}/reviews`);

    // Re-process HTMX attributes
    htmx.process(document.body);

    // Tab switching logic
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;

            // Remove active class from all
            tabButtons.forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.remove('active');
            });

            // Add active class to current
            this.classList.add('active');
            document.getElementById(`tab-${tabName}`).classList.add('active');
        });
    });
});

// Booking modal functions
function openBookingModal() {
    document.getElementById('booking-modal').classList.add('active');
}

function closeBookingModal() {
    document.getElementById('booking-modal').classList.remove('active');
}

// Add extra to booking (for later implementation)
function addExtra(extraId) {
    console.log('Adding extra:', extraId);
    // TODO: Implement cart logic
}
```

---

## Phase 4: Booking System

### Step 4.1: Create Booking Controller

**File:** `D:/xampp82/htdocs/ssst3/app/Http/Controllers/Partials/BookingController.php`

```php
<?php

namespace App\Http\Controllers\Partials;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Show booking form
     */
    public function form(string $tourSlug)
    {
        $tour = Tour::where('slug', $tourSlug)
            ->where('is_active', true)
            ->with('activeExtras')
            ->firstOrFail();

        return view('partials.bookings.form', compact('tour'));
    }

    /**
     * Store booking
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'tour_id' => 'required|exists:tours,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'start_date' => 'required|date|after:today',
            'pax_total' => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:1000',
            'extras' => 'nullable|array',
            'extras.*' => 'exists:tour_extras,id',
        ]);

        if ($validator->fails()) {
            return view('partials.bookings.error', [
                'errors' => $validator->errors()
            ])->setStatusCode(422);
        }

        $validated = $validator->validated();

        // Find or create customer
        $customer = Customer::firstOrCreate(
            ['email' => $validated['customer_email']],
            [
                'name' => $validated['customer_name'],
                'phone' => $validated['customer_phone'] ?? null,
            ]
        );

        // Get tour
        $tour = Tour::findOrFail($validated['tour_id']);

        // Check capacity
        if ($validated['pax_total'] > $tour->max_guests) {
            return view('partials.bookings.error', [
                'message' => "Maximum {$tour->max_guests} guests allowed for this tour."
            ])->setStatusCode(422);
        }

        // Calculate pricing
        $totalPrice = $tour->price_per_person * $validated['pax_total'];

        // Create booking
        $booking = Booking::create([
            'customer_id' => $customer->id,
            'tour_id' => $tour->id,
            'start_date' => $validated['start_date'],
            'pax_total' => $validated['pax_total'],
            'status' => 'pending',
            'currency' => $tour->currency,
            'total_price' => $totalPrice,
            'notes' => $validated['special_requests'] ?? null,
        ]);

        // Attach extras if selected
        if (!empty($validated['extras'])) {
            foreach ($validated['extras'] as $extraId) {
                $extra = $tour->extras()->find($extraId);
                if ($extra) {
                    $booking->extras()->attach($extraId, [
                        'price_at_booking' => $extra->price,
                        'quantity' => 1,
                    ]);
                    $totalPrice += $extra->price;
                }
            }

            // Update total price
            $booking->update(['total_price' => $totalPrice]);
        }

        // Return success partial
        return view('partials.bookings.confirmation', compact('booking'));
    }
}
```

---

### Step 4.2: Create Booking Form Partial

**File:** `D:/xampp82/htdocs/ssst3/resources/views/partials/bookings/form.blade.php`

```blade
{{-- Booking Form --}}
<div class="booking-form">
    <h2>Book {{ $tour->title }}</h2>

    <form id="booking-form"
          hx-post="/partials/bookings"
          hx-target="#booking-result"
          hx-swap="innerHTML">

        @csrf

        <input type="hidden" name="tour_id" value="{{ $tour->id }}">

        {{-- Customer Info --}}
        <div class="form-section">
            <h3>Your Information</h3>

            <div class="form-group">
                <label for="customer_name">Full Name *</label>
                <input type="text"
                       id="customer_name"
                       name="customer_name"
                       required>
            </div>

            <div class="form-group">
                <label for="customer_email">Email Address *</label>
                <input type="email"
                       id="customer_email"
                       name="customer_email"
                       required>
            </div>

            <div class="form-group">
                <label for="customer_phone">Phone Number</label>
                <input type="tel"
                       id="customer_phone"
                       name="customer_phone">
            </div>
        </div>

        {{-- Booking Details --}}
        <div class="form-section">
            <h3>Booking Details</h3>

            <div class="form-group">
                <label for="start_date">Tour Date *</label>
                <input type="date"
                       id="start_date"
                       name="start_date"
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                       required>
            </div>

            <div class="form-group">
                <label for="pax_total">Number of Guests *</label>
                <input type="number"
                       id="pax_total"
                       name="pax_total"
                       min="{{ $tour->min_guests }}"
                       max="{{ $tour->max_guests }}"
                       value="{{ $tour->min_guests }}"
                       required>
                <small>Min: {{ $tour->min_guests }}, Max: {{ $tour->max_guests }}</small>
            </div>
        </div>

        {{-- Extras --}}
        @if ($tour->activeExtras->isNotEmpty())
            <div class="form-section">
                <h3>Add Extra Services (Optional)</h3>

                @foreach ($tour->activeExtras as $extra)
                    <div class="form-checkbox">
                        <input type="checkbox"
                               id="extra_{{ $extra->id }}"
                               name="extras[]"
                               value="{{ $extra->id }}">
                        <label for="extra_{{ $extra->id }}">
                            <strong>{{ $extra->name }}</strong>
                            <span class="extra-price">
                                +${{ number_format($extra->price, 0) }}
                                ({{ str_replace('_', ' ', $extra->price_unit) }})
                            </span>
                            @if ($extra->description)
                                <p class="extra-description">{{ $extra->description }}</p>
                            @endif
                        </label>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Special Requests --}}
        <div class="form-section">
            <h3>Special Requests</h3>
            <div class="form-group">
                <label for="special_requests">
                    Any special requirements or requests? (Optional)
                </label>
                <textarea id="special_requests"
                          name="special_requests"
                          rows="4"></textarea>
            </div>
        </div>

        {{-- Price Summary --}}
        <div class="booking-summary">
            <h3>Price Summary</h3>
            <div class="summary-row">
                <span>Tour Price:</span>
                <span>${{ number_format($tour->price_per_person, 0) }} × <span id="guests-display">1</span> guests</span>
            </div>
            <div class="summary-row summary-total">
                <strong>Total:</strong>
                <strong id="total-price">${{ number_format($tour->price_per_person, 0) }}</strong>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="form-actions">
            <button type="submit" class="btn btn--primary btn--lg">
                Confirm Booking
            </button>
        </div>

        {{-- Result Container --}}
        <div id="booking-result"></div>
    </form>
</div>

<script>
// Update price calculation
document.getElementById('pax_total').addEventListener('input', function() {
    const guests = parseInt(this.value) || 1;
    const pricePerPerson = {{ $tour->price_per_person }};
    const total = pricePerPerson * guests;

    document.getElementById('guests-display').textContent = guests;
    document.getElementById('total-price').textContent = '$' + total.toLocaleString();
});
</script>
```

---

### Step 4.3: Create Confirmation Partial

**File:** `D:/xampp82/htdocs/ssst3/resources/views/partials/bookings/confirmation.blade.php`

```blade
{{-- Booking Confirmation --}}
<div class="booking-confirmation">
    <div class="confirmation-icon">
        <svg viewBox="0 0 24 24" width="64" height="64">
            <circle cx="12" cy="12" r="10" fill="#10b981"/>
            <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" fill="none"/>
        </svg>
    </div>

    <h2>Booking Confirmed!</h2>
    <p class="confirmation-message">
        Thank you for booking with Jahongir Travel. We've received your booking request.
    </p>

    <div class="booking-details">
        <h3>Booking Details</h3>

        <div class="detail-row">
            <span class="label">Booking Reference:</span>
            <strong class="value">{{ $booking->reference }}</strong>
        </div>

        <div class="detail-row">
            <span class="label">Tour:</span>
            <span class="value">{{ $booking->tour->title }}</span>
        </div>

        <div class="detail-row">
            <span class="label">Date:</span>
            <span class="value">{{ $booking->start_date->format('F j, Y') }}</span>
        </div>

        <div class="detail-row">
            <span class="label">Guests:</span>
            <span class="value">{{ $booking->pax_total }} {{ Str::plural('person', $booking->pax_total) }}</span>
        </div>

        <div class="detail-row">
            <span class="label">Total Price:</span>
            <strong class="value">${{ number_format($booking->total_price, 2) }}</strong>
        </div>

        <div class="detail-row">
            <span class="label">Status:</span>
            <span class="badge badge--{{ $booking->status }}">
                {{ ucfirst($booking->status) }}
            </span>
        </div>
    </div>

    <div class="next-steps">
        <h4>What happens next?</h4>
        <ol>
            <li>You'll receive a confirmation email at {{ $booking->customer->email }} shortly</li>
            <li>Our team will review your booking within 24 hours</li>
            <li>We'll send you payment instructions and final details</li>
            <li>Enjoy your tour!</li>
        </ol>
    </div>

    <div class="confirmation-actions">
        <a href="/tours.html" class="btn btn--secondary">
            Browse More Tours
        </a>
        <button class="btn btn--primary" onclick="printBooking()">
            Print Confirmation
        </button>
    </div>
</div>

<script>
function printBooking() {
    window.print();
}

// Close modal after 10 seconds (optional)
setTimeout(() => {
    if (typeof closeBookingModal === 'function') {
        closeBookingModal();
    }
}, 10000);
</script>
```

---

### Step 4.4: Create Error Partial

**File:** `D:/xampp82/htdocs/ssst3/resources/views/partials/bookings/error.blade.php`

```blade
{{-- Booking Error --}}
<div class="booking-error">
    <div class="error-icon">
        <svg viewBox="0 0 24 24" width="48" height="48">
            <circle cx="12" cy="12" r="10" fill="#ef4444"/>
            <path d="M9 9l6 6M15 9l-6 6" stroke="white" stroke-width="2"/>
        </svg>
    </div>

    <h3>Booking Failed</h3>

    @if (isset($message))
        <p class="error-message">{{ $message }}</p>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="error-list">
            <p><strong>Please fix the following errors:</strong></p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <button class="btn btn--secondary" onclick="this.closest('#booking-result').innerHTML = ''">
        Try Again
    </button>
</div>
```

---

## Phase 5: Caching & Optimization

### Step 5.1: Implement Cache Invalidation

**File:** `D:/xampp82/htdocs/ssst3/app/Observers/TourObserver.php`

Create observer:
```bash
cd D:/xampp82/htdocs/ssst3
php artisan make:observer TourObserver --model=Tour
```

```php
<?php

namespace App\Observers;

use App\Models\Tour;
use Illuminate\Support\Facades\Cache;

class TourObserver
{
    /**
     * Handle the Tour "updated" event.
     */
    public function updated(Tour $tour): void
    {
        $this->clearCache($tour);
    }

    /**
     * Handle the Tour "deleted" event.
     */
    public function deleted(Tour $tour): void
    {
        $this->clearCache($tour);
    }

    /**
     * Clear all caches related to this tour
     */
    protected function clearCache(Tour $tour): void
    {
        Cache::forget("tour.{$tour->slug}");
        Cache::forget("tour.{$tour->slug}.faqs");
        Cache::forget("tour.{$tour->slug}.extras");
        Cache::forget("tour.{$tour->slug}.reviews.page.1");
        Cache::forget('tours.list');
    }
}
```

Register in `AppServiceProvider`:
```php
use App\Models\Tour;
use App\Observers\TourObserver;

public function boot(): void
{
    Tour::observe(TourObserver::class);
}
```

---

### Step 5.2: Add Response Caching Headers

**File:** Update `TourController` methods with cache headers:

```php
public function list(Request $request)
{
    $tours = Cache::remember('tours.list', 3600, function () {
        return Tour::with('city')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    });

    return response()
        ->view('partials.tours.list', compact('tours'))
        ->header('Cache-Control', 'public, max-age=3600');
}
```

---

## Phase 6: Testing Checklist

### Manual Testing Checklist:

- [ ] **Tour List Page:**
  - [ ] Tour cards load correctly
  - [ ] Images display properly
  - [ ] Search functionality works
  - [ ] Filters update results
  - [ ] Pagination works

- [ ] **Tour Detail Page:**
  - [ ] Hero section loads
  - [ ] All tabs load correctly
  - [ ] FAQs accordion works
  - [ ] Reviews pagination works
  - [ ] Extras display correctly

- [ ] **Booking System:**
  - [ ] Form validation works
  - [ ] Date picker prevents past dates
  - [ ] Guest count respects min/max
  - [ ] Extras can be selected
  - [ ] Price calculation updates
  - [ ] Confirmation shows after submit
  - [ ] Booking saved in database

- [ ] **Cross-Browser Testing:**
  - [ ] Chrome
  - [ ] Firefox
  - [ ] Safari
  - [ ] Edge

- [ ] **Mobile Responsiveness:**
  - [ ] All partials render on mobile
  - [ ] Forms are usable on mobile
  - [ ] HTMX works on mobile

- [ ] **Performance:**
  - [ ] Page loads in < 2 seconds
  - [ ] Partial loads in < 500ms
  - [ ] No console errors
  - [ ] Cache working properly

---

## 🚀 Deployment Considerations

### Production Configuration:

1. **Update CORS for Production Domain:**
```php
'allowed_origins' => [
    'https://jahongirtravel.uz',
    'https://www.jahongirtravel.uz',
],
```

2. **Update Base URLs in Frontend:**
```javascript
// Change from:
const baseUrl = 'http://localhost/ssst3/partials/tours';

// To:
const baseUrl = 'https://jahongirtravel.uz/partials/tours';
```

3. **Enable Production Caching:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

4. **Set Up SSL:**
- Ensure SSL certificates installed
- Force HTTPS in Laravel config

5. **Monitor Performance:**
- Enable Laravel Telescope (optional)
- Monitor cache hit rates
- Track slow queries

---

## 📊 Next Steps After Completion

After implementing all phases:

1. **Phase 7: Beds24 Integration**
   - Connect to Beds24 API
   - Sync availability
   - Check real-time pricing

2. **Phase 8: Payment Gateway**
   - Integrate Stripe/PayPal
   - Process payments
   - Send receipts

3. **Phase 9: Email Notifications**
   - Booking confirmation emails
   - Admin notification emails
   - Reminder emails

4. **Phase 10: Advanced Features**
   - User accounts
   - Booking history
   - Wishlists
   - Multi-language support

---

## 📝 Summary

This implementation plan provides a complete partials-based architecture:

- ✅ **Backend:** Laravel serves HTML partials (not JSON)
- ✅ **Frontend:** Static HTML + HTMX for dynamic loading
- ✅ **Separation:** Frontend and backend remain separate
- ✅ **Performance:** Aggressive caching at partial level
- ✅ **SEO:** Server-rendered HTML = perfect for search engines
- ✅ **UX:** Smooth, no-page-reload experience with HTMX
- ✅ **Simplicity:** No complex API, no authentication, no CORS headaches

**Estimated Total Time:** 15-21 hours of development

Ready to start implementation? Let's go! 🚀
