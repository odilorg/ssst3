# SSST3 Frontend Codebase Analysis

## Project Overview

**Stack:** Laravel 11 + Filament v4 + HTMX
**Purpose:** Tour booking website for Jahongir Travel
**URL:** http://127.0.0.1:8000

---

## Core Models (Public Frontend)

### Tour Model

**File:** `app/Models/Tour.php`

#### Fields

| Category | Fields |
|----------|--------|
| Basic Info | `title`, `slug`, `short_description`, `long_description` |
| Duration | `duration_days`, `duration_text` |
| Pricing | `price_per_person`, `currency` |
| Capacity | `max_guests`, `min_guests` |
| Images | `hero_image`, `gallery_images` (JSON array) |
| Content (JSON) | `highlights`, `included_items`, `excluded_items`, `languages`, `requirements` |
| Tour Meta | `tour_type`, `city_id`, `is_active` |
| Ratings | `rating`, `review_count` (cached values) |
| Booking Settings | `min_booking_hours`, `has_hotel_pickup`, `pickup_radius_km` |
| Meeting Point | `meeting_point_address`, `meeting_instructions`, `meeting_lat`, `meeting_lng` |
| Cancellation | `cancellation_hours`, `cancellation_policy` |
| Flags | `include_global_requirements`, `include_global_faqs` |

#### Relationships

```php
// Belongs To
public function city() -> City

// Has Many
public function itineraryItems() -> ItineraryItem
public function bookings() -> Booking
public function faqs() -> TourFaq
public function extras() -> TourExtra
public function reviews() -> Review
public function inquiries() -> TourInquiry

// Many to Many
public function categories() -> TourCategory (pivot: tour_category_tour)
```

#### Query Scopes

| Scope | Description |
|-------|-------------|
| `active()` | Filter only active tours |
| `withFrontendRelations()` | Eager load city and categories |
| `withDetailRelations()` | Eager load all detail page relations |
| `withReviews()` | Tours with ratings and reviews |
| `byCity($cityId)` | Filter by city ID or slug |
| `byCategory($categoryId)` | Filter by category ID or slug |
| `popular($direction)` | Order by rating and review count |
| `recent($direction)` | Order by creation date |
| `byType($type)` | Filter by tour_type (private, group, day_trip) |
| `byDuration($min, $max)` | Filter by duration_days |
| `byPriceRange($min, $max)` | Filter by price_per_person |

#### Helper Methods

- `isSingleDay()` / `isMultiDay()` - Check duration type
- `getFormattedDuration()` - Human-readable duration
- `updateRatingCache()` - Recalculate rating from approved reviews
- `clearCategoryCaches()` - Clear related category caches
- `getFeaturedImageUrlAttribute()` - Get full URL for hero_image

---

### BlogPost Model

**File:** `app/Models/BlogPost.php`

#### Fields

| Category | Fields |
|----------|--------|
| Relations | `category_id`, `city_id` |
| Content | `title`, `slug`, `excerpt`, `content` |
| Media | `featured_image` |
| Author | `author_name`, `author_image` |
| Metrics | `reading_time`, `view_count` |
| Status | `is_featured`, `is_published`, `published_at` |
| SEO | `meta_title`, `meta_description` |

#### Relationships

```php
// Belongs To
public function category() -> BlogCategory
public function city() -> City

// Many to Many
public function tags() -> BlogTag (pivot: blog_post_tag)

// Has Many
public function comments() -> BlogComment
public function approvedComments() -> BlogComment (filtered)
public function topLevelComments() -> BlogComment (no parent)
```

#### Query Scopes

| Scope | Description |
|-------|-------------|
| `published()` | is_published = true AND published_at NOT NULL |
| `featured()` | is_featured = true |
| `popular($limit)` | Order by view_count desc |
| `recent($limit)` | Order by published_at desc |

#### Special Method: `getRelatedTours($limit)`

Smart tour matching:
1. By `city_id` if set
2. By city name found in content
3. Fallback to popular tours

---

## Related Models

### TourCategory

**File:** `app/Models/TourCategory.php`

**Features:**
- Multi-language support: `name`, `description`, `meta_title`, `meta_description` stored as JSON arrays
- Soft deletes enabled

**Fields:**
- `name` (JSON), `slug`, `description` (JSON)
- `icon`, `image_path`, `hero_image`
- `display_order`, `is_active`, `show_on_homepage`
- `meta_title` (JSON), `meta_description` (JSON)

**Key Methods:**
- `getTranslatedNameAttribute()` - Get name for current locale
- `getCachedTourCountAttribute()` - Cached active tour count
- `getHomepageCategories()` - Static cached homepage categories

---

### BlogCategory

**File:** `app/Models/BlogCategory.php`

**Fields:**
- `name`, `slug`, `description`, `image`
- `is_active`, `display_order`

**Relationships:**
- `posts()` -> BlogPost
- `publishedPosts()` -> BlogPost (filtered)

---

## Routes

### Web Routes (`routes/web.php`)

#### Public Pages

| Route | Handler | Name |
|-------|---------|------|
| `GET /` | `HomeController@index` | `home` |
| `GET /about` | Closure | `about` |
| `GET /contact` | Closure | `contact` |
| `GET /privacy` | Closure | `privacy` |
| `GET /terms` | Closure | `terms` |

#### Tour Routes

| Route | Handler | Name |
|-------|---------|------|
| `GET /tours` | `TourListingController@index` | `tours.index` |
| `GET /tours/category/{slug}` | `CategoryLandingController@show` | `tours.category` |
| `GET /tours/{slug}` | Closure (inline) | `tours.show` |
| `POST /tours/{slug}/reviews` | `ReviewController@store` | `reviews.store` |

#### Blog Routes

| Route | Handler | Name |
|-------|---------|------|
| `GET /blog` | `BlogController@index` | `blog.index` |
| `GET /blog/{slug}` | `BlogController@show` | `blog.show` |
| `POST /comments` | `CommentController@store` | `comments.store` |

#### Destination Routes

| Route | Handler | Name |
|-------|---------|------|
| `GET /destinations` | `DestinationController@index` | `destinations.index` |
| `GET /destinations/{slug}` | `DestinationController@show` | `city.show` |

---

## HTMX Partials

All partials are under `/partials/*` prefix.

### Tour Partials

| Endpoint | Purpose |
|----------|---------|
| `/partials/tours` | Tour list |
| `/partials/tours/search` | Search/filter |
| `/partials/tours/{slug}/hero` | Hero section |
| `/partials/tours/{slug}/gallery` | Image gallery |
| `/partials/tours/{slug}/overview` | Overview text |
| `/partials/tours/{slug}/highlights` | Highlights list |
| `/partials/tours/{slug}/itinerary` | Day-by-day itinerary |
| `/partials/tours/{slug}/included-excluded` | What's included/excluded |
| `/partials/tours/{slug}/requirements` | Know before you go |
| `/partials/tours/{slug}/cancellation` | Cancellation policy |
| `/partials/tours/{slug}/meeting-point` | Meeting location |
| `/partials/tours/{slug}/faqs` | FAQs accordion |
| `/partials/tours/{slug}/extras` | Add-on services |
| `/partials/tours/{slug}/reviews` | Customer reviews |

### Blog Partials

| Endpoint | Purpose |
|----------|---------|
| `/partials/blog/{slug}/hero` | Article hero |
| `/partials/blog/{slug}/content` | Article body |
| `/partials/blog/{slug}/sidebar` | Sidebar widgets |
| `/partials/blog/{slug}/related` | Related posts |
| `/partials/blog/{slug}/comments` | Comments section |
| `/partials/blog/{slug}/related-tours` | Related tours |
| `/partials/blog/listing` | Blog listing (HTMX) |

### Other Partials

| Endpoint | Purpose |
|----------|---------|
| `/partials/categories/homepage` | Homepage categories |
| `/partials/categories/related` | Related categories |
| `/partials/cities/related` | Related cities |
| `/partials/bookings/form/{tour_slug}` | Booking form |
| `POST /partials/bookings` | Submit booking |

---

## Controllers

### Main Controllers

| Controller | File | Purpose |
|------------|------|---------|
| `HomeController` | `app/Http/Controllers/HomeController.php` | Homepage |
| `TourListingController` | `app/Http/Controllers/TourListingController.php` | Tours index |
| `CategoryLandingController` | `app/Http/Controllers/CategoryLandingController.php` | Category pages |
| `BlogController` | `app/Http/Controllers/BlogController.php` | Blog index & show |
| `DestinationController` | `app/Http/Controllers/DestinationController.php` | City/destination pages |
| `ContactController` | `app/Http/Controllers/ContactController.php` | Contact form |
| `ReviewController` | `app/Http/Controllers/ReviewController.php` | Tour reviews |
| `CommentController` | `app/Http/Controllers/CommentController.php` | Blog comments |

### Partial Controllers (`app/Http/Controllers/Partials/`)

| Controller | Purpose |
|------------|---------|
| `TourController` | HTMX tour section partials |
| `BlogController` | HTMX blog partials |
| `BookingController` | Booking form & submission |
| `SearchController` | Tour search/filter |
| `CategoryController` | Category partials |
| `CityController` | City/destination partials |

---

## Frontend Views

### Layout

**Main Layout:** `resources/views/layouts/main.blade.php`

### Tour Pages

#### Tour Details (`resources/views/pages/tour-details.blade.php`)

**Structure:**
1. SEO meta tags and structured data (TouristTrip schema)
2. Hero section (HTMX loaded)
3. Gallery section (HTMX loaded)
4. Two-column layout:
   - **Left:** Main content sections (all HTMX lazy-loaded)
     - Overview
     - Highlights
     - Included/Excluded
     - Cancellation Policy
     - Itinerary
     - Meeting Point
     - Know Before You Go
     - FAQs
     - Extra Services
     - Reviews
   - **Right:** Booking sidebar
     - Price display with breakdown
     - Date picker
     - Guest selector
     - Book/Inquiry buttons
     - Full booking form (hidden initially)
     - Simple inquiry form
     - Trust badges
     - WhatsApp contact
5. Mobile floating CTA
6. Booking confirmation modal
7. Inquiry confirmation modal

**Assets:**
- `tour-details.css`
- `css/gallery-lightbox.css`
- `tour-details-gallery-addon.css`
- `css/tour-reviews.css`
- `js/htmx.min.js`
- `tour-details.js`
- `js/booking-form.js`
- `js/gallery-lightbox.js`
- `js/tour-reviews.js`

#### Tours Listing (`resources/views/pages/tours-listing.blade.php`)

**Structure:**
1. Hero section with CTA
2. Breadcrumb navigation
3. Trust badges (5000+ travelers, 4.9/5 rating, etc.)
4. Intro content section
5. Tours grid:
   - Filter tabs by category
   - Server-rendered tour cards
   - Client-side filtering via JavaScript
   - Pagination
6. FAQ section
7. Floating WhatsApp CTA

**Features:**
- Server-side initial render with `$tours`
- `window.__INITIAL_TOURS__` for client-side filtering
- Category filter tabs
- Responsive tour cards

### Blog Pages

**Blog Index:** `resources/views/blog/index.blade.php`
**Blog Article:** `resources/views/blog/article.blade.php`

**Blog Partials:** `resources/views/partials/blog/`
- `related-tours.blade.php` - Related tours widget

---

## Caching Strategy

### Cache Keys

**Tour-related:**
- `category_{id}_tour_count` - Tour count per category (6 hours)
- `related_categories.{slug}` - Related categories
- `category_data.{slug}.{locale}` - Category page data

**Blog-related:**
- `blog.categories.all` - All active categories (1 hour)
- `blog.tags.all` - All tags with posts (1 hour)
- `blog.featured` - Featured posts (10 minutes)
- `blog.listing.{md5}` - Listing results (10 minutes)
- `blog.page.{slug}` - Single post (1 hour)
- `blog.post.{slug}` - Post with city (1 hour)

**Homepage:**
- `homepage_categories` - Homepage categories (12 hours)
- `active_categories` - All active categories

### Cache Invalidation

- Tour save/delete clears category caches
- BlogPost save/delete clears blog caches
- TourCategory save/delete clears homepage/active caches

---

## Architecture Patterns

### 1. HTMX-Driven Frontend

Pages use HTMX for progressive loading:
```html
<section hx-get="/partials/tours/{slug}/overview"
         hx-trigger="load"
         hx-swap="innerHTML">
  <!-- Loading skeleton -->
</section>
```

**Benefits:**
- Faster initial page load
- Progressive enhancement
- Server-side rendering for SEO

### 2. Server-Side Rendering + Client Enhancement

Tours listing example:
- Initial HTML rendered server-side
- JavaScript enhances with filtering
- Graceful degradation without JS

### 3. Multi-Language Support

TourCategory uses JSON for translations:
```php
protected $casts = [
    'name' => 'array',        // ['en' => '...', 'ru' => '...']
    'description' => 'array',
];
```

### 4. SEO Optimization

- Structured data (Schema.org TouristTrip)
- Meta tags (title, description, OG, Twitter)
- Canonical URLs
- Semantic HTML

### 5. Cache-First Performance

Extensive caching with automatic invalidation on model events.

---

## File Structure

```
app/
├── Http/Controllers/
│   ├── BlogController.php
│   ├── TourListingController.php
│   ├── CategoryLandingController.php
│   ├── DestinationController.php
│   ├── HomeController.php
│   ├── Partials/
│   │   ├── TourController.php
│   │   ├── BlogController.php
│   │   ├── BookingController.php
│   │   └── ...
│   └── ...
├── Models/
│   ├── Tour.php
│   ├── BlogPost.php
│   ├── TourCategory.php
│   ├── BlogCategory.php
│   ├── City.php
│   └── ...
└── Services/
    ├── TourCacheService.php
    ├── BlogListingService.php
    └── ...

resources/views/
├── layouts/
│   └── main.blade.php
├── pages/
│   ├── tour-details.blade.php
│   ├── tours-listing.blade.php
│   ├── home.blade.php
│   └── ...
├── blog/
│   ├── index.blade.php
│   └── article.blade.php
└── partials/
    ├── blog/
    └── ...

public/
├── css/
│   ├── tours-listing.css
│   ├── gallery-lightbox.css
│   └── tour-reviews.css
├── js/
│   ├── htmx.min.js
│   ├── booking-form.js
│   ├── gallery-lightbox.js
│   └── tour-reviews.js
├── tour-details.css
└── tour-details.js

routes/
├── web.php
├── api.php
└── filament.php
```

---

## Key URLs

| Page | URL |
|------|-----|
| Homepage | `/` |
| Tours listing | `/tours` |
| Tour detail | `/tours/{slug}` |
| Category page | `/tours/category/{slug}` |
| Blog listing | `/blog` |
| Blog article | `/blog/{slug}` |
| Destinations | `/destinations` |
| City page | `/destinations/{slug}` |
| Admin Panel | `/admin` |

---

## Development Notes

### Running the Application

```bash
cd D:\xampp82\htdocs\ssst3
php artisan serve --port=8000
```

Access at: http://127.0.0.1:8000

### Key Patterns

1. **HTMX for dynamic content** - Sections load progressively
2. **Blade templates** - Server-side rendering
3. **JSON casts** - Complex data stored as JSON arrays
4. **Cache everywhere** - Automatic invalidation on model events
5. **Scopes for queries** - Reusable query builders

### Adding New Features

1. Check existing patterns in similar files
2. Use HTMX partials for dynamic sections
3. Add appropriate caching
4. Implement cache invalidation in model events
5. Follow SEO best practices (meta tags, structured data)

---

*Generated: November 19, 2025*
