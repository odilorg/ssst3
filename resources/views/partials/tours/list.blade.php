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
