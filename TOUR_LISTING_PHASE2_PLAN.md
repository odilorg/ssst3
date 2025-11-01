# 🚀 Tour Listing - Phase 2 Detailed Implementation Plan

**Date:** November 1, 2025
**Goal:** Create full-featured public tours page with filters and search
**Timeline:** 2 - 2.5 hours
**Difficulty:** Medium

---

## 📋 Phase 2 Overview

**What We're Building:**
A complete, production-ready tours listing page at `http://127.0.0.1:8000/tours.html` with:
- ✅ 12 tours per page (configurable)
- ✅ Filter sidebar (search, duration, sort)
- ✅ "Load More" button pagination
- ✅ Beautiful design matching your existing site
- ✅ Mobile-responsive layout
- ✅ Full HTMX integration

**What We Built in Phase 1:**
- ✅ Backend pagination working (`TourController::list()`)
- ✅ Production tour cards blade (`list.blade.php`)
- ✅ "Load More" button HTMX functionality
- ✅ Test page proving everything works

**What's New in Phase 2:**
1. Full HTML page with header/footer
2. Filter sidebar with search, duration filter, sort options
3. Enhanced `SearchController` for filters
4. Mobile-responsive filter layout
5. Integration with existing site navigation

---

## 🎯 Success Criteria

After Phase 2, you'll have:
- ✅ `/tours.html` page accessible from navigation
- ✅ Sidebar filters working (search by keyword, filter by duration, sort by price/rating)
- ✅ 12 tours displayed initially
- ✅ "Load More" button appending tours smoothly
- ✅ Filters updating tour list dynamically (no page refresh)
- ✅ Mobile-friendly (filters collapse to top bar)
- ✅ Consistent styling with your existing site

---

## 📁 Files We'll Create/Modify

### New Files (4 files):
```
🆕 D:\xampp82\htdocs\jahongir-custom-website\tours.html (design workspace)
🆕 D:\xampp82\htdocs\ssst3\public\tours.html (production copy)
```

### Files to Modify (1 file):
```
✏️ app/Http/Controllers/Partials/SearchController.php (enhance filters)
```

### Files Already Complete (no changes needed):
```
✅ app/Http/Controllers/Partials/TourController.php (Phase 1)
✅ app/Models/Tour.php (Phase 1)
✅ resources/views/partials/tours/list.blade.php (Phase 1)
✅ routes/web.php (already has /partials/tours/search route)
```

---

## 🎨 Design Reference

### Desktop Layout (1200px+)
```
┌─────────────────────────────────────────────────────────┐
│  Header / Navigation                                     │
├──────────────┬──────────────────────────────────────────┤
│              │  Page Title: "All Tours"                  │
│  FILTERS     │  Subtitle: "12 tours / 4 cities"         │
│  SIDEBAR     │                                           │
│              │  ┌────────┐ ┌────────┐ ┌────────┐       │
│  Search      │  │ Tour 1 │ │ Tour 2 │ │ Tour 3 │       │
│  [_______]   │  └────────┘ └────────┘ └────────┘       │
│              │                                           │
│  Duration    │  ┌────────┐ ┌────────┐ ┌────────┐       │
│  ○ All       │  │ Tour 4 │ │ Tour 5 │ │ Tour 6 │       │
│  ○ 1 Day     │  └────────┘ └────────┘ └────────┘       │
│  ○ 2-5 Days  │                                           │
│  ○ 6+ Days   │  ┌────────┐ ┌────────┐ ┌────────┐       │
│              │  │ Tour 7 │ │ Tour 8 │ │ Tour 9 │       │
│  Sort By     │  └────────┘ └────────┘ └────────┘       │
│  [Latest ▼]  │                                           │
│              │  ┌────────┐ ┌────────┐ ┌────────┐       │
│  [Apply]     │  │Tour 10 │ │Tour 11 │ │Tour 12 │       │
│              │  └────────┘ └────────┘ └────────┘       │
│              │                                           │
│              │  [Load More Tours]                        │
│              │  Showing 1-12 of 48 tours                │
├──────────────┴──────────────────────────────────────────┤
│  Footer                                                  │
└─────────────────────────────────────────────────────────┘

Width: Sidebar 300px | Main Content flex-1
Gap: 32px
```

### Mobile Layout (< 768px)
```
┌──────────────────────────┐
│  Header (Hamburger)      │
├──────────────────────────┤
│  All Tours               │
│  12 tours / 4 cities     │
│                          │
│  [🔍 Search] [⚙ Filters]│ ← Horizontal chips
│                          │
│  ┌──────────────────────┐│
│  │ Tour Card 1          ││
│  │ (Full Width)         ││
│  └──────────────────────┘│
│                          │
│  ┌──────────────────────┐│
│  │ Tour Card 2          ││
│  └──────────────────────┘│
│                          │
│  [Load More Tours]       │
├──────────────────────────┤
│  Footer                  │
└──────────────────────────┘
```

---

## 🔧 Step-by-Step Implementation

---

### **STEP 1: Enhance SearchController** (20 minutes)

We already have a basic `SearchController`, but let's enhance it with better filtering.

**File:** `app/Http/Controllers/Partials/SearchController.php`

**Current code:**
```php
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
```

**ENHANCED code (replace entire method):**
```php
/**
 * Search and filter tours
 * Returns: Filtered tour cards HTML with pagination
 *
 * Query params:
 * - q: Search keyword (searches title, description)
 * - duration: Duration filter (1, 2-5, 6+, or empty for all)
 * - sort: Sort order (latest, price_low, price_high, rating)
 * - per_page: Results per page (default: 12)
 * - page: Current page (default: 1)
 * - append: If true, returns only cards without wrapper
 */
public function search(Request $request)
{
    // Get parameters
    $keyword = $request->get('q');
    $duration = $request->get('duration');
    $sortBy = $request->get('sort', 'latest');
    $perPage = $request->get('per_page', 12);
    $isAppend = $request->boolean('append', false);

    // Validate per_page
    $perPage = min(max($perPage, 6), 50);

    // Build query
    $query = Tour::query()
        ->with(['city'])
        ->where('is_active', true);

    // Apply search filter
    if (!empty($keyword)) {
        $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('short_description', 'like', "%{$keyword}%")
              ->orWhere('long_description', 'like', "%{$keyword}%");
        });
    }

    // Apply duration filter
    if (!empty($duration)) {
        switch ($duration) {
            case '1':
                $query->where('duration_days', 1);
                break;
            case '2-5':
                $query->whereBetween('duration_days', [2, 5]);
                break;
            case '6+':
                $query->where('duration_days', '>=', 6);
                break;
        }
    }

    // Apply sorting
    switch ($sortBy) {
        case 'price_low':
            $query->orderBy('price_per_person', 'asc');
            break;
        case 'price_high':
            $query->orderBy('price_per_person', 'desc');
            break;
        case 'rating':
            $query->orderBy('rating', 'desc')
                  ->orderBy('review_count', 'desc');
            break;
        case 'popular':
            $query->orderBy('review_count', 'desc')
                  ->orderBy('rating', 'desc');
            break;
        default: // 'latest'
            $query->orderBy('created_at', 'desc');
    }

    // Execute query with pagination
    $tours = $query->paginate($perPage);

    return view('partials.tours.list', compact('tours', 'isAppend'));
}
```

**What changed:**
1. ✅ Added pagination support (was missing!)
2. ✅ Added `isAppend` support for "Load More"
3. ✅ Search now includes `long_description`
4. ✅ Added "popular" sort option (by review count)
5. ✅ Returns paginated results instead of all results
6. ✅ Added detailed documentation

**✅ Checkpoint:** Save the file.

---

### **STEP 2: Create tours.html in Template Folder** (40 minutes)

Now we'll create the full HTML page in your design workspace.

**File:** `D:\xampp82\htdocs\jahongir-custom-website\tours.html`

**Complete HTML:**
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse all tours in Uzbekistan - Silk Road adventures, cultural experiences, mountain treks, and more. Expert guides, flexible itineraries, best prices.">
    <title>All Tours - Explore Uzbekistan | Jahongir Travel</title>

    <!-- Canonical URL -->
    <link rel="canonical" href="https://jahongirtravel.com/tours">

    <!-- Preconnect to Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Skip to Main Content -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- =====================================================
         HEADER / NAVIGATION (Same as index.html)
         ===================================================== -->
    <header class="site-header" role="banner">
        <nav class="nav" aria-label="Main navigation">
            <div class="container">
                <a href="/" class="nav__logo">
                    <span class="nav__logo-text">Jahongir <strong>Travel</strong></span>
                </a>

                <ul class="nav__menu" id="navMenu">
                    <li><a href="/">Home</a></li>
                    <li><a href="/tours.html" class="active">Tours</a></li>
                    <li><a href="/destinations/">Destinations</a></li>
                    <li><a href="/about/">About Us</a></li>
                    <li><a href="/contact/">Contact</a></li>
                </ul>

                <a href="tel:+998991234567" class="btn btn--accent nav__cta">
                    <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6.62 10.79a15.09 15.09 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.24 11.72 11.72 0 003.67.59 1 1 0 011 1v3.54a1 1 0 01-1 1A18.5 18.5 0 013 5a1 1 0 011-1h3.55a1 1 0 011 1 11.72 11.72 0 00.59 3.67 1 1 0 01-.25 1.11z"/>
                    </svg>
                    +998 99 123 4567
                </a>

                <button class="nav__toggle" id="navToggle" aria-label="Toggle navigation menu" aria-expanded="false">
                    <span class="nav__toggle-icon"></span>
                </button>
            </div>
        </nav>
    </header>

    <!-- =====================================================
         PAGE HEADER
         ===================================================== -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-header__title">All Tours</h1>
            <p class="page-header__subtitle">
                Discover handcrafted journeys through the heart of the Silk Road
            </p>
        </div>
    </section>

    <!-- =====================================================
         TOURS CATALOG (Sidebar + Grid)
         ===================================================== -->
    <section class="tours-catalog" id="main-content">
        <div class="container">
            <div class="tours-catalog__layout">

                <!-- SIDEBAR FILTERS (Desktop) -->
                <aside class="tours-catalog__filters" id="filters-sidebar">
                    <div class="filters-header">
                        <h2 class="filters-header__title">Filter Tours</h2>
                        <button class="filters-header__reset" id="reset-filters">
                            <i class="fas fa-redo" aria-hidden="true"></i>
                            Reset All
                        </button>
                    </div>

                    <form id="tour-filters"
                          hx-get="http://127.0.0.1:8000/partials/tours/search"
                          hx-trigger="change, submit"
                          hx-target="#tour-results"
                          hx-swap="innerHTML"
                          hx-indicator="#filter-loading">

                        <!-- Search -->
                        <div class="filter-group">
                            <label for="search" class="filter-group__label">
                                <i class="fas fa-search" aria-hidden="true"></i>
                                Search Tours
                            </label>
                            <input
                                type="text"
                                id="search"
                                name="q"
                                placeholder="Search by keyword..."
                                class="filter-input"
                            >
                        </div>

                        <!-- Duration Filter -->
                        <div class="filter-group">
                            <label class="filter-group__label">
                                <i class="far fa-clock" aria-hidden="true"></i>
                                Duration
                            </label>
                            <div class="filter-options">
                                <label class="filter-radio">
                                    <input type="radio" name="duration" value="" checked>
                                    <span>All Durations</span>
                                </label>
                                <label class="filter-radio">
                                    <input type="radio" name="duration" value="1">
                                    <span>1 Day</span>
                                </label>
                                <label class="filter-radio">
                                    <input type="radio" name="duration" value="2-5">
                                    <span>2-5 Days</span>
                                </label>
                                <label class="filter-radio">
                                    <input type="radio" name="duration" value="6+">
                                    <span>6+ Days</span>
                                </label>
                            </div>
                        </div>

                        <!-- Sort By -->
                        <div class="filter-group">
                            <label for="sort" class="filter-group__label">
                                <i class="fas fa-sort" aria-hidden="true"></i>
                                Sort By
                            </label>
                            <select id="sort" name="sort" class="filter-select">
                                <option value="latest">Latest Tours</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                                <option value="rating">Highest Rated</option>
                                <option value="popular">Most Popular</option>
                            </select>
                        </div>

                        <!-- Hidden field for per_page -->
                        <input type="hidden" name="per_page" value="12">

                        <!-- Apply Button -->
                        <button type="submit" class="btn btn--primary btn--block">
                            <span id="filter-loading" class="htmx-indicator">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                            <span>Apply Filters</span>
                        </button>
                    </form>

                    <!-- Filter Tips (Optional) -->
                    <div class="filter-tips">
                        <p class="filter-tips__title">💡 Pro Tip</p>
                        <p class="filter-tips__text">
                            Use filters to find your perfect adventure. All tours include expert guides and flexible itineraries.
                        </p>
                    </div>
                </aside>

                <!-- TOUR RESULTS -->
                <div class="tours-catalog__results">

                    <!-- Results Header -->
                    <div class="results-header">
                        <div class="results-header__count">
                            <h2 id="results-count">Loading tours...</h2>
                        </div>
                        <!-- Mobile Filter Toggle -->
                        <button class="btn btn--secondary mobile-filter-toggle" id="mobile-filter-toggle">
                            <i class="fas fa-filter"></i>
                            Filters
                        </button>
                    </div>

                    <!-- Tour Grid (HTMX loads here) -->
                    <div id="tour-results"
                         hx-get="http://127.0.0.1:8000/partials/tours?per_page=12"
                         hx-trigger="load"
                         hx-swap="innerHTML">
                        <!-- Loading Skeleton -->
                        <div class="loading-skeleton">
                            <div class="skeleton-card"></div>
                            <div class="skeleton-card"></div>
                            <div class="skeleton-card"></div>
                            <div class="skeleton-card"></div>
                            <div class="skeleton-card"></div>
                            <div class="skeleton-card"></div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <!-- =====================================================
         FOOTER (Same as index.html)
         ===================================================== -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-main">
                <!-- Footer Brand -->
                <div class="footer-brand">
                    <div class="footer-brand__text">Jahongir <strong>Travel</strong></div>
                    <p>Discover the magic of Uzbekistan with expert-guided tours through the ancient Silk Road.</p>
                    <p>📧 info@jahongirtravel.com</p>
                    <p>📞 +998 99 123 4567</p>
                </div>

                <!-- Quick Links -->
                <div class="footer-col">
                    <h3 class="footer-nav__title">Quick Links</h3>
                    <ul class="footer-nav__list">
                        <li><a href="/">Home</a></li>
                        <li><a href="/tours.html">All Tours</a></li>
                        <li><a href="/destinations/">Destinations</a></li>
                        <li><a href="/about/">About Us</a></li>
                    </ul>
                </div>

                <!-- Popular Tours -->
                <div class="footer-col">
                    <h3 class="footer-nav__title">Popular Tours</h3>
                    <ul class="footer-nav__list">
                        <li><a href="/tours/silk-road-classic/">Silk Road Classic</a></li>
                        <li><a href="/tours/samarkand/">Samarkand City Tour</a></li>
                        <li><a href="/tours/bukhara/">Bukhara Old City</a></li>
                        <li><a href="/tours/chimgan/">Chimgan Mountains</a></li>
                    </ul>
                </div>

                <!-- Destinations -->
                <div class="footer-col">
                    <h3 class="footer-nav__title">Destinations</h3>
                    <ul class="footer-nav__list">
                        <li><a href="/destinations/samarkand/">Samarkand</a></li>
                        <li><a href="/destinations/bukhara/">Bukhara</a></li>
                        <li><a href="/destinations/khiva/">Khiva</a></li>
                        <li><a href="/destinations/tashkent/">Tashkent</a></li>
                    </ul>
                </div>

                <!-- Social -->
                <div class="footer-col">
                    <h3 class="footer-nav__title">Follow Us</h3>
                    <ul class="footer-social__list">
                        <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
                        <li><a href="#"><i class="fab fa-instagram"></i> Instagram</a></li>
                        <li><a href="#"><i class="fab fa-youtube"></i> YouTube</a></li>
                        <li><a href="#"><i class="fab fa-tripadvisor"></i> TripAdvisor</a></li>
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="footer-bottom__wrap">
                    <p>&copy; 2025 Jahongir Travel. All rights reserved.</p>
                    <div class="footer-bottom__legal">
                        <a href="/privacy/">Privacy Policy</a>
                        <span>|</span>
                        <a href="/terms/">Terms of Service</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- =====================================================
         SCRIPTS
         ===================================================== -->

    <!-- HTMX Library -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>

    <!-- Main JavaScript -->
    <script src="js/main.js"></script>

    <!-- Tours Page Specific JS -->
    <script>
        // Mobile filter toggle
        const mobileFilterToggle = document.getElementById('mobile-filter-toggle');
        const filtersSidebar = document.getElementById('filters-sidebar');

        if (mobileFilterToggle && filtersSidebar) {
            mobileFilterToggle.addEventListener('click', function() {
                filtersSidebar.classList.toggle('is-open');
                this.querySelector('i').classList.toggle('fa-filter');
                this.querySelector('i').classList.toggle('fa-times');
            });
        }

        // Reset filters
        const resetButton = document.getElementById('reset-filters');
        if (resetButton) {
            resetButton.addEventListener('click', function(e) {
                e.preventDefault();
                const form = document.getElementById('tour-filters');
                form.reset();

                // Trigger HTMX reload with no filters
                htmx.ajax('GET', 'http://127.0.0.1:8000/partials/tours?per_page=12', {
                    target: '#tour-results',
                    swap: 'innerHTML'
                });
            });
        }

        // Update results count after HTMX swap
        document.body.addEventListener('htmx:afterSwap', function(evt) {
            if (evt.detail.target.id === 'tour-results') {
                const tourCards = evt.detail.target.querySelectorAll('.tour-card');
                const count = tourCards.length;
                const countElement = document.getElementById('results-count');
                if (countElement && count > 0) {
                    countElement.textContent = `${count} tours found`;
                } else if (countElement) {
                    countElement.textContent = 'No tours found';
                }
            }
        });

        // Close mobile filters when clicking outside
        document.addEventListener('click', function(e) {
            if (filtersSidebar && filtersSidebar.classList.contains('is-open')) {
                if (!filtersSidebar.contains(e.target) && !mobileFilterToggle.contains(e.target)) {
                    filtersSidebar.classList.remove('is-open');
                    mobileFilterToggle.querySelector('i').classList.add('fa-filter');
                    mobileFilterToggle.querySelector('i').classList.remove('fa-times');
                }
            }
        });

        console.log('[Tours Page] Loaded successfully');
    </script>

</body>
</html>
```

**✅ Checkpoint:** Save the file in `D:\xampp82\htdocs\jahongir-custom-website\tours.html`

---

### **STEP 3: Add CSS for Tours Page** (30 minutes)

Add these styles to your `style.css` file.

**File:** `D:\xampp82\htdocs\jahongir-custom-website\style.css`

**Add at the end of the file:**

```css
/* =====================================================
   TOURS CATALOG PAGE
   ===================================================== */

/* Page Header */
.page-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 80px 0 60px;
  text-align: center;
}

.page-header__title {
  font-size: clamp(2.5rem, 5vw, 3.5rem);
  font-weight: 700;
  margin: 0 0 16px 0;
  font-family: 'Playfair Display', serif;
}

.page-header__subtitle {
  font-size: clamp(1.125rem, 2vw, 1.25rem);
  opacity: 0.95;
  max-width: 600px;
  margin: 0 auto;
}

/* Tours Catalog Layout */
.tours-catalog {
  padding: 64px 0;
}

.tours-catalog__layout {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 48px;
  align-items: start;
}

/* Filters Sidebar */
.tours-catalog__filters {
  position: sticky;
  top: 96px;
  background: white;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 2px solid #f3f4f6;
}

.filters-header__title {
  font-size: 1.375rem;
  font-weight: 700;
  margin: 0;
  color: #111827;
}

.filters-header__reset {
  background: none;
  border: none;
  color: #3b82f6;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 6px;
  transition: all 0.2s;
}

.filters-header__reset:hover {
  background: #eff6ff;
}

/* Filter Groups */
.filter-group {
  margin-bottom: 28px;
}

.filter-group__label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  font-size: 0.9375rem;
  color: #374151;
  margin-bottom: 12px;
}

.filter-group__label i {
  color: #6b7280;
  font-size: 0.875rem;
}

.filter-input {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.9375rem;
  transition: all 0.2s;
  font-family: inherit;
}

.filter-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-select {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.9375rem;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
  font-family: inherit;
}

.filter-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Radio Filters */
.filter-options {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.filter-radio {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.9375rem;
}

.filter-radio:hover {
  background: #f9fafb;
}

.filter-radio input[type="radio"] {
  cursor: pointer;
  width: 18px;
  height: 18px;
}

.filter-radio input[type="radio"]:checked + span {
  color: #3b82f6;
  font-weight: 600;
}

/* Filter Tips */
.filter-tips {
  margin-top: 32px;
  padding: 16px;
  background: #fffbeb;
  border: 1px solid #fbbf24;
  border-radius: 8px;
}

.filter-tips__title {
  font-weight: 600;
  font-size: 0.9375rem;
  color: #92400e;
  margin: 0 0 8px 0;
}

.filter-tips__text {
  font-size: 0.875rem;
  color: #78350f;
  margin: 0;
  line-height: 1.5;
}

/* Results Section */
.tours-catalog__results {
  min-height: 600px;
}

.results-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
}

.results-header__count h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  margin: 0;
}

.mobile-filter-toggle {
  display: none; /* Hidden on desktop */
}

/* Loading Skeleton */
.loading-skeleton {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 32px;
}

.skeleton-card {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: skeleton-loading 1.5s infinite;
  border-radius: 16px;
  height: 420px;
}

@keyframes skeleton-loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* =====================================================
   MOBILE RESPONSIVE (Tablets and Below)
   ===================================================== */

@media (max-width: 1024px) {
  .tours-catalog__layout {
    grid-template-columns: 1fr;
    gap: 24px;
  }

  .tours-catalog__filters {
    position: fixed;
    top: 0;
    left: -100%;
    height: 100vh;
    width: 320px;
    max-width: 85vw;
    z-index: 1000;
    transition: left 0.3s ease;
    overflow-y: auto;
    margin: 0;
  }

  .tours-catalog__filters.is-open {
    left: 0;
    box-shadow: 4px 0 12px rgba(0, 0, 0, 0.1);
  }

  .mobile-filter-toggle {
    display: inline-flex;
  }

  .results-header {
    flex-wrap: wrap;
    gap: 16px;
  }
}

@media (max-width: 768px) {
  .page-header {
    padding: 48px 0 32px;
  }

  .tours-catalog {
    padding: 32px 0;
  }

  .tours-catalog__filters {
    padding: 24px;
    width: 100%;
    max-width: 100%;
  }

  .filter-group {
    margin-bottom: 20px;
  }

  .btn--block {
    width: 100%;
  }
}

/* Mobile Filter Backdrop */
@media (max-width: 1024px) {
  .tours-catalog__filters.is-open::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: -1;
  }
}
```

**✅ Checkpoint:** Save the file.

---

### **STEP 4: Copy to Laravel Public Folder** (5 minutes)

Copy the completed `tours.html` to Laravel's public folder.

**Windows Command:**
```bash
cp D:/xampp82/htdocs/jahongir-custom-website/tours.html D:/xampp82/htdocs/ssst3/public/tours.html
```

**✅ Checkpoint:** File copied to production.

---

### **STEP 5: Test Everything** (20 minutes)

Now test the complete tours page!

**Test 1: Initial Load**
```
http://127.0.0.1:8000/tours.html
```

**Expected:**
- ✅ Purple gradient header "All Tours"
- ✅ Sidebar with filters on the left
- ✅ 12 tour cards in grid on the right
- ✅ "Load More Tours" button
- ✅ "Showing 1-12 of X tours"

---

**Test 2: Search Filter**
- Type "Samarkand" in search box
- Click "Apply Filters"

**Expected:**
- ✅ Only tours with "Samarkand" in title/description show
- ✅ Results count updates
- ✅ No page refresh (HTMX swap)

---

**Test 3: Duration Filter**
- Select "1 Day" radio button
- Form auto-submits (no button click needed)

**Expected:**
- ✅ Only 1-day tours show
- ✅ Grid updates dynamically

---

**Test 4: Sort**
- Select "Price: Low to High"
- Form auto-submits

**Expected:**
- ✅ Tours reorder by price (cheapest first)

---

**Test 5: Reset Filters**
- Click "Reset All" button

**Expected:**
- ✅ All filters clear
- ✅ All 12 tours show again

---

**Test 6: Load More**
- Scroll down
- Click "Load More Tours"

**Expected:**
- ✅ Next 12 tours append
- ✅ Button updates or disappears

---

**Test 7: Mobile (Resize browser to < 768px)**
- Resize browser window

**Expected:**
- ✅ Sidebar hides
- ✅ "Filters" button appears in top right
- ✅ Click "Filters" → sidebar slides in from left
- ✅ Click outside → sidebar closes

---

## 📊 Phase 2 Completion Checklist

Before calling Phase 2 complete, verify:

**Backend:**
- [ ] SearchController enhanced with pagination
- [ ] Filters work (search, duration, sort)
- [ ] "Load More" works with filtered results
- [ ] Cache working properly

**Frontend:**
- [ ] tours.html created in template folder
- [ ] tours.html copied to Laravel public/
- [ ] CSS added to style.css
- [ ] All filters render correctly
- [ ] Sidebar styled properly

**Functionality:**
- [ ] Initial load shows 12 tours
- [ ] Search filter works
- [ ] Duration filter works
- [ ] Sort options work
- [ ] "Reset All" clears filters
- [ ] "Load More" appends tours
- [ ] Mobile filters slide in/out
- [ ] No JavaScript errors in console

**Design:**
- [ ] Matches existing site design
- [ ] Purple gradient header
- [ ] Tour cards styled correctly
- [ ] Hover effects work
- [ ] Responsive on mobile
- [ ] Accessible (keyboard navigation works)

---

## 🐛 Troubleshooting

### Problem: Filters not working
**Solution:**
- Check SearchController has pagination
- Verify HTMX URL is correct (http://127.0.0.1:8000)
- Check browser console for errors

### Problem: "Load More" not working with filters
**Solution:**
- SearchController must return `$isAppend` variable
- Blade view must handle `$isAppend` correctly

### Problem: Mobile filters not sliding
**Solution:**
- Check CSS is loaded
- Verify JavaScript runs
- Check `.is-open` class toggles

### Problem: Results count not updating
**Solution:**
- Check HTMX `htmx:afterSwap` event listener
- Verify `#results-count` element exists

---

## 🎉 What You'll Have After Phase 2

✅ **Complete tours listing page** at `/tours.html`
✅ **Sidebar filters** (search, duration, sort)
✅ **12 tours per page** with "Load More"
✅ **Mobile-responsive** design
✅ **Dynamic filtering** with no page refresh
✅ **Production-ready** and fully functional

---

## 🚀 Next Steps (Phase 3 - Optional)

After Phase 2, you could add:
1. Price range slider
2. City/category filters
3. Save favorite tours (localStorage)
4. Share tour links
5. Advanced search with multiple keywords
6. Tour availability calendar

---

## 📝 Summary

**Phase 2 Duration:** 2 - 2.5 hours
**Files Modified:** 2 (SearchController.php, style.css)
**Files Created:** 2 (tours.html in template and public)
**Lines of Code:** ~500

**Ready to start Phase 2?** 🚀

Say "start Phase 2" and I'll guide you through each step!
