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
                <!-- Breadcrumb -->
                <nav class="breadcrumb" aria-label="Breadcrumb">
                    <a href="/">Home</a>
                    <span class="breadcrumb__separator">/</span>
                    <a href="/tours">Tours</a>
                    <span class="breadcrumb__separator">/</span>
                    <span id="category-breadcrumb">{{ $category->name[$locale] ?? $category->name['en'] ?? 'Category' }}</span>
                </nav>

                <!-- Category Icon -->
                <div class="category-hero__icon" id="category-icon">
                    @if($category->icon)
                        <i class="{{ $category->icon }}"></i>
                    @else
                        <i class="fas fa-map"></i>
                    @endif
                </div>

                <!-- Category Name -->
                <h1 class="category-hero__title" id="category-name">{{ $category->name[$locale] ?? $category->name['en'] ?? 'Category' }}</h1>

                <!-- Category Description -->
                <p class="category-hero__description" id="category-description">
                    {{ $category->description[$locale] ?? $category->description['en'] ?? 'Discover amazing tours in this category' }}
                </p>

                <!-- Tour Count Badge -->
                <div class="category-hero__badge" id="category-badge">
                    <i class="fas fa-map-marked-alt"></i>
                    <span id="tour-count-badge">{{ $category->tours()->where('is_active', true)->count() }} tours</span>
                </div>
            </div>
        </div>
    </section>

    <!-- =====================================================
         TOURS CATALOG (Horizontal Filters + Grid)
         ===================================================== -->
    <section class="tours-catalog" id="main-content">
        <div class="container">

            <!-- HORIZONTAL FILTERS BAR -->
            <div class="tours-catalog__filters-top">
                <form id="tour-filters"
                      hx-get="{{ url('/partials/tours/search') }}"
                      hx-trigger="change, submit"
                      hx-target="#tour-results"
                      hx-swap="innerHTML"
                      hx-indicator="#filter-loading">

                    <!-- Hidden category field -->
                    <input type="hidden" name="category" id="category-slug" value="{{ $category->slug }}">
                    <input type="hidden" name="per_page" value="12">

                    <div class="filters-horizontal">
                        <!-- Search -->
                        <div class="filter-item filter-item--search">
                            <label for="search" class="sr-only">Search Tours</label>
                            <div class="filter-input-wrapper">
                                <i class="fas fa-search" aria-hidden="true"></i>
                                <input
                                    type="text"
                                    id="search"
                                    name="q"
                                    placeholder="Search tours..."
                                    class="filter-input"
                                >
                            </div>
                        </div>

                        <!-- Duration Filter -->
                        <div class="filter-item">
                            <label for="duration" class="filter-label">
                                <i class="far fa-clock" aria-hidden="true"></i>
                                Duration
                            </label>
                            <select id="duration" name="duration" class="filter-select">
                                <option value="">All Durations</option>
                                <option value="1">1 Day</option>
                                <option value="2-5">2-5 Days</option>
                                <option value="6+">6+ Days</option>
                            </select>
                        </div>

                        <!-- Sort By -->
                        <div class="filter-item">
                            <label for="sort" class="filter-label">
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

                        <!-- Reset Button -->
                        <button class="filter-reset-btn" id="reset-filters" type="button" title="Reset All Filters">
                            <i class="fas fa-redo" aria-hidden="true"></i>
                            <span>Reset</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- RESULTS HEADER -->
            <div class="results-header">
                <div class="results-header__count">
                    <h2 id="results-count">Loading tours...</h2>
                </div>
            </div>

            <!-- TOUR GRID (HTMX loads here) -->
            <div id="tour-results"
                 hx-get="{{ url('/partials/tours/search?category=' . $category->slug) }}"
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
    </section>

    <!-- =====================================================
         EXPLORE ALL CATEGORIES
         ===================================================== -->
    <section class="related-categories">
        <div class="container">
            <div class="section-header">
                <h2 class="section-header__title">Explore Other Categories</h2>
                <p class="section-header__subtitle">Find more adventures that match your interests</p>
            </div>

            <div id="related-categories"
                 hx-get="{{ url('/partials/categories/related?current=' . $category->slug) }}"
                 hx-trigger="load"
                 hx-swap="innerHTML">
                <!-- Loading skeleton -->
                <div class="related-categories-grid">
                    <div class="skeleton-card skeleton-card--small"></div>
                    <div class="skeleton-card skeleton-card--small"></div>
                    <div class="skeleton-card skeleton-card--small"></div>
                    <div class="skeleton-card skeleton-card--small"></div>
                    <div class="skeleton-card skeleton-card--small"></div>
                    <div class="skeleton-card skeleton-card--small"></div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<!-- HTMX Library -->
<script src="https://unpkg.com/htmx.org@1.9.10"></script>
<!-- Category Landing Page Specific JS -->
<script src="{{ asset('js/category-landing.js') }}?v={{ filemtime(public_path('js/category-landing.js')) }}"></script>
@endpush
