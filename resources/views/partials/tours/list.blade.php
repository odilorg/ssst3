{{--
    Tour List Partial - Production Version

    This partial renders tour cards in two modes:
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
{{--
    OPTION 3: Grid Card with Info Overlay
    Modern, space-efficient, image-heavy
    Aspect ratio: ~1:1.3 (300px × 400px)
--}}

{{--
    OPTION 2: Compact Vertical Card
    Reduced height, better aspect ratio, still familiar vertical pattern
    Aspect ratio: ~1:1.5 (300px × 450px)
--}}

<article class="tour-card-v" data-tour-id="{{ $tour->id }}">
    
    {{-- Image with floating badges --}}
    <div class="tour-card-v__media">
        <img
            src="{{ $tour->featured_image_url ?? asset('images/default-tour.webp') }}"
            alt="{{ $tour->title }}"
            width="400"
            height="300"
            loading="lazy"
            decoding="async"
        >
        
        {{-- Floating badges --}}
        <div class="tour-card-v__badges">
            <span class="tour-card-v__badge">
                <i class="far fa-clock" aria-hidden="true"></i>
                {{ $tour->duration_text ?? $tour->duration_days . ' days' }}
            </span>
            <span class="tour-card-v__badge">
                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                {{ $tour->city->name ?? 'Uzbekistan' }}
            </span>
        </div>
    </div>

    {{-- Content --}}
    <div class="tour-card-v__content">
        {{-- Title --}}
        <h3 class="tour-card-v__title">
            <a href="/tours/{{ $tour->slug }}">
                {{ $tour->title }}
            </a>
        </h3>

        {{-- Description --}}
        <p class="tour-card-v__description">
            {{ Str::limit($tour->meta_description ?? $tour->description, 100) }}
        </p>

        {{-- Footer (Price + CTA) --}}
        <div class="tour-card-v__footer">
            <div class="tour-card-v__price">
                <span class="tour-card-v__price-amount">${{ number_format($tour->price_per_person, 0) }}</span>
            </div>
            <a href="/tours/{{ $tour->slug }}" class="tour-card-v__btn">
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

{{-- Replace Load More button on subsequent loads (Out-of-band swap) --}}
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
