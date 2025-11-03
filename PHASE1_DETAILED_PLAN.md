# PHASE 1: SETUP & CONFIGURATION - DETAILED PLAN
**Partials Architecture Implementation**

**Estimated Time:** 1-2 hours
**Difficulty:** Easy
**Prerequisites:** XAMPP running, Laravel backend working, static frontend exists

---

## 📋 Phase 1 Overview

This phase sets up the foundation for the partials architecture:
1. Install HTMX in the frontend
2. Configure Laravel CORS middleware
3. Create partial routes structure
4. Create base TourController with caching
5. Create BookingController skeleton
6. Create SearchController skeleton
7. Test basic setup

**Goal:** By the end of Phase 1, you should be able to fetch a simple HTML partial from Laravel backend to the static frontend.

---

## 🎯 Tasks Breakdown

### **Task 1.1: Create New Git Branches** ✅

**Why:** Keep work separate and organized for both projects

**Backend Branch:**
```bash
cd D:/xampp82/htdocs/ssst3
git status                                    # Verify clean state
git checkout -b feature/partials-backend      # Create new branch
git branch --show-current                     # Confirm branch
```

**Frontend Branch:**
```bash
cd D:/xampp82/htdocs/jahongir-custom-website
git status                                    # Verify clean state
git checkout -b feature/partials-frontend     # Create new branch
git branch --show-current                     # Confirm branch
```

**Expected Output:**
```
feature/partials-backend      (backend)
feature/partials-frontend     (frontend)
```

**Verification:**
- [ ] Backend on `feature/partials-backend` branch
- [ ] Frontend on `feature/partials-frontend` branch
- [ ] Both working directories clean

---

### **Task 1.2: Install HTMX in Frontend** ⏱️ 10 minutes

**Why:** HTMX allows us to load server-rendered HTML partials without writing JavaScript

**Option A: Download HTMX (Recommended for Production)**

1. **Download HTMX:**
   - Visit: https://unpkg.com/htmx.org@1.9.10/dist/htmx.min.js
   - Save as: `D:/xampp82/htdocs/jahongir-custom-website/assets/js/htmx.min.js`

2. **Verify file exists:**
```bash
cd D:/xampp82/htdocs/jahongir-custom-website
ls -lh assets/js/htmx.min.js
```

**Expected Output:**
```
-rw-r--r-- 1 Admin 197121 44K Jan 15 10:30 assets/js/htmx.min.js
```

**Option B: Use CDN (Faster for Development)**

No download needed - just reference CDN in HTML (we'll do this in later phases).

**Decision Point:** Which option do you prefer?
- [ ] Option A: Download and host locally
- [ ] Option B: Use CDN

**Verification:**
- [ ] HTMX file exists in frontend assets folder OR
- [ ] Confirmed using CDN approach

---

### **Task 1.3: Check Laravel CORS Package** ⏱️ 5 minutes

**Why:** CORS allows frontend (different domain/port) to fetch from backend

**Check if CORS middleware exists:**
```bash
cd D:/xampp82/htdocs/ssst3
php artisan route:list | grep -i cors
```

**Check if cors.php config exists:**
```bash
ls -l config/cors.php
```

**Expected Output:**
```
-rw-r--r-- 1 Admin 197121 1.2K config/cors.php
```

**If file doesn't exist:**
```bash
# Install fruitcake/laravel-cors (should be included in Laravel 12 by default)
composer require fruitcake/laravel-cors
php artisan vendor:publish --tag="cors"
```

**Verification:**
- [ ] `config/cors.php` file exists
- [ ] No errors when running commands

---

### **Task 1.4: Configure Laravel CORS** ⏱️ 10 minutes

**Why:** Allow localhost frontend to fetch from localhost backend during development

**File to Edit:** `D:/xampp82/htdocs/ssst3/config/cors.php`

**Changes:**

1. **Add `/partials/*` to allowed paths:**

**Before:**
```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
```

**After:**
```php
'paths' => [
    'api/*',
    'partials/*',  // ← ADD THIS
    'sanctum/csrf-cookie'
],
```

2. **Add localhost origins:**

**Before:**
```php
'allowed_origins' => ['*'],
```

**After:**
```php
'allowed_origins' => [
    'http://localhost',
    'http://localhost:3000',
    'http://127.0.0.1',
    'http://localhost:8080',
    // Add any other ports your frontend might use
],
```

3. **Enable credentials:**

**Before:**
```php
'supports_credentials' => false,
```

**After:**
```php
'supports_credentials' => true,
```

**Full Updated File Preview:**
```php
<?php

return [
    'paths' => [
        'api/*',
        'partials/*',
        'sanctum/csrf-cookie'
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost',
        'http://localhost:3000',
        'http://127.0.0.1',
        'http://localhost:8080',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
```

**Clear Laravel Cache:**
```bash
cd D:/xampp82/htdocs/ssst3
php artisan config:cache
php artisan config:clear
```

**Expected Output:**
```
Configuration cache cleared successfully.
Configuration cached successfully.
```

**Verification:**
- [ ] `config/cors.php` updated with correct values
- [ ] Config cache cleared successfully
- [ ] No errors displayed

---

### **Task 1.5: Create Partial Routes** ⏱️ 15 minutes

**Why:** Define all the endpoints that will serve HTML partials

**File to Edit:** `D:/xampp82/htdocs/ssst3/routes/web.php`

**Action:** Add these routes at the END of the file (after existing routes):

```php
<?php

use App\Http\Controllers\Partials\TourController;
use App\Http\Controllers\Partials\BookingController;
use App\Http\Controllers\Partials\SearchController;
use Illuminate\Support\Facades\Route;

// ... existing routes stay here ...

// ============================================
// PUBLIC PARTIAL ROUTES
// ============================================

Route::prefix('partials')->name('partials.')->group(function () {

    // -------- TOUR LIST --------
    Route::get('/tours', [TourController::class, 'list'])
        ->name('tours.list');

    // -------- TOUR SEARCH/FILTER --------
    Route::get('/tours/search', [SearchController::class, 'search'])
        ->name('tours.search');

    // -------- TOUR DETAIL SECTIONS --------
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

    // -------- BOOKING --------
    Route::get('/bookings/form/{tour_slug}', [BookingController::class, 'form'])
        ->name('bookings.form');

    Route::post('/bookings', [BookingController::class, 'store'])
        ->name('bookings.store');
});
```

**Verify Routes Created:**
```bash
cd D:/xampp82/htdocs/ssst3
php artisan route:list --name=partials
```

**Expected Output (should show 12 routes):**
```
GET|HEAD  partials/tours ..................... partials.tours.list
GET|HEAD  partials/tours/search .............. partials.tours.search
GET|HEAD  partials/tours/{slug}/hero ......... partials.tours.hero
GET|HEAD  partials/tours/{slug}/overview ..... partials.tours.overview
GET|HEAD  partials/tours/{slug}/highlights ... partials.tours.highlights
GET|HEAD  partials/tours/{slug}/itinerary .... partials.tours.itinerary
GET|HEAD  partials/tours/{slug}/included-excluded ... partials.tours.included-excluded
GET|HEAD  partials/tours/{slug}/faqs ......... partials.tours.faqs
GET|HEAD  partials/tours/{slug}/extras ....... partials.tours.extras
GET|HEAD  partials/tours/{slug}/reviews ...... partials.tours.reviews
GET|HEAD  partials/bookings/form/{tour_slug}  partials.bookings.form
POST      partials/bookings .................. partials.bookings.store
```

**Verification:**
- [ ] Routes added to `web.php`
- [ ] `php artisan route:list` shows 12 new partial routes
- [ ] No syntax errors

---

### **Task 1.6: Create Controllers Directory** ⏱️ 2 minutes

**Why:** Organize partial controllers in a separate folder

**Create Directory:**
```bash
cd D:/xampp82/htdocs/ssst3
mkdir -p app/Http/Controllers/Partials
```

**Verify Directory Created:**
```bash
ls -la app/Http/Controllers/Partials
```

**Expected Output:**
```
drwxr-xr-x 1 Admin 197121 0 Jan 15 11:00 .
drwxr-xr-x 1 Admin 197121 0 Jan 15 11:00 ..
```

**Verification:**
- [ ] Directory `app/Http/Controllers/Partials/` exists

---

### **Task 1.7: Create TourController** ⏱️ 20 minutes

**Why:** Main controller for serving tour partials with caching

**File to Create:** `D:/xampp82/htdocs/ssst3/app/Http/Controllers/Partials/TourController.php`

**Full Code:**
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
     * Returns: Grid of tour cards
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
     * Returns: Tour hero with title, image, price, CTA
     */
    public function hero(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.hero', compact('tour'));
    }

    /**
     * Overview section
     * Returns: Description, quick info grid
     */
    public function overview(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.overview', compact('tour'));
    }

    /**
     * Highlights section
     * Returns: Bulleted list of tour highlights
     */
    public function highlights(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.highlights', compact('tour'));
    }

    /**
     * Itinerary section
     * Returns: Day-by-day itinerary (if multi-day tour)
     */
    public function itinerary(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.itinerary', compact('tour'));
    }

    /**
     * Included/Excluded section
     * Returns: What's included and what's not included
     */
    public function includedExcluded(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.included-excluded', compact('tour'));
    }

    /**
     * FAQs section
     * Returns: Accordion of frequently asked questions
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
     * Returns: Grid of optional add-on services
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
     * Returns: Paginated customer reviews
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
     * Caches tour for 1 hour to reduce database queries
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

**Verify File Created:**
```bash
cd D:/xampp82/htdocs/ssst3
ls -l app/Http/Controllers/Partials/TourController.php
```

**Expected Output:**
```
-rw-r--r-- 1 Admin 197121 4.2K Jan 15 11:15 TourController.php
```

**Check for Syntax Errors:**
```bash
php artisan about
```

**Expected:** No errors, application loads successfully.

**Verification:**
- [ ] TourController.php created
- [ ] No syntax errors
- [ ] File size around 4KB

---

### **Task 1.8: Create BookingController Skeleton** ⏱️ 10 minutes

**Why:** Handle booking form display and submission

**File to Create:** `D:/xampp82/htdocs/ssst3/app/Http/Controllers/Partials/BookingController.php`

**Full Code:**
```php
<?php

namespace App\Http\Controllers\Partials;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Show booking form partial
     * Returns: Booking form HTML with tour details
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
     * Returns: Confirmation HTML or error HTML
     *
     * Will be implemented in Phase 4
     */
    public function store(Request $request)
    {
        // TODO: Implement in Phase 4
        return response()->json([
            'message' => 'Booking controller - store method - Coming in Phase 4'
        ]);
    }
}
```

**Verify File Created:**
```bash
ls -l app/Http/Controllers/Partials/BookingController.php
```

**Verification:**
- [ ] BookingController.php created
- [ ] No syntax errors

---

### **Task 1.9: Create SearchController Skeleton** ⏱️ 10 minutes

**Why:** Handle tour search and filtering

**File to Create:** `D:/xampp82/htdocs/ssst3/app/Http/Controllers/Partials/SearchController.php`

**Full Code:**
```php
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
```

**Verify File Created:**
```bash
ls -l app/Http/Controllers/Partials/SearchController.php
```

**Verification:**
- [ ] SearchController.php created
- [ ] No syntax errors

---

### **Task 1.10: Create Views Directory Structure** ⏱️ 3 minutes

**Why:** Organize Blade templates for partials

**Create Directories:**
```bash
cd D:/xampp82/htdocs/ssst3
mkdir -p resources/views/partials/tours/show
mkdir -p resources/views/partials/bookings
mkdir -p resources/views/partials/search
```

**Verify Directories Created:**
```bash
ls -la resources/views/partials/
```

**Expected Output:**
```
drwxr-xr-x 1 Admin 197121 0 Jan 15 11:20 tours
drwxr-xr-x 1 Admin 197121 0 Jan 15 11:20 bookings
drwxr-xr-x 1 Admin 197121 0 Jan 15 11:20 search
```

**Verification:**
- [ ] `resources/views/partials/tours/` exists
- [ ] `resources/views/partials/tours/show/` exists
- [ ] `resources/views/partials/bookings/` exists
- [ ] `resources/views/partials/search/` exists

---

### **Task 1.11: Create Test Partial View** ⏱️ 5 minutes

**Why:** Create a simple test partial to verify everything works

**File to Create:** `D:/xampp82/htdocs/ssst3/resources/views/partials/tours/list.blade.php`

**Simple Test Content:**
```blade
{{-- Tour List Partial - Test Version --}}
<div class="tours-test" style="padding: 20px; background: #f0f0f0; border: 2px solid #4CAF50;">
    <h2 style="color: #4CAF50;">✅ Partials Working!</h2>
    <p><strong>Backend:</strong> Laravel serving HTML partials</p>
    <p><strong>Tours Found:</strong> {{ $tours->count() }}</p>

    @if ($tours->isNotEmpty())
        <ul style="list-style: none; padding: 0;">
            @foreach ($tours->take(3) as $tour)
                <li style="background: white; margin: 10px 0; padding: 15px; border-radius: 5px;">
                    <strong>{{ $tour->title }}</strong>
                    <br>
                    <small>Price: ${{ number_format($tour->price_per_person, 0) }} | Duration: {{ $tour->duration_text }}</small>
                </li>
            @endforeach
        </ul>
    @else
        <p>No tours available.</p>
    @endif

    <p style="margin-top: 20px; font-size: 12px; color: #666;">
        This is a test partial. Full design coming in Phase 2.
    </p>
</div>
```

**Verify File Created:**
```bash
ls -l resources/views/partials/tours/list.blade.php
```

**Verification:**
- [ ] `list.blade.php` created in correct location
- [ ] File contains test HTML

---

### **Task 1.12: Test Backend Partial Endpoint** ⏱️ 5 minutes

**Why:** Verify the backend can serve HTML partials

**Method 1: Browser Test**

1. **Start XAMPP** (Apache & MySQL)
2. **Open Browser**
3. **Navigate to:** `http://localhost/ssst3/partials/tours`

**Expected Result:**
- Green box appears with "✅ Partials Working!"
- Shows count of tours
- Shows 3 tour cards

**Method 2: Command Line Test**
```bash
curl -i http://localhost/ssst3/partials/tours
```

**Expected Output:**
```
HTTP/1.1 200 OK
Content-Type: text/html; charset=UTF-8

<div class="tours-test" style="...">
  <h2>✅ Partials Working!</h2>
  ...
</div>
```

**If You Get 404 Error:**
- Check Apache is running
- Verify route exists: `php artisan route:list --name=partials.tours.list`
- Clear route cache: `php artisan route:clear`

**If You Get 500 Error:**
- Check Laravel logs: `tail -50 storage/logs/laravel.log`
- Check for syntax errors in controller
- Verify Tour model exists

**Verification:**
- [ ] Browser shows green success box
- [ ] Tour count is displayed
- [ ] At least 1 tour card shown (from seeded data)
- [ ] No errors in browser console

---

### **Task 1.13: Create Simple Frontend Test Page** ⏱️ 10 minutes

**Why:** Test HTMX can fetch partials from backend

**File to Create:** `D:/xampp82/htdocs/jahongir-custom-website/test-partials.html`

**Full Code:**
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partials Test | Jahongir Travel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .loading {
            padding: 40px;
            text-align: center;
            color: #666;
            font-style: italic;
        }
        .success {
            color: #4CAF50;
            font-weight: bold;
        }
        .error {
            color: #f44336;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>🧪 Partials Architecture Test Page</h1>
    <p>This page tests if HTMX can load HTML partials from Laravel backend.</p>

    <!-- Test Section 1: Tour List -->
    <div class="test-section">
        <h2>Test 1: Tour List Partial</h2>
        <p>Loading tours from: <code>http://localhost/ssst3/partials/tours</code></p>

        <div id="tour-list-container"
             hx-get="http://localhost/ssst3/partials/tours"
             hx-trigger="load"
             hx-swap="innerHTML">

            <div class="loading">
                ⏳ Loading tours from backend...
            </div>
        </div>
    </div>

    <!-- Test Section 2: Manual Trigger -->
    <div class="test-section">
        <h2>Test 2: Manual Load Button</h2>
        <button hx-get="http://localhost/ssst3/partials/tours"
                hx-target="#manual-result"
                hx-swap="innerHTML"
                style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
            Click to Load Tours
        </button>

        <div id="manual-result" style="margin-top: 20px;">
            <p style="color: #666;">Click the button above to test manual loading.</p>
        </div>
    </div>

    <!-- Test Results -->
    <div class="test-section">
        <h2>✅ Test Checklist</h2>
        <p>After page loads, verify:</p>
        <ul>
            <li>✅ Green success box appears in Test 1</li>
            <li>✅ Tour count is displayed</li>
            <li>✅ Tour cards are shown</li>
            <li>✅ Manual button works in Test 2</li>
            <li>✅ No CORS errors in browser console (F12)</li>
        </ul>
    </div>

    <!-- HTMX Library -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>

    <!-- Debug Script -->
    <script>
        // Log HTMX events for debugging
        document.body.addEventListener('htmx:afterRequest', function(evt) {
            console.log('✅ HTMX Request Successful:', evt.detail);
        });

        document.body.addEventListener('htmx:responseError', function(evt) {
            console.error('❌ HTMX Request Failed:', evt.detail);
        });

        // Show CORS errors
        window.addEventListener('load', function() {
            console.log('🧪 Test page loaded. Check console for any errors.');
        });
    </script>
</body>
</html>
```

**Verify File Created:**
```bash
cd D:/xampp82/htdocs/jahongir-custom-website
ls -l test-partials.html
```

**Verification:**
- [ ] `test-partials.html` created in frontend root

---

### **Task 1.14: Test Frontend + Backend Integration** ⏱️ 10 minutes

**Why:** Verify complete setup works end-to-end

**Steps:**

1. **Ensure XAMPP Running:**
   - Apache: ✅ Running
   - MySQL: ✅ Running

2. **Open Test Page in Browser:**
   - URL: `http://localhost/jahongir-custom-website/test-partials.html`
   - OR: Open file directly from File Explorer

3. **Open Browser Console (F12):**
   - Look for any error messages

**Expected Results:**

✅ **SUCCESS - You Should See:**
- Green box with "✅ Partials Working!"
- Tour count displayed
- 3 tour cards showing
- Manual button loads tours when clicked
- Console shows: `✅ HTMX Request Successful`

❌ **FAILURE - Common Issues:**

**Issue 1: CORS Error**
```
Access to fetch at 'http://localhost/ssst3/partials/tours' from origin 'null' has been blocked by CORS policy
```
**Solution:**
- Verify Task 1.4 completed (CORS config)
- Run: `php artisan config:clear`
- Restart Apache

**Issue 2: 404 Not Found**
```
GET http://localhost/ssst3/partials/tours 404 (Not Found)
```
**Solution:**
- Verify routes created (Task 1.5)
- Run: `php artisan route:clear`
- Check `.htaccess` exists in `public/`

**Issue 3: 500 Internal Server Error**
```
GET http://localhost/ssst3/partials/tours 500 (Internal Server Error)
```
**Solution:**
- Check Laravel logs: `storage/logs/laravel.log`
- Verify Tour model exists
- Check database has tour data

**Issue 4: Nothing Loads**
- Check browser console for JavaScript errors
- Verify HTMX loaded: Type `htmx` in console, should return object
- Verify internet connection (if using CDN)

**Verification Checklist:**
- [ ] Page loads without errors
- [ ] Green success box appears
- [ ] Tour data displayed
- [ ] Manual button works
- [ ] No CORS errors in console
- [ ] Console shows successful HTMX requests

---

### **Task 1.15: Commit Phase 1 Changes** ⏱️ 5 minutes

**Why:** Save progress with clear commit messages

**Backend Commit:**
```bash
cd D:/xampp82/htdocs/ssst3
git status
git add config/cors.php
git add routes/web.php
git add app/Http/Controllers/Partials/
git add resources/views/partials/
git commit -m "$(cat <<'EOF'
feat: Phase 1 - Setup partials architecture backend

- Configure CORS for localhost development
- Add 12 partial routes (/partials/*)
- Create TourController with caching (9 methods)
- Create BookingController skeleton
- Create SearchController with basic filtering
- Create views directory structure
- Add test partial view for tours list

Phase 1 of partials implementation complete.
Backend ready to serve HTML partials to frontend.

🤖 Generated with [Claude Code](https://claude.com/claude-code)

Co-Authored-By: Claude <noreply@anthropic.com>
EOF
)"
```

**Frontend Commit:**
```bash
cd D:/xampp82/htdocs/jahongir-custom-website
git status
git add assets/js/htmx.min.js    # If downloaded locally
git add test-partials.html
git commit -m "$(cat <<'EOF'
feat: Phase 1 - Setup partials architecture frontend

- Add HTMX library for partial loading
- Create test page for backend integration
- Verify CORS and route configuration working

Phase 1 of partials implementation complete.
Frontend can fetch HTML partials from backend.

🤖 Generated with [Claude Code](https://claude.com/claude-code)

Co-Authored-By: Claude <noreply@anthropic.com>
EOF
)"
```

**Verify Commits:**
```bash
# Backend
cd D:/xampp82/htdocs/ssst3
git log --oneline -1

# Frontend
cd D:/xampp82/htdocs/jahongir-custom-website
git log --oneline -1
```

**Expected Output:**
```
abc1234 feat: Phase 1 - Setup partials architecture backend
def5678 feat: Phase 1 - Setup partials architecture frontend
```

**Verification:**
- [ ] Backend changes committed
- [ ] Frontend changes committed
- [ ] Commit messages clear and descriptive
- [ ] Working directories clean

---

## 📊 Phase 1 Completion Checklist

Before moving to Phase 2, verify ALL items below:

### Backend Checklist:
- [ ] On `feature/partials-backend` branch
- [ ] CORS configured for localhost
- [ ] 12 partial routes created and working
- [ ] TourController with 9 methods created
- [ ] BookingController skeleton created
- [ ] SearchController skeleton created
- [ ] Views directory structure created
- [ ] Test partial view created
- [ ] Backend test endpoint works in browser
- [ ] Changes committed to git

### Frontend Checklist:
- [ ] On `feature/partials-frontend` branch
- [ ] HTMX library installed/referenced
- [ ] Test page created
- [ ] Test page shows tours successfully
- [ ] No CORS errors in console
- [ ] Manual load button works
- [ ] Changes committed to git

### Integration Test:
- [ ] Can fetch `/partials/tours` from browser
- [ ] Can fetch `/partials/tours` from frontend HTML
- [ ] HTMX requests succeed (check console)
- [ ] Tour data displays correctly
- [ ] No 404/500 errors

---

## 🎯 Success Criteria

**Phase 1 is COMPLETE when:**

1. ✅ You can open `http://localhost/ssst3/partials/tours` and see a green success box
2. ✅ You can open the test HTML page and tours load automatically
3. ✅ Manual button loads tours on click
4. ✅ Browser console shows no CORS errors
5. ✅ All code committed to git on feature branches

---

## 🚀 What's Next?

Once Phase 1 is approved and complete:

**Phase 2: Tour List Page**
- Create real tour card design
- Implement search filters
- Add pagination
- Style with your existing CSS

**Estimated Time:** 3-4 hours

---

## 🐛 Troubleshooting Guide

### Problem: CORS errors persist

**Check:**
```bash
php artisan config:clear
php artisan config:cache
cat config/cors.php | grep partials
```

**Verify CORS middleware in kernel:**
```bash
grep -r "HandleCors" app/Http/Kernel.php
```

---

### Problem: Routes not found

**Check:**
```bash
php artisan route:clear
php artisan route:cache
php artisan route:list --name=partials
```

---

### Problem: Tour model not found

**Check:**
```bash
php artisan tinker
>>> App\Models\Tour::count()
>>> exit
```

If returns 0, run seeders again:
```bash
php artisan db:seed --class=TourSeeder
```

---

### Problem: View not found

**Check:**
```bash
ls -l resources/views/partials/tours/list.blade.php
php artisan view:clear
```

---

## 📋 Quick Reference Commands

```bash
# Clear all Laravel caches
php artisan optimize:clear

# Check application status
php artisan about

# List all partial routes
php artisan route:list --name=partials

# Check for syntax errors
php artisan about

# Tail Laravel logs
tail -f storage/logs/laravel.log

# Test route in terminal
curl -i http://localhost/ssst3/partials/tours
```

---

## ✅ Phase 1 Sign-Off

**Before proceeding to Phase 2, confirm:**

- [ ] I have completed all tasks above
- [ ] All verification checkboxes are checked
- [ ] Test page loads successfully
- [ ] No errors in browser console
- [ ] Changes committed to git
- [ ] Ready to proceed to Phase 2

**Estimated Completion Time:** 1-2 hours

**Ready to start? Let me know and I'll begin implementation!** 🚀
