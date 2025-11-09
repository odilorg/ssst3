# Phase 5: Convert Dynamic Pages with Regex Injection - Detailed Plan

## Overview
Goal: Convert remaining pages that use regex injection with dynamic database content to Blade templates
Time: 1-2 hours
Complexity: High (dynamic data, complex logic)

---

## Pages to Convert

1. **Category Landing Pages** `/tours/category/{slug}` - Tour listings by category
2. **Tour Details Pages** `/tours/{slug}` - Individual tour pages with booking forms
3. **Destination Landing Pages** `/destinations/{slug}` - City/destination pages

---

## Task 5.1: Convert Category Landing Pages (30 minutes)

**Current State:**
- Route: `/tours/category/{slug}`
- Uses: `category-landing.html` + regex injection
- Dynamic: Tour listings filtered by category, SEO meta tags

**Current Implementation:**
```php
Route::get('/tours/category/{slug}', function ($slug) {
    $category = TourCategory::where('slug', $slug)->firstOrFail();
    $tours = Tour::where('category_id', $category->id)->get();

    $html = file_get_contents(public_path('category-landing.html'));

    // Regex injection for:
    // - Category name/description
    // - Tour cards grid
    // - Meta tags

    return response($html);
});
```

**Steps:**
1. Analyze current regex patterns in the route
2. Create `resources/views/pages/category-landing.blade.php`
3. Extract HTML structure from `public/category-landing.html`
4. Replace regex patterns with Blade directives:
   ```blade
   @foreach($tours as $tour)
     <div class="tour-card">
       <h3>{{ $tour->translated_name }}</h3>
       <p>{{ $tour->translated_description }}</p>
       <span>${{ $tour->price_from }}</span>
     </div>
   @endforeach
   ```
5. Update route to pass data to Blade view
6. Test with multiple categories

**Success Criteria:**
- Category pages display correct tours
- SEO meta tags dynamic (category name in title)
- Pagination works if present
- Empty state handled (no tours in category)

---

## Task 5.2: Convert Tour Details Pages (45 minutes)

**Current State:**
- Route: `/tours/{slug}`
- Uses: `tour-details.html` + regex injection
- Dynamic: Tour info, pricing, itinerary, booking form, related tours

**Current Implementation:**
```php
Route::get('/tours/{slug}', function ($slug) {
    $tour = Tour::where('slug', $slug)->with(['category', 'inclusions'])->firstOrFail();

    $html = file_get_contents(public_path('tour-details.html'));

    // Complex regex for:
    // - Tour name, description, duration, price
    // - Image gallery
    // - Itinerary days
    // - Inclusions/exclusions
    // - Booking form
    // - Related tours
    // - Reviews

    return response($html);
});
```

**Steps:**
1. Map all dynamic sections in tour-details.html
2. Create `resources/views/pages/tour-details.blade.php`
3. Convert sections systematically:
   - Hero with tour name/image
   - Tour overview (duration, group size, price)
   - Itinerary (@foreach days)
   - Inclusions/exclusions (@foreach items)
   - Booking form (with @csrf)
   - Image gallery (@foreach images)
   - Related tours (@foreach)
4. Handle optional fields (some tours may not have all data)
5. Update route to eager load relationships
6. Test with different tour types

**Success Criteria:**
- Tour details display correctly
- All dynamic sections render
- Booking form has CSRF token
- Related tours show correctly
- Empty states handled gracefully

---

## Task 5.3: Convert Destination Landing Pages (30 minutes)

**Current State:**
- Route: `/destinations/{slug}`
- Uses: `destination-landing.html` + regex injection
- Dynamic: City info, attractions, tours in that destination

**Current Implementation:**
```php
Route::get('/destinations/{slug}', function ($slug) {
    $city = City::where('slug', $slug)->firstOrFail();
    $attractions = $city->attractions;
    $tours = Tour::whereHas('destinations', fn($q) => $q->where('city_id', $city->id))->get();

    $html = file_get_contents(public_path('destination-landing.html'));

    // Regex for:
    // - City name, description, tagline
    // - Attractions list
    // - Tours available
    // - Hero image

    return response($html);
});
```

**Steps:**
1. Analyze current destination page structure
2. Create `resources/views/pages/destination-landing.blade.php`
3. Extract and convert sections:
   - Hero with city name/image
   - City overview
   - Attractions grid (@foreach)
   - Available tours (@foreach)
   - Travel tips/info
4. Update route with proper data loading
5. Test with different destinations

**Success Criteria:**
- City pages display correctly
- Attractions render properly
- Tours filtered by destination
- SEO meta tags include city name

---

## Common Patterns for All Pages

### Blade Conversion Strategy

**1. Dynamic Content Replacement:**
```php
// OLD (Regex)
$html = preg_replace('/<h1>.*?<\/h1>/', '<h1>' . $tour->name . '</h1>', $html);

// NEW (Blade)
<h1>{{ $tour->translated_name }}</h1>
```

**2. Loops:**
```php
// OLD (String concatenation)
$toursHtml = '';
foreach ($tours as $tour) {
    $toursHtml .= '<div>...</div>';
}

// NEW (Blade)
@foreach($tours as $tour)
  <div>...</div>
@endforeach
```

**3. Conditionals:**
```php
// OLD (Ternary in string)
$priceText = $tour->price ? '$' . $tour->price : 'Contact us';

// NEW (Blade)
@if($tour->price)
  ${{ $tour->price }}
@else
  Contact us
@endif
```

**4. Asset Paths:**
```php
// OLD (Hardcoded)
<img src="/images/tours/{{ $tour->id }}.jpg">

// NEW (Blade helper)
<img src="{{ asset('images/tours/' . $tour->id . '.jpg') }}">
```

---

## Route Updates Pattern

**Before:**
```php
Route::get('/tours/category/{slug}', function ($slug) {
    // ... database queries
    $html = file_get_contents(public_path('category-landing.html'));
    // ... 50+ lines of regex
    return response($html);
});
```

**After:**
```php
Route::get('/tours/category/{slug}', function ($slug) {
    $category = TourCategory::where('slug', $slug)->firstOrFail();
    $tours = Tour::where('category_id', $category->id)
        ->published()
        ->with(['category', 'destinations'])
        ->get();

    return view('pages.category-landing', compact('category', 'tours'));
});
```

---

## SEO Meta Tags Strategy

Each page needs dynamic SEO tags:

```blade
@section('title', $category->translated_name . ' Tours - Jahongir Travel')
@section('meta_description', 'Discover ' . $category->translated_description . '. Book authentic tours with Jahongir Travel.')
@section('canonical', url('/tours/category/' . $category->slug))

@section('structured_data')
{
  "@@context": "https://schema.org",
  "@@type": "CollectionPage",
  "name": "{{ $category->translated_name }} Tours",
  "description": "{{ $category->translated_description }}"
}
@endsection
```

---

## Testing Strategy

### Test Each Page Type:

**Category Landing:**
- Test with category that has many tours
- Test with category that has 0 tours
- Test with category that has 1 tour
- Verify pagination if implemented

**Tour Details:**
- Test with fully populated tour (all fields)
- Test with minimal tour (only required fields)
- Test booking form submission
- Verify related tours show correctly

**Destination Landing:**
- Test with city that has many attractions
- Test with city that has no attractions
- Test tours filtered correctly by destination

---

## Potential Issues & Solutions

### Issue 1: Complex Regex Patterns
**Problem:** Existing regex patterns are hard to understand
**Solution:** Read the HTML output manually, identify sections, convert one-by-one

### Issue 2: Missing Data
**Problem:** Some tours/cities may not have all fields populated
**Solution:** Use @if/@isset in Blade, provide fallbacks

### Issue 3: Performance
**Problem:** N+1 queries if relationships not eager loaded
**Solution:** Use ->with() to eager load relationships

### Issue 4: Image Paths
**Problem:** Images may be in different locations
**Solution:** Create helper method for tour images, use fallback placeholder

### Issue 5: Booking Form
**Problem:** Form submission logic may be complex
**Solution:** Keep existing form action/logic, just add @csrf

---

## Git Strategy

Commit after each page type:
```bash
git add resources/views/pages/category-landing.blade.php routes/web.php
git commit -m "Phase 5.1: Convert category landing pages to Blade"

git add resources/views/pages/tour-details.blade.php routes/web.php
git commit -m "Phase 5.2: Convert tour details pages to Blade"

git add resources/views/pages/destination-landing.blade.php routes/web.php
git commit -m "Phase 5.3: Convert destination landing pages to Blade"
```

---

## Expected File Structure After Phase 5

```
resources/views/
├── layouts/
│   └── main.blade.php                  ✅ Phase 1
├── partials/
│   ├── header.blade.php                ✅ Phase 1
│   └── footer.blade.php                ✅ Phase 1
├── pages/
│   ├── home.blade.php                  ✅ Phase 3
│   ├── about.blade.php                 ✅ Phase 4
│   ├── contact.blade.php               ✅ Phase 4
│   ├── tours-listing.blade.php         ✅ Phase 4
│   ├── destinations.blade.php          ✅ Phase 4
│   ├── category-landing.blade.php      ← Phase 5.1
│   ├── tour-details.blade.php          ← Phase 5.2
│   └── destination-landing.blade.php   ← Phase 5.3
└── test-layout.blade.php               ✅ Test
```

---

## Success Metrics

- ✅ 3 additional dynamic page types converted
- ✅ All regex injection removed
- ✅ Database queries optimized (eager loading)
- ✅ All pages tested with real data
- ✅ SEO meta tags dynamic
- ✅ Forms have CSRF protection
- ✅ No breaking changes to functionality

---

## Time Estimate

- Category landing: 30 minutes
- Tour details: 45 minutes (most complex)
- Destination landing: 30 minutes
- Testing & fixes: 15 minutes
- **Total: 1.5-2 hours**

---

## Next Steps After Phase 5

Once all pages are converted:
1. Remove old static HTML files (public/*.html)
2. Update documentation
3. Performance testing
4. Consider creating Blade components for reusable elements
5. Consider moving to controllers instead of route closures

---

**Ready to begin?** Let's start with Category Landing pages (easiest of the three)!
