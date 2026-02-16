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
  "description": "{{ $city->short_description ?? 'Explore ' . $city->name . ' with Jahongir Travel' }}",
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
<link rel="stylesheet" href="{{ asset('css/tours-listing.css') }}">
<style>
    /* ========================================
       City Guide Specific Styles
       ======================================== */

    .city-overview {
        padding: 80px 0;
        background: #fff;
    }

    .city-overview__layout {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 48px;
        align-items: start;
    }

    .city-overview__text h2 {
        font-family: 'Playfair Display', serif;
        font-size: 36px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 24px;
    }

    .city-overview__description {
        font-size: 17px;
        line-height: 1.8;
        color: #444;
    }

    .city-overview__description p {
        margin-bottom: 16px;
    }

    /* Quick Facts Sidebar */
    .quick-facts {
        background: #f8f9fa;
        border-radius: 16px;
        padding: 32px;
        border: 1px solid #e5e5e5;
        position: sticky;
        top: 100px;
    }

    .quick-facts__title {
        font-size: 20px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #e5e5e5;
    }

    .quick-facts__item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .quick-facts__item:last-child {
        border-bottom: none;
    }

    .quick-facts__icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f7ff;
        border-radius: 10px;
        flex-shrink: 0;
    }

    .quick-facts__icon i {
        font-size: 16px;
        color: #1a5490;
    }

    .quick-facts__info {
        flex: 1;
    }

    .quick-facts__label {
        font-size: 12px;
        font-weight: 600;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }

    .quick-facts__value {
        font-size: 15px;
        font-weight: 600;
        color: #1a1a1a;
    }

    /* Top Sights Section */
    .top-sights {
        padding: 80px 0;
        background: #f8f9fa;
    }

    .top-sights__grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-top: 48px;
    }

    .sight-card {
        background: white;
        border-radius: 16px;
        padding: 32px 24px;
        text-align: center;
        border: 1px solid #e5e5e5;
        transition: all 0.3s ease;
    }

    .sight-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        border-color: #1a5490;
    }

    .sight-card__icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f7ff;
        border-radius: 50%;
    }

    .sight-card__icon i {
        font-size: 28px;
        color: #1a5490;
    }

    .sight-card__name {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 8px;
    }

    .sight-card__description {
        font-size: 14px;
        color: #666;
        line-height: 1.6;
    }

    /* Featured Tours Section */
    .featured-tours {
        padding: 80px 0;
        background: #fff;
    }

    .featured-tours__grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-top: 48px;
    }

    .featured-tours__cta {
        text-align: center;
        margin-top: 48px;
    }

    /* CTA Button */
    .btn--cta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 32px;
        background: #1a5490;
        color: white;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn--cta:hover {
        background: #143f6e;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(26, 84, 144, 0.3);
        color: white;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .city-overview__layout {
            grid-template-columns: 1fr;
            gap: 32px;
        }

        .quick-facts {
            position: static;
        }

        .top-sights__grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .featured-tours__grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .city-overview {
            padding: 60px 0;
        }

        .city-overview__text h2 {
            font-size: 28px;
        }

        .top-sights {
            padding: 60px 0;
        }

        .top-sights__grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .sight-card {
            display: flex;
            text-align: left;
            gap: 20px;
            padding: 20px;
        }

        .sight-card__icon {
            margin: 0;
            flex-shrink: 0;
        }

        .featured-tours {
            padding: 60px 0;
        }

        .featured-tours__grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <!-- =====================================================
         HERO SECTION
         ===================================================== -->
    <section class="category-hero" id="category-hero" style="background-image: url('{{ $ogImage }}');">
        <div class="category-hero__overlay"></div>
        <div class="container">
            <div class="category-hero__content">

                <!-- Breadcrumb -->
                <nav class="breadcrumb" aria-label="Breadcrumb">
                    <a href="/">Home</a>
                    <span class="breadcrumb__separator">/</span>
                    <a href="/destinations">Destinations</a>
                    <span class="breadcrumb__separator">/</span>
                    <span>{{ $city->name }}</span>
                </nav>

                <!-- City Icon -->
                <div class="category-hero__icon" id="category-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>

                <!-- City Name -->
                <h1 class="category-hero__title">{{ $city->name }}</h1>

                <!-- Tagline -->
                @if($city->tagline)
                <p class="category-hero__description">{{ $city->tagline }}</p>
                @else
                <p class="category-hero__description">Discover the beauty and heritage of {{ $city->name }}</p>
                @endif

                <!-- Travel Guide Badge -->
                <div class="category-hero__badge">
                    <i class="fas fa-book-open"></i>
                    <span>Travel Guide</span>
                </div>
            </div>
        </div>
    </section>

    <!-- =====================================================
         CITY OVERVIEW + QUICK FACTS
         ===================================================== -->
    <section class="city-overview" id="main-content">
        <div class="container">
            <div class="city-overview__layout">

                <!-- Main Content -->
                <div class="city-overview__text">
                    <h2>About {{ $city->name }}</h2>
                    <div class="city-overview__description">
                        @if($city->long_description)
                            {!! is_string($city->long_description) && str_starts_with(trim($city->long_description), '{')
                                ? (json_decode($city->long_description, true)['en'] ?? $city->long_description)
                                : $city->long_description !!}
                        @elseif($city->short_description)
                            <p>{{ $city->short_description }}</p>
                        @else
                            <p>{{ $city->name }} is one of Uzbekistan's fascinating destinations, offering visitors a unique blend of history, culture, and natural beauty. Whether you're drawn to ancient architecture, vibrant bazaars, or stunning landscapes, {{ $city->name }} has something special to offer every traveler.</p>
                        @endif
                    </div>
                </div>

                <!-- Quick Facts Sidebar -->
                <aside class="quick-facts">
                    <h3 class="quick-facts__title">Quick Facts</h3>

                    <div class="quick-facts__item">
                        <div class="quick-facts__icon">
                            <i class="fas fa-globe-asia"></i>
                        </div>
                        <div class="quick-facts__info">
                            <div class="quick-facts__label">Country</div>
                            <div class="quick-facts__value">{{ $city->country ?? 'Uzbekistan' }}</div>
                        </div>
                    </div>

                    <div class="quick-facts__item">
                        <div class="quick-facts__icon">
                            <i class="fas fa-sun"></i>
                        </div>
                        <div class="quick-facts__info">
                            <div class="quick-facts__label">Best Time to Visit</div>
                            <div class="quick-facts__value">April - June, September - October</div>
                        </div>
                    </div>

                    <div class="quick-facts__item">
                        <div class="quick-facts__icon">
                            <i class="fas fa-language"></i>
                        </div>
                        <div class="quick-facts__info">
                            <div class="quick-facts__label">Language</div>
                            <div class="quick-facts__value">Uzbek, Russian</div>
                        </div>
                    </div>

                    <div class="quick-facts__item">
                        <div class="quick-facts__icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="quick-facts__info">
                            <div class="quick-facts__label">Currency</div>
                            <div class="quick-facts__value">Uzbekistani Som (UZS)</div>
                        </div>
                    </div>

                    @if($city->latitude && $city->longitude)
                    <div class="quick-facts__item">
                        <div class="quick-facts__icon">
                            <i class="fas fa-map-pin"></i>
                        </div>
                        <div class="quick-facts__info">
                            <div class="quick-facts__label">Coordinates</div>
                            <div class="quick-facts__value">{{ number_format($city->latitude, 2) }}°N, {{ number_format($city->longitude, 2) }}°E</div>
                        </div>
                    </div>
                    @endif
                </aside>

            </div>
        </div>
    </section>

    <!-- =====================================================
         TOP SIGHTS (from monuments)
         ===================================================== -->
    @if($topSights->isNotEmpty())
    <section class="top-sights">
        <div class="container">
            <div class="section-header">
                <h2 class="section-header__title">Top Sights in {{ $city->name }}</h2>
                <p class="section-header__subtitle">Must-visit landmarks and attractions</p>
            </div>

            <div class="top-sights__grid">
                @foreach($topSights as $sight)
                <div class="sight-card">
                    <div class="sight-card__icon">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <div>
                        <h3 class="sight-card__name">{{ $sight->name }}</h3>
                        @if($sight->description)
                        <p class="sight-card__description">{{ Str::limit(strip_tags($sight->description), 120) }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- =====================================================
         FEATURED TOURS
         ===================================================== -->
    @if($featuredTours->isNotEmpty())
    <section class="featured-tours">
        <div class="container">
            <div class="section-header">
                <h2 class="section-header__title">Tours Featuring {{ $city->name }}</h2>
                <p class="section-header__subtitle">Explore our curated tours that include this destination</p>
            </div>

            <div class="featured-tours__grid">
                @foreach($featuredTours as $tour)
                    @php $tr = $tour->translationOrDefault(); @endphp
                    <article class="tour-card-o">
                        <img
                            src="{{ $tour->featured_image_url ?? asset('images/default-tour.webp') }}"
                            alt="{{ $tr->title ?? $tour->title }}"
                            class="tour-card-o__bg"
                            width="400"
                            height="500"
                            loading="lazy"
                            decoding="async"
                        >
                        <div class="tour-card-o__overlay"></div>
                        <div class="tour-card-o__content">
                            <div class="tour-card-o__top">
                                <span class="tour-card-o__badge">
                                    <i class="far fa-clock" aria-hidden="true"></i>
                                    {{ $tour->duration_text ?? $tour->duration_days . ' days' }}
                                </span>
                                <span class="tour-card-o__badge">
                                    <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                    {{ $tour->city->name ?? 'Uzbekistan' }}
                                </span>
                            </div>
                            <div class="tour-card-o__bottom">
                                <h3 class="tour-card-o__title">
                                    <a href="/tours/{{ $tr->slug ?? $tour->slug }}">
                                        {{ $tr->title ?? $tour->title }}
                                    </a>
                                </h3>
                                <p class="tour-card-o__description">
                                    {{ Str::limit($tr->excerpt ?? $tour->meta_description ?? $tour->description, 90) }}
                                </p>
                                <div class="tour-card-o__footer">
                                    <div class="tour-card-o__price">
                                        <span class="tour-card-o__price-amount">${{ number_format($tour->price_per_person, 0) }}</span>
                                    </div>
                                    <a href="/tours/{{ $tr->slug ?? $tour->slug }}" class="tour-card-o__btn">
                                        View Tour
                                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="featured-tours__cta">
                <a href="/tours" class="btn--cta">
                    View All Tours
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- =====================================================
         RELATED DESTINATIONS
         ===================================================== -->
    @if($relatedCities->isNotEmpty())
    <section class="related-categories">
        <div class="container">
            <div class="section-header">
                <h2 class="section-header__title">Explore Other Destinations</h2>
                <p class="section-header__subtitle">Discover more cities and regions in Uzbekistan</p>
            </div>

            @include('partials.cities.related-cards', ['cities' => $relatedCities])
        </div>
    </section>
    @endif
@endsection
