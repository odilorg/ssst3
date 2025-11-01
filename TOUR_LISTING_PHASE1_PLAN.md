# 🚀 Tour Listing - Phase 1 Detailed Implementation Plan

**Date:** November 1, 2025
**Selected Options:**
- ✅ 12 tours per page
- ✅ Load More Button
- ✅ Sidebar filters (desktop) / Top filters (mobile)

**Timeline:** 1.5 - 2 hours
**Difficulty:** Medium

---

## 📋 Phase 1 Overview

**Goal:** Update Laravel backend to support paginated tour listing with "Load More" functionality

**What We'll Build:**
1. Enhanced `TourController::list()` with pagination support
2. Production-ready `list.blade.php` with beautiful tour cards
3. "Load More" button that appends new tours
4. Testing to ensure everything works

**What We WON'T Build (saved for Phase 2):**
- Frontend HTML pages (tours.html)
- Filter UI (search, duration, sort)
- CSS styling (we'll use your existing tour-card styles)

---

## 🎯 Success Criteria

After Phase 1, you should be able to:
- ✅ Visit `http://127.0.0.1:8000/partials/tours` and see 12 tour cards
- ✅ See a "Load More" button at the bottom
- ✅ Click "Load More" and see tours 13-24 appended
- ✅ Click again and see tours 25-36 appended
- ✅ Button disappears when no more tours exist
- ✅ All tours display with correct data (image, title, price, rating, etc.)

---

## 📁 Files We'll Modify/Create

### Files to Modify (2 files)
```
✏️ app/Http/Controllers/Partials/TourController.php
✏️ resources/views/partials/tours/list.blade.php
```

### Files to Create (1 file - optional)
```
🆕 database/seeders/TourSeeder.php (if you don't have sample tours)
```

### Files to Check (verify these exist)
```
✅ app/Models/Tour.php
✅ app/Models/City.php
✅ routes/web.php (already has /partials/tours route)
```

---

## 🔧 Step-by-Step Implementation

### **STEP 1: Check Tour Model Fields** (5 minutes)

Before we start, let's verify your Tour model has all required fields.

**Action: Check the Tour model**

Open: `app/Models/Tour.php`

**Required fields for tour cards:**
```php
// Required fields in tours table:
- id
- slug (unique)
- title
- short_description
- description (full description)
- price_per_person (decimal)
- duration_days (integer)
- duration_text (e.g., "5 days / 4 nights")
- featured_image (path or URL)
- rating (decimal, e.g., 4.5)
- review_count (integer)
- is_active (boolean)
- city_id (foreign key, nullable)
- created_at
- updated_at

// Optional but recommended:
- badge (nullable, e.g., "Most Popular")
- sort_order (integer, for manual ordering)
```

**Action: Verify Tour model relationships**

The Tour model should have:
```php
public function city()
{
    return $this->belongsTo(City::class);
}

public function reviews()
{
    return $this->hasMany(Review::class);
}

// Optional: accessor for full image URL
public function getFeaturedImageUrlAttribute()
{
    if (!$this->featured_image) {
        return asset('images/default-tour.webp');
    }

    // If stored as full URL
    if (str_starts_with($this->featured_image, 'http')) {
        return $this->featured_image;
    }

    // If stored as path
    return asset('storage/' . $this->featured_image);
}
```

**✅ Checkpoint:** Make sure you have at least 15-20 sample tours in your database for testing.

**If you don't have tours, run:**
```bash
php artisan db:seed --class=TourSeeder
```

---

### **STEP 2: Update TourController** (15 minutes)

**File:** `app/Http/Controllers/Partials/TourController.php`

**Current code (lines 16-26):**
```php
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
```

**NEW code (replace the entire method):**
```php
/**
 * Tour list partial
 * Returns: Grid of tour cards with pagination support
 *
 * Query params:
 * - per_page: Number of tours per page (default: 12)
 * - page: Current page number (default: 1)
 * - append: If true, returns only cards without wrapper (for Load More)
 */
public function list(Request $request)
{
    // Get pagination parameters
    $perPage = $request->get('per_page', 12);
    $page = $request->get('page', 1);
    $isAppend = $request->boolean('append', false);

    // Validate per_page (prevent abuse)
    $perPage = min(max($perPage, 6), 50); // Between 6 and 50

    // Cache key includes page and per_page
    $cacheKey = "tours.list.page.{$page}.per_page.{$perPage}";

    $tours = Cache::remember($cacheKey, 3600, function () use ($perPage) {
        return Tour::with(['city'])
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')  // Manual order first
            ->orderBy('created_at', 'desc') // Then by newest
            ->paginate($perPage);
    });

    return view('partials.tours.list', compact('tours', 'isAppend'));
}
```

**What changed:**
1. ✅ Added pagination (12 tours per page)
2. ✅ Added `isAppend` flag for "Load More" functionality
3. ✅ Changed from `get()` to `paginate()` for pagination
4. ✅ Added cache key with page number
5. ✅ Added validation for `per_page` (prevents someone requesting 10000 tours)
6. ✅ Added `sort_order` for manual ordering

**✅ Checkpoint:** Save the file.

---

### **STEP 3: Create Production Tour Card Blade** (30 minutes)

**File:** `resources/views/partials/tours/list.blade.php`

**Current code:** Test version with colored boxes

**NEW code:** Replace entire file with this:

```blade
{{--
    Tour List Partial - Production Version

    This partial can be loaded in two modes:
    1. Initial load (isAppend = false): Returns wrapper + cards + Load More button
    2. Append load (isAppend = true): Returns only new cards + updated Load More button

    Usage:
    - Initial: GET /partials/tours?per_page=12
    - Append: GET /partials/tours?page=2&per_page=12&append=true
--}}

@if (!$isAppend)
{{-- INITIAL LOAD: Include wrapper --}}
<div class="tours__grid">
@endif

    {{-- TOUR CARDS (Always rendered) --}}
    @forelse ($tours as $tour)
        <article class="tour-card" data-tour-id="{{ $tour->id }}">

            {{-- Tour Image --}}
            <div class="tour-card__media">
                <img
                    src="{{ $tour->featured_image_url ?? asset('images/default-tour.webp') }}"
                    alt="{{ $tour->title }}"
                    width="400"
                    height="300"
                    loading="lazy"
                    decoding="async"
                >

                {{-- Badge (if exists) --}}
                @if ($tour->badge)
                    <span class="tour-card__badge tour-card__badge--featured">
                        {{ $tour->badge }}
                    </span>
                @endif
            </div>

            {{-- Tour Content --}}
            <div class="tour-card__content">

                {{-- Tags (City) --}}
                <div class="tour-card__tags">
                    @if ($tour->city)
                        <span class="tag">{{ $tour->city->name }}</span>
                    @endif
                </div>

                {{-- Title --}}
                <h3 class="tour-card__title">
                    <a href="/tour-details.html?slug={{ $tour->slug }}">
                        {{ $tour->title }}
                    </a>
                </h3>

                {{-- Meta (Duration + Rating) --}}
                <div class="tour-card__meta">

                    {{-- Duration --}}
                    <div class="tour-card__duration">
                        <i class="far fa-clock" aria-hidden="true"></i>
                        <span>{{ $tour->duration_text ?? $tour->duration_days . ' days' }}</span>
                    </div>

                    {{-- Rating --}}
                    @if ($tour->rating > 0)
                        <div class="tour-card__rating">
                            <div class="stars" aria-label="Rated {{ number_format($tour->rating, 1) }} out of 5 stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($tour->rating))
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                    @elseif ($i - 0.5 <= $tour->rating)
                                        <i class="fas fa-star-half-alt" aria-hidden="true"></i>
                                    @else
                                        <i class="far fa-star" aria-hidden="true"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="tour-card__reviews">({{ $tour->review_count ?? 0 }} reviews)</span>
                        </div>
                    @endif
                </div>

                {{-- Footer (Price + CTA) --}}
                <div class="tour-card__footer">
                    <div class="tour-card__price">
                        <span class="tour-card__price-label">From</span>
                        <span class="tour-card__price-amount">${{ number_format($tour->price_per_person, 0) }}</span>
                        <span class="tour-card__price-unit">per person</span>
                    </div>
                    <a href="/tour-details.html?slug={{ $tour->slug }}" class="btn btn--primary">
                        View Details
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

            </div>
        </article>
    @empty
        {{-- No tours found --}}
        <div class="no-results">
            <div class="no-results__icon">
                <i class="fas fa-search" aria-hidden="true"></i>
            </div>
            <h3 class="no-results__title">No tours found</h3>
            <p class="no-results__message">
                We couldn't find any tours matching your criteria. Please try adjusting your filters or search terms.
            </p>
        </div>
    @endforelse

@if (!$isAppend)
{{-- INITIAL LOAD: Close wrapper --}}
</div>
@endif

{{-- LOAD MORE BUTTON (Show if more pages exist) --}}
@if ($tours->hasMorePages())
    <div class="tours__load-more" id="load-more-container">
        <button
            type="button"
            hx-get="{{ url('/partials/tours') }}?page={{ $tours->currentPage() + 1 }}&per_page={{ $tours->perPage() }}&append=true"
            hx-target=".tours__grid"
            hx-swap="beforeend"
            hx-select="article.tour-card"
            hx-indicator="#loading-spinner"
            class="btn btn--secondary btn--lg"
            aria-label="Load more tours">

            <span class="btn__text">Load More Tours</span>

            <span id="loading-spinner" class="htmx-indicator">
                <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
            </span>
        </button>

        <p class="tours__pagination-info">
            Showing {{ $tours->firstItem() }}-{{ $tours->lastItem() }} of {{ $tours->total() }} tours
        </p>
    </div>
@else
    {{-- All tours loaded - show end message --}}
    @if ($tours->count() > 0 && $tours->currentPage() > 1)
        <div class="tours__end-message">
            <p>You've reached the end! All {{ $tours->total() }} tours displayed.</p>
        </div>
    @endif
@endif

{{-- Replace Load More button on subsequent loads --}}
@if ($isAppend && $tours->hasMorePages())
    <div class="tours__load-more" id="load-more-container" hx-swap-oob="true">
        <button
            type="button"
            hx-get="{{ url('/partials/tours') }}?page={{ $tours->currentPage() + 1 }}&per_page={{ $tours->perPage() }}&append=true"
            hx-target=".tours__grid"
            hx-swap="beforeend"
            hx-select="article.tour-card"
            hx-indicator="#loading-spinner"
            class="btn btn--secondary btn--lg"
            aria-label="Load more tours">

            <span class="btn__text">Load More Tours</span>

            <span id="loading-spinner" class="htmx-indicator">
                <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
            </span>
        </button>

        <p class="tours__pagination-info">
            Showing {{ $tours->firstItem() }}-{{ $tours->lastItem() }} of {{ $tours->total() }} tours
        </p>
    </div>
@elseif ($isAppend && !$tours->hasMorePages())
    <div class="tours__end-message" id="load-more-container" hx-swap-oob="true">
        <p>You've reached the end! All {{ $tours->total() }} tours displayed.</p>
    </div>
@endif
```

**What this blade does:**

1. **Conditional wrapper:**
   - Initial load: Wraps cards in `<div class="tours__grid">`
   - Append load: Only returns cards (no wrapper)

2. **Tour cards:**
   - Uses your exact HTML structure from `index.html`
   - Image with lazy loading
   - Badge if exists
   - City tag
   - Title with link to tour details
   - Duration and rating
   - Price and CTA button

3. **Load More button:**
   - Shows if more pages exist
   - Uses HTMX to fetch next page
   - Shows loading spinner while fetching
   - Displays pagination info (e.g., "Showing 1-12 of 48 tours")

4. **Out-of-band swap (hx-swap-oob):**
   - When appending, replaces the old "Load More" button with updated one
   - This updates the page number in the button's `hx-get` URL

5. **Empty state:**
   - Shows friendly message if no tours found

**✅ Checkpoint:** Save the file.

---

### **STEP 4: Test the Backend** (15 minutes)

Now let's test that the backend works correctly.

**Test 1: Initial Load**

Open your browser and visit:
```
http://127.0.0.1:8000/partials/tours
```

**Expected result:**
- ✅ See 12 tour cards displayed
- ✅ Each card shows: image, title, price, duration, rating
- ✅ "Load More Tours" button at the bottom
- ✅ Text says "Showing 1-12 of [total] tours"

**If you see errors:**
- Check Laravel logs: `storage/logs/laravel.log`
- Check browser console for errors
- Verify Tour model has required fields

---

**Test 2: Load More Button (Manual)**

We'll test the "Load More" functionality manually (without HTMX yet).

Visit:
```
http://127.0.0.1:8000/partials/tours?page=2&append=true
```

**Expected result:**
- ✅ See ONLY tour cards (no wrapper `<div class="tours__grid">`)
- ✅ See tours 13-24
- ✅ See updated "Load More" button with `page=3` in the URL

---

**Test 3: Last Page**

If you have exactly 36 tours, visit:
```
http://127.0.0.1:8000/partials/tours?page=3
```

**Expected result:**
- ✅ See tours 25-36
- ✅ NO "Load More" button
- ✅ See "You've reached the end!" message

---

**Test 4: Different Per Page**

Test with 6 tours per page:
```
http://127.0.0.1:8000/partials/tours?per_page=6
```

**Expected result:**
- ✅ See 6 tour cards
- ✅ "Load More" button present
- ✅ Text says "Showing 1-6 of [total] tours"

---

### **STEP 5: Add Simple Test Page** (10 minutes)

To test the "Load More" button with HTMX (before we build the full frontend), let's create a simple test page.

**Create file:** `public/test-tours.html`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Listing Test</title>

    <!-- Minimal CSS for testing -->
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #fafafa;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .tours__grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .tour-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .tour-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .tour-card__media {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }

        .tour-card__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tour-card__badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #f59e0b;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .tour-card__content {
            padding: 16px;
        }

        .tour-card__tags {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
        }

        .tag {
            background: #e5e7eb;
            color: #374151;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .tour-card__title {
            margin: 0 0 12px 0;
            font-size: 18px;
            line-height: 1.4;
        }

        .tour-card__title a {
            color: #1f2937;
            text-decoration: none;
        }

        .tour-card__title a:hover {
            color: #3b82f6;
        }

        .tour-card__meta {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
            font-size: 14px;
            color: #6b7280;
        }

        .tour-card__duration,
        .tour-card__rating {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .stars {
            color: #f59e0b;
        }

        .tour-card__footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .tour-card__price {
            display: flex;
            flex-direction: column;
        }

        .tour-card__price-label {
            font-size: 12px;
            color: #6b7280;
        }

        .tour-card__price-amount {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
        }

        .tour-card__price-unit {
            font-size: 12px;
            color: #6b7280;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn--primary {
            background: #3b82f6;
            color: white;
        }

        .btn--primary:hover {
            background: #2563eb;
        }

        .btn--secondary {
            background: #f3f4f6;
            color: #1f2937;
            border: 1px solid #d1d5db;
        }

        .btn--secondary:hover {
            background: #e5e7eb;
        }

        .btn--lg {
            padding: 14px 28px;
            font-size: 16px;
        }

        .tours__load-more {
            text-align: center;
            margin: 32px 0;
        }

        .tours__pagination-info {
            margin-top: 12px;
            font-size: 14px;
            color: #6b7280;
        }

        .tours__end-message {
            text-align: center;
            padding: 32px;
            color: #6b7280;
        }

        .htmx-indicator {
            display: none;
        }

        .htmx-request .htmx-indicator {
            display: inline;
        }

        .htmx-request .btn__text {
            opacity: 0.5;
        }

        .no-results {
            grid-column: 1 / -1;
            text-align: center;
            padding: 64px 32px;
        }

        .no-results__icon {
            font-size: 48px;
            color: #d1d5db;
            margin-bottom: 16px;
        }

        .no-results__title {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .no-results__message {
            color: #6b7280;
        }

        /* Loading skeleton */
        .loading {
            grid-column: 1 / -1;
            text-align: center;
            padding: 48px;
            font-size: 18px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <h1>🚀 Tour Listing - Phase 1 Test</h1>

    <p style="text-align: center; color: #6b7280; margin-bottom: 32px;">
        Testing HTMX "Load More" functionality with 12 tours per page
    </p>

    <!-- Tour Grid Container -->
    <div id="tour-container"
         hx-get="http://127.0.0.1:8000/partials/tours?per_page=12"
         hx-trigger="load"
         hx-swap="innerHTML">
        <div class="loading">
            Loading tours...
        </div>
    </div>

    <!-- HTMX Library -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>

    <!-- Font Awesome (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Debug Script -->
    <script>
        console.log('[Test Page] Loaded');

        document.body.addEventListener('htmx:beforeRequest', function(evt) {
            console.log('[HTMX] Loading:', evt.detail.pathInfo.requestPath);
        });

        document.body.addEventListener('htmx:afterSwap', function(evt) {
            console.log('[HTMX] Loaded successfully');
        });

        document.body.addEventListener('htmx:responseError', function(evt) {
            console.error('[HTMX] Error:', evt.detail.xhr.status);
        });
    </script>
</body>
</html>
```

**Test the page:**

1. Open browser: `http://127.0.0.1:8000/test-tours.html`
2. You should see 12 tours load automatically
3. Click "Load More Tours" button
4. Watch tours 13-24 appear
5. Click again for tours 25-36
6. Button should disappear when all tours loaded

**✅ Checkpoint:** If this works, Phase 1 backend is complete! 🎉

---

### **STEP 6: Add Database Indexes (Optional - 5 minutes)**

For better performance with large datasets, add indexes to the tours table.

**Create migration:**
```bash
php artisan make:migration add_indexes_to_tours_table
```

**Edit migration file:**
```php
public function up()
{
    Schema::table('tours', function (Blueprint $table) {
        // Index for listing queries
        $table->index(['is_active', 'sort_order']);
        $table->index(['is_active', 'created_at']);

        // Index for search (if you add search later)
        $table->index('slug');
    });
}

public function down()
{
    Schema::table('tours', function (Blueprint $table) {
        $table->dropIndex(['is_active', 'sort_order']);
        $table->dropIndex(['is_active', 'created_at']);
        $table->dropIndex(['slug']);
    });
}
```

**Run migration:**
```bash
php artisan migrate
```

---

## ✅ Phase 1 Completion Checklist

Before moving to Phase 2, verify:

**Backend:**
- [ ] TourController::list() updated with pagination
- [ ] Pagination works (12 tours per page)
- [ ] Cache is working (check `storage/framework/cache`)
- [ ] list.blade.php returns beautiful tour cards
- [ ] "Load More" button appears when needed
- [ ] Button disappears on last page

**Testing:**
- [ ] `/partials/tours` shows 12 tours
- [ ] `/partials/tours?page=2&append=true` shows next 12
- [ ] test-tours.html works with HTMX
- [ ] Clicking "Load More" appends new tours
- [ ] No JavaScript errors in console
- [ ] All tour data displays correctly (image, title, price, etc.)

**Performance:**
- [ ] Page loads in < 2 seconds
- [ ] No N+1 queries (check with Laravel Debugbar if installed)
- [ ] Images lazy load properly
- [ ] Database indexes added (optional but recommended)

---

## 🐛 Troubleshooting

### Problem: No tours showing
**Solution:** Run seeder
```bash
php artisan db:seed --class=TourSeeder
```

### Problem: Images not loading
**Solution:** Check `featured_image_url` accessor in Tour model, or use default image

### Problem: "Load More" button doesn't work
**Solution:**
1. Check browser console for errors
2. Verify HTMX is loaded
3. Check that the URL in `hx-get` is correct

### Problem: Cache not updating
**Solution:** Clear cache
```bash
php artisan cache:clear
```

### Problem: 500 error
**Solution:** Check logs
```bash
tail -f storage/logs/laravel.log
```

---

## 📊 What We've Achieved

After Phase 1, you'll have:

✅ **Working pagination backend**
- 12 tours per page
- Clean, efficient queries
- Proper caching

✅ **Production-ready tour cards**
- Beautiful HTML matching your design
- All tour data displayed correctly
- Responsive image loading

✅ **"Load More" functionality**
- HTMX-powered, no page refresh
- Appends new tours smoothly
- Updates button with next page URL
- Disappears when all tours loaded

✅ **Performance optimized**
- Cached queries (1 hour)
- Lazy loading images
- Database indexes
- Minimal HTTP requests

---

## 🚀 Next: Phase 2 Preview

After Phase 1 is complete and tested, Phase 2 will include:

1. **Create tours.html** (full page with header/footer)
2. **Add filter sidebar** (search, duration, sort)
3. **Update SearchController** for filters
4. **Add filter UI components**
5. **Copy to Laravel public folder**
6. **Test full integration**

**Estimated time:** 1.5 hours

---

## 📝 Summary

**Phase 1 Duration:** 1.5 - 2 hours
**Files Modified:** 2 (TourController.php, list.blade.php)
**Files Created:** 1 (test-tours.html)
**Lines of Code:** ~200

**Ready to start Phase 1?** 🚀

Just say "start Phase 1" and I'll guide you through each step with the exact code to write!
