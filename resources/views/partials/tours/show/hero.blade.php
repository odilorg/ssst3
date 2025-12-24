{{-- Tour Hero Partial - Breadcrumbs, Title, Rating --}}
<div class="container">

        <!-- Breadcrumb Navigation -->
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <ol class="breadcrumbs__list">
                <li class="breadcrumbs__item">
                    <a href="/" class="breadcrumbs__link">Home</a>
                    <span class="breadcrumbs__separator" aria-hidden="true">/</span>
                </li>
                <li class="breadcrumbs__item">
                    <a href="/tours" class="breadcrumbs__link">Craft Journeys</a>
                    <span class="breadcrumbs__separator" aria-hidden="true">/</span>
                </li>
                @if($tour->city)
                    <li class="breadcrumbs__item">
                        <a href="/tours?city={{ $tour->city->slug }}" class="breadcrumbs__link">{{ $tour->city->name }}</a>
                        <span class="breadcrumbs__separator" aria-hidden="true">/</span>
                    </li>
                @endif
                <li class="breadcrumbs__item">
                    <span class="breadcrumbs__current" aria-current="page">{{ Str::limit($tour->title, 50) }}</span>
                </li>
            </ol>
        </nav>

        <!-- Tour Title with Actions -->
        <div class="tour-header__title-row">
            <h1 class="tour-title">{{ $tour->title }}</h1>
            <div class="tour-header__actions">
                <button class="btn-icon btn-share" aria-label="Share this tour" title="Share" data-tour-title="{{ $tour->title }}" data-tour-url="{{ url('/tours/' . $tour->slug) }}">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path d="M15 7a2 2 0 100-4 2 2 0 000 4zM5 13a2 2 0 100-4 2 2 0 000 4zM15 17a2 2 0 100-4 2 2 0 000 4zM6.5 11.5l7-3M6.5 11.5l7 3"/>
                    </svg>
                </button>
                <button class="btn-icon btn-favorite" aria-label="Add to favorites" title="Save to wishlist" data-tour-id="{{ $tour->id }}" data-tour-title="{{ $tour->title }}">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path d="M10 17.5l-1.45-1.32C3.4 11.36 0 8.27 0 4.5 0 1.42 2.42 0 5 0c1.74 0 3.41.81 4.5 2.08C10.59.81 12.26 0 14 0c2.58 0 5 1.42 5 4.5 0 3.77-3.4 6.86-8.55 11.68L10 17.5z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Rating and Location -->
        <div class="tour-header__rating">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="#F4B400" aria-hidden="true">
                <path d="M8 0l2.163 5.238 5.837.423-4.462 3.662 1.338 5.677L8 12.236 3.124 15l1.338-5.677L0 5.661l5.837-.423z"/>
            </svg>
            <span class="rating-score">{{ number_format($tour->rating, 1) }}</span>
            <span class="rating-count">({{ $tour->review_count }} {{ Str::plural('review', $tour->review_count) }})</span>
            @if($tour->city)
                <span class="rating-separator">•</span>
                <span class="tour-location">{{ $tour->city->name }}, Uzbekistan</span>
            @endif
        </div>

</div>
