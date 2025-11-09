# Phase 3: Convert Homepage - Detailed Plan

## Overview
Goal: Convert homepage from static HTML with regex injection to clean Blade template
Time: 45 minutes
Complexity: High (has dynamic database content)

## Current Homepage Implementation

The homepage currently:
1. Reads static HTML from public/index.html
2. Uses PHP regex to inject dynamic content:
   - Tour categories from database
   - Blog posts (latest 3)
   - Featured cities
   - Customer reviews (7 reviews)
3. Returns modified HTML as response

## Task 3.1: Analyze Current Implementation

Check the current route to understand data flow:
- Categories: TourCategory::getHomepageCategories()
- Blog posts: BlogPost::published()->take(3)
- Cities: City::getHomepageCities()
- Reviews: Review::approved()->take(7)

## Task 3.2: Create Home Blade Template

File: resources/views/pages/home.blade.php

Strategy:
1. Extract static HTML sections from public/index.html
2. Replace regex injection points with @foreach loops
3. Keep all existing CSS classes and structure
4. Use Blade helpers for asset paths

Sections to convert:
- Hero section (static)
- Categories grid (dynamic @foreach)
- Blog posts grid (dynamic @foreach)
- Cities grid (dynamic @foreach)
- Reviews carousel (dynamic @foreach)

## Task 3.3: Update Homepage Route

Change from:
```php
Route::get('/', function () {
    // Complex regex injection logic
    return response($html);
});
```

To:
```php
Route::get('/', function () {
    $categories = \App\Models\TourCategory::getHomepageCategories();
    $blogPosts = \App\Models\BlogPost::published()->take(3)->get();
    $cities = \App\Models\City::getHomepageCities();
    $reviews = \App\Models\Review::approved()->take(7)->get();
    
    return view('pages.home', compact('categories', 'blogPosts', 'cities', 'reviews'));
});
```

## Task 3.4: Test Homepage

Verify:
- All dynamic sections render
- Categories show correct count and images
- Blog posts display properly
- Cities grid works
- Reviews carousel functions
- No broken images or links

## Success Criteria

- Homepage loads without errors
- All dynamic data displays correctly
- Layout matches original
- Performance is similar or better
- No regex injection code remains

## Rollback Plan

```bash
git reset --hard HEAD~1
```
