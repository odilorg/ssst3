@extends('layouts.main')

@section('title', $pageTitle)
@section('meta_description', $metaDescription)
@section('canonical', $canonicalUrl)

{{-- Open Graph / Facebook --}}
@section('og_type', 'website')
@section('og_url', $canonicalUrl)
@section('og_title', $pageTitle)
@section('og_description', $metaDescription)
@section('og_image', $ogImage)

{{-- Twitter Card --}}
@section('twitter_url', $canonicalUrl)
@section('twitter_title', $pageTitle)
@section('twitter_description', $metaDescription)
@section('twitter_image', $ogImage)

{{-- Structured Data for Destination --}}
@section('structured_data')
{
  "@@context": "https://schema.org",
  "@@type": "TouristDestination",
  "name": "{{ $city->name }}",
  "description": "{{ $city->short_description ?? $city->description ?? 'Explore ' . $city->name . ' with Jahongir Travel' }}",
  "url": "{{ $canonicalUrl }}",
  "image": "{{ $ogImage }}",
  @if($city->latitude && $city->longitude)
  "geo": {
    "@@type": "GeoCoordinates",
    "latitude": "{{ $city->latitude }}",
    "longitude": "{{ $city->longitude }}"
  },
  @endif
  "touristType": ["Cultural tourism", "Historical tourism", "Adventure tourism"],
  "containedInPlace": {
    "@@type": "Country",
    "name": "Uzbekistan"
  },
  "isAccessibleForFree": false,
  "publicAccess": true
}
@endsection

{{-- Breadcrumb Structured Data --}}
@push('structured_data_breadcrumb')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "{{ url('/') }}"
    },
    {
      "@@type": "ListItem",
      "position": 2,
      "name": "Destinations",
      "item": "{{ url('/destinations') }}"
    },
    {
      "@@type": "ListItem",
      "position": 3,
      "name": "{{ $city->name }}",
      "item": "{{ $canonicalUrl }}"
    }
  ]
}
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/category-landing.css') }}">
@endpush

@section('content')
    <!-- =====================================================
         CATEGORY HERO
         ===================================================== -->
    <section class="category-hero" id="category-hero" style="background-image: url('{{ $ogImage }}');">
        <div class="category-hero__overlay"></div>
        <div class="container">
            <div class="category-hero__content">

                <!-- Category Icon -->
                <div class="category-hero__icon" id="category-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>

                <!-- Category Name -->
                <h1 class="category-hero__title" id="category-name">{{ $city->name }}</h1>

                <!-- Category Description -->
                <p class="category-hero__description" id="category-description">
                    {{ $city->short_description ?? 'Discover amazing tours in ' . $city->name }}
                </p>

                <!-- Tour Count Badge -->
                <div class="category-hero__badge" id="category-badge">
                    <i class="fas fa-map-marked-alt"></i>
                    <span id="tour-count-badge">Discover tours</span>
                </div>
            </div>
        </div>
    </section>

    <!-- =====================================================
         TOURS CATALOG (Sidebar + Grid)
         ===================================================== -->
    <section class="tours-catalog" id="main-content">
        <div class="container">
<!-- Breadcrumb -->            <nav class="breadcrumb breadcrumb--light" aria-label="Breadcrumb">                <a href="/">Home</a>                <span class="breadcrumb__separator">/</span>                <a href="/tours">Tours</a>                <span class="breadcrumb__separator">/</span>                <span>{{ $city->name }}</span>            </nav>
            <div class="tours-catalog__layout">

                <!-- SIDEBAR FILTERS (Desktop) -->
                <aside class="tours-catalog__filters" id="filters-sidebar">
                    <div class="filters-header">
                        <h2 class="filters-header__title">Filter Tours</h2>
                        <button class="filters-header__reset" id="reset-filters" type="button">
                            <i class="fas fa-redo" aria-hidden="true"></i>
                            Reset All
                        </button>
                    </div>

                    <form id="tour-filters"
                          hx-get="{{ url('/partials/tours/search') }}"
                          hx-trigger="change, submit"
                          hx-target="#tour-results"
                          hx-swap="innerHTML"
                          hx-indicator="#filter-loading">

                        <!-- Hidden category field -->
                        <input type="hidden" name="category" id="category-slug" value="">

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
                </aside>

                <!-- TOUR RESULTS -->
                <div class="tours-catalog__results">

                    <!-- Results Header -->
                    <div class="results-header">
                        <div class="results-header__count">
                            <h2 id="results-count">Loading tours...</h2>
                        </div>
                        <!-- Mobile Filter Toggle -->
                        <button class="btn btn--secondary mobile-filter-toggle" id="mobile-filter-toggle" type="button">
                            <i class="fas fa-filter"></i>
                            Filters
                        </button>
                    </div>

                    <!-- Tour Grid (HTMX loads here) -->
                    <div id="tour-results"
                         hx-get="{{ url('/partials/tours/search?city=' . $city->id) }}"
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
         RELATED CATEGORIES
         ===================================================== -->
    <section class="related-categories">
        <div class="container">
            <div class="section-header">
                <h2 class="section-header__title">Explore Other Destinations</h2>
                <p class="section-header__subtitle">Discover more cities and regions in Uzbekistan</p>
            </div>

            <div id="related-categories"
                 hx-get="{{ url('/partials/categories/related?current=' . $city->id . '&limit=5') }}"
                 hx-trigger="load"
                 hx-swap="innerHTML">
                <!-- Loading skeleton -->
                <div class="related-categories-grid">
                    <div class="skeleton-card skeleton-card--small"></div>
                    <div class="skeleton-card skeleton-card--small"></div>
                    <div class="skeleton-card skeleton-card--small"></div>
                    <div class="skeleton-card skeleton-card--small"></div>
                    <div class="skeleton-card skeleton-card--small"></div>
                </div>
            </div>
        </div>
    </section>@endsection

@push('scripts')
<!-- HTMX Library (Local) -->
<script src="{{ asset('js/htmx.min.js') }}"></script>
<!-- Destination Landing Page Specific JS -->
<script src="{{ asset('js/destination-landing.js') }}"></script>
@endpush
