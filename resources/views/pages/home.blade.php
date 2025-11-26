@extends('layouts.main')

@section('title', 'Jahongir Travel - Discover the Magic of Uzbekistan | Silk Road Tours')
@section('meta_description', 'Discover Uzbekistan with Jahongir Travel - Expert guided tours of the ancient Silk Road, featuring Samarkand, Bukhara, Khiva, and more.')
@section('meta_keywords', 'Uzbekistan tours, Silk Road travel, Samarkand tours, Bukhara, Khiva, Central Asia travel')
@section('canonical', url('/'))

@section('structured_data')
{
  "@@context": "https://schema.org",
  "@@type": "TravelAgency",
  "name": "Jahongir Travel",
  "description": "Expert guided tours in Uzbekistan and the Silk Road",
  "url": "{{ url('/') }}",
  "telephone": "+998 91 555 08 08",
  "email": "info@jahongirtravel.com",
  "address": {
    "@@type": "PostalAddress",
    "addressLocality": "Samarkand",
    "addressCountry": "UZ"
  },
  "aggregateRating": {
    "@@type": "AggregateRating",
    "ratingValue": "4.9",
    "reviewCount": "2400"
  }
}
@endsection

@push('styles')
  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

  <!-- Reviews Carousel Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/reviews-carousel.css') }}">
@endpush

@section('content')
  <!-- =====================================================
       HERO SECTION
       ===================================================== -->
  <section class="hero" aria-labelledby="hero-heading">
    <!-- Hero Background Image -->
    <div class="hero__media">
      <picture>
        <source srcset="{{ asset('images/hero-registan.webp') }}" type="image/webp">
        <img src="{{ asset('images/hero-registan.webp') }}"
             alt="Registan Square in Samarkand, Chirokchi 4 at sunset"
             width="1920"
             height="1080"
             sizes="100vw"
             fetchpriority="high">
      </picture>
    </div>

    <!-- Hero Content -->
    <div class="hero__content">
      <div class="container container--wide">
        <div class="hero__text">
          <h1 id="hero-heading" class="hero__title">Discover the Magic of Uzbekistan</h1>
          <p class="hero__sub">Experience the ancient Silk Road with expert local guides</p>

          <div class="hero__cta">
            <a href="{{ url('/tours') }}" class="btn btn--accent btn--large btn--pill">
              <i class="fas fa-map-marked-alt" aria-hidden="true"></i>
              Choose a Destination
            </a>
          </div>
        </div>

        <!-- Hero Badges -->
        <div class="hero__badges">
          <div class="badge">
            <div class="badge__icon">
              <i class="fas fa-users" aria-hidden="true"></i>
            </div>
            <div class="badge__content">
              <strong class="badge__title">Family-Run</strong>
              <p class="badge__text">Samarkand-based team with true local expertise.</p>
            </div>
          </div>

          <div class="badge">
            <div class="badge__icon">
              <i class="fas fa-gem" aria-hidden="true"></i>
            </div>
            <div class="badge__content">
              <strong class="badge__title">Authentic Experiences</strong>
              <p class="badge__text">Artisans, workshops & hidden gems beyond the usual spots.</p>
            </div>
          </div>

          <div class="badge">
            <div class="badge__icon">
              <i class="fas fa-leaf" aria-hidden="true"></i>
            </div>
            <div class="badge__content">
              <strong class="badge__title">Eco-Friendly</strong>
              <p class="badge__text">Travel that respects nature and supports local life.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Gradient overlay for readability -->
    <div class="hero__overlay" aria-hidden="true"></div>
  </section>

  <!-- Section 2: Why We're Your Perfect Travel Partner -->
  <section class="why-us" id="why-us">
    <div class="container">
      <div class="why-us__grid">

        <!-- Left Column: Content -->
        <div class="why-us__intro">
          <span class="eyebrow">WHY CHOOSE US</span>
          <h2>Trusted Local Experts in Uzbekistan</h2>
          <p>For over a decade, Jahongir Travel has been guiding guests beyond postcards — into the living heart of Uzbekistan's culture, cuisine, and craftsmanship.</p>
          <p class="micro-proof"><i class="fas fa-check-circle" aria-hidden="true"></i> Loved by travelers from all over the world</p>

          <div class="why-us__contacts">
            <a href="mailto:info@@jahongirtravel.com" class="contact-link" aria-label="Email Jahongir Travel">
              <i class="fas fa-envelope" aria-hidden="true"></i> Email Us
            </a>
            <a href="https://wa.me/998915550808" class="contact-link" target="_blank" rel="noopener noreferrer" aria-label="Chat with Jahongir Travel on WhatsApp">
              <i class="fab fa-whatsapp" aria-hidden="true"></i> WhatsApp
            </a>
          </div>

          <div class="why-us__cta-wrapper">
            <a href="{{ url('/tours') }}" class="btn btn--primary btn--large">
              <i class="fas fa-route" aria-hidden="true"></i>
              Plan My Trip
            </a>
          </div>

          <div class="why-us__divider"></div>
          <ul class="benefits">
            <li>
              <i class="benefits__icon fas fa-map-marked-alt" aria-hidden="true"></i>
              <div class="benefits__content">
                <strong>Local Expertise</strong>
                <p>Born and raised in Samarkand, we know every hidden corner and local secret.</p>
              </div>
            </li>
            <li>
              <i class="benefits__icon fas fa-route" aria-hidden="true"></i>
              <div class="benefits__content">
                <strong>Personalized Itineraries</strong>
                <p>Every tour is customized to your interests, pace, and travel style.</p>
              </div>
            </li>
            <li>
              <i class="benefits__icon fas fa-headset" aria-hidden="true"></i>
              <div class="benefits__content">
                <strong>24/7 Multilingual Support</strong>
                <p>Round-the-clock assistance in English, French, Russian, and Uzbek throughout your journey.</p>
              </div>
            </li>
          </ul>
        </div>

        <!-- Right Column: Photos -->
        <div class="why-us__media">
          <div class="why-us__photo">
            <img src="{{ asset('images/guide-tourists.webp') }}"
                 alt="Jahongir Travel guide with tourists at Registan Square"
                 width="400"
                 height="300"
                 loading="lazy"
                 decoding="async">
          </div>
          <div class="why-us__photo">
            <img src="{{ asset('images/craft-pottery.webp') }}"
                 alt="Local craftsman demonstrating traditional pottery making"
                 width="400"
                 height="300"
                 loading="lazy"
                 decoding="async">
          </div>
          <div class="why-us__photo">
            <img src="{{ asset('images/uzbek-cuisine.webp') }}"
                 alt="Travelers enjoying authentic Uzbek cuisine"
                 width="400"
                 height="300"
                 loading="lazy"
                 decoding="async">
          </div>
          <div class="why-us__badge">
            <div class="trust-badge">
              <span class="mini-label">Traveler Rating</span>
              <i class="fas fa-star" aria-hidden="true"></i>
              <strong>4.8</strong>
              <span>Hundreds of Happy Travelers</span>
            </div>
          </div>

          <!-- Statistics Cards -->
          <div class="stat-card">
            <i class="fas fa-calendar-check" aria-hidden="true"></i>
            <span>Guiding Travelers Since 2012</span>
          </div>
          <div class="stat-card">
            <i class="fas fa-map-marked" aria-hidden="true"></i>
            <span>Authentic Routes &amp; Workshops</span>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Section 3: Trending Activities -->
  <section class="activities" id="activities">
    <!-- JSON-LD Schema (Dynamic) -->
    @if(!empty($categories) && count($categories) > 0)
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "ItemList",
      "name": "Trending Activities in Uzbekistan",
      "itemListElement": [
        @foreach($categories as $index => $category)
        {
          "@@type": "ListItem",
          "position": {{ $index + 1 }},
          "name": "{{ $category->translated_name }}",
          "url": "{{ url('/tours/category/' . $category->slug) }}"
        }{{ $loop->last ? '' : ',' }}
        @endforeach
      ]
    }
    </script>
    @endif

    <div class="container">

      <!-- Section Header -->
      <div class="section-header">
        <p class="section-eyebrow">WHAT MOVES YOU</p>
        <h2 class="section-header__title">Trending Activities in Uzbekistan</h2>
        <p class="section-header__subtitle">Choose your style of travel — from ancient history to mountain adventures</p>
      </div>

      <!-- Activity Cards Grid (DYNAMIC) -->
      <div class="activities__grid">
        @if(!empty($categories) && count($categories) > 0)
        @foreach($categories as $category)
          @php
            $tourCount = $category->cached_tour_count;
            $tourText = $tourCount === 1 ? 'tour' : 'tours';

            // Image priority: 1) Category image, 2) Featured tour hero image, 3) Placeholder
            $imageUrl = null;
            if ($category->image_path) {
                $imageUrl = asset('storage/' . $category->image_path);
            } else {
                // Get a featured tour from this category to use its hero image
                $featuredTour = $category->tours()
                    ->where('is_active', true)
                    ->whereNotNull('hero_image')
                    ->first();

                if ($featuredTour && $featuredTour->hero_image) {
                    $imageUrl = asset('storage/' . $featuredTour->hero_image);
                } else {
                    $imageUrl = 'https://placehold.co/400x300/0D4C92/FFFFFF?text=' . urlencode($category->translated_name);
                }
            }
          @endphp

          <!-- Card: {{ $category->translated_name }} -->
          <a href="{{ url('/tours/category/' . $category->slug) }}" class="activity-card" aria-label="Explore {{ $category->translated_name }} tours">
            <div class="activity-card__media">
              <img src="{{ $imageUrl }}"
                   alt="{{ $category->translated_description }}"
                   width="400"
                   height="300"
                   loading="lazy"
                   decoding="async">
              <div class="activity-card__overlay"></div>
              <span class="activity-card__badge">{{ $tourCount }} {{ $tourText }}</span>
            </div>
            <div class="activity-card__content">
              <h3 class="activity-card__title">{{ $category->translated_name }}</h3>
              <p class="activity-card__description">{{ $category->translated_description }}</p>
              <span class="activity-card__link">Explore Tours <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
            </div>
          </a>
        @endforeach
        @else
          <div class="empty-state">
            <p class="empty-state__message">No activities available at the moment. Please check back later.</p>
          </div>
        @endif
      </div>

      <!-- View All Tours Link -->
      <div class="activities__cta">
        <a href="{{ url('/tours') }}" class="btn btn--primary btn--large" aria-label="View all Uzbekistan tours">
          <i class="fas fa-compass" aria-hidden="true"></i>
          See All Activities
        </a>
      </div>

    </div>
  </section>

  <!-- ========================================
       SECTION 4: EXPLORE POPULAR TOURS
  ========================================= -->
  <section class="tours" id="tours">

    <!-- JSON-LD Schema: TouristTrip for Featured Tours (Dynamic) -->
    @if(!empty($featuredTours) && count($featuredTours) > 0)
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "ItemList",
      "name": "Popular Uzbekistan Tours",
      "itemListElement": [
        @foreach($featuredTours as $index => $tour)
        {
          "@@type": "TouristTrip",
          "position": {{ $index + 1 }},
          "name": "{{ $tour->title }}",
          "description": "{{ strip_tags($tour->short_description ?? $tour->long_description ?? '') }}",
          "touristType": "{{ $tour->categories->first()?->translated_name ?? 'Tour' }}"{{ $tour->city ? ',' : '' }}
          @if($tour->city)
          "itinerary": {
            "@@type": "ItemList",
            "itemListElement": [
              {"@@type": "City", "name": "{{ $tour->city->name }}"}
            ]
          },
          @endif
          "offers": {
            "@@type": "Offer",
            "url": "{{ url('/tours/' . $tour->slug) }}",
            "price": "{{ $tour->price_per_person }}",
            "priceCurrency": "{{ $tour->currency ?? 'USD' }}",
            "availability": "{{ $tour->is_active ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}"
          },
          "provider": {
            "@@type": "TravelAgency",
            "name": "Jahongir Travel"
          }{{ ($tour->rating && $tour->review_count) ? ',' : '' }}
          @if($tour->rating && $tour->review_count)
          "aggregateRating": {
            "@@type": "AggregateRating",
            "ratingValue": "{{ $tour->rating }}",
            "reviewCount": "{{ $tour->review_count }}"
          }
          @endif
        }{{ $loop->last ? '' : ',' }}
        @endforeach
      ]
    }
    </script>
    @endif

    <div class="container--wide">

      <!-- Section Header -->
      <div class="tours__header">
        <p class="section-eyebrow">Featured Adventures</p>
        <h2 class="section-title">Explore Popular Uzbekistan Tours</h2>
        <p class="section-subtitle">
          Handcrafted journeys through the heart of the Silk Road
        </p>
      </div>


      <!-- Tours Grid (HTMX Dynamic Loading) -->
      <div class="tours__grid"
           hx-get="{{ url('/partials/tours?per_page=6') }}"
           hx-trigger="load once"
           hx-select="article.tour-card"
           hx-swap="innerHTML">

        <!-- Loading Skeleton (will be replaced by HTMX) -->
        <div class="skeleton-card"></div>
        <div class="skeleton-card"></div>
        <div class="skeleton-card"></div>
        <div class="skeleton-card"></div>
        <div class="skeleton-card"></div>
        <div class="skeleton-card"></div>

      </div>

      <!-- View All CTA -->
      <div class="tours__cta">
        <a href="{{ url('/tours') }}" class="btn btn--accent btn--large" aria-label="Find your perfect Uzbekistan tour">
          <i class="fas fa-map-marked-alt" aria-hidden="true"></i>
          Find Your Perfect Journey
        </a>
      </div>

    </div>
  </section>

  <!-- ========================================
       SECTION 5: TOP PLACES TO TRAVEL
  ========================================= -->
  <section class="places" id="places">

    <!-- JSON-LD Schema: Place for Top Destinations -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "ItemList",
      "name": "Top Places to Travel in Uzbekistan",
      "itemListElement": [
        {
          "@@type": "Place",
          "position": 1,
          "name": "Samarkand",
          "description": "The Jewel of the Silk Road. Historic city famous for Registan Square, Shah-i-Zinda necropolis, and Gur-e-Amir mausoleum.",
          "geo": {
            "@@type": "GeoCoordinates",
            "latitude": "39.6542",
            "longitude": "66.9597"
          },
          "address": {
            "@@type": "PostalAddress",
            "addressLocality": "Samarkand",
            "addressCountry": "UZ"
          },
          "url": "{{ url('/destinations/samarkand') }}",
          "touristType": "Cultural & Historical"
        },
        {
          "@@type": "Place",
          "position": 2,
          "name": "Bukhara",
          "description": "Living Museum of Central Asia. Ancient city with over 140 architectural monuments and a UNESCO World Heritage old town.",
          "geo": {
            "@@type": "GeoCoordinates",
            "latitude": "39.7747",
            "longitude": "64.4286"
          },
          "address": {
            "@@type": "PostalAddress",
            "addressLocality": "Bukhara",
            "addressCountry": "UZ"
          },
          "url": "{{ url('/destinations/bukhara') }}",
          "touristType": "Cultural & Historical"
        },
        {
          "@@type": "Place",
          "position": 3,
          "name": "Khiva",
          "description": "Ancient Desert Fortress. UNESCO-protected Itchan Kala fortress city with perfectly preserved Silk Road architecture.",
          "geo": {
            "@@type": "GeoCoordinates",
            "latitude": "41.3775",
            "longitude": "60.3642"
          },
          "address": {
            "@@type": "PostalAddress",
            "addressLocality": "Khiva",
            "addressCountry": "UZ"
          },
          "url": "{{ url('/destinations/khiva') }}",
          "touristType": "Cultural & Historical"
        },
        {
          "@@type": "Place",
          "position": 4,
          "name": "Tashkent",
          "description": "Modern Heart of Uzbekistan. Capital city blending Soviet architecture, vibrant bazaars, and contemporary culture.",
          "geo": {
            "@@type": "GeoCoordinates",
            "latitude": "41.2995",
            "longitude": "69.2401"
          },
          "address": {
            "@@type": "PostalAddress",
            "addressLocality": "Tashkent",
            "addressCountry": "UZ"
          },
          "url": "{{ url('/destinations/tashkent') }}",
          "touristType": "Urban & Modern"
        }
      ]
    }
    </script>

    <div class="container--wide">

      <!-- Section Header -->
      <div class="places__header">
        <p class="section-eyebrow">Discover Uzbekistan</p>
        <h2 class="section-title">Top Places to Travel</h2>
        <p class="section-subtitle">
          Explore the legendary cities of the ancient Silk Road
        </p>
      </div>

      <!-- Places Grid (DYNAMIC) -->
      <div class="places__grid">
        @if(!empty($cities) && count($cities) > 0)
        @foreach($cities as $city)
          @php
            $tourCount = $city->tour_count;
            $tourText = $tourCount === 1 ? 'tour' : 'tours';

            // Get city image - use hero_image or placeholder
            $imageUrl = $city->featured_image_url ?? 'https://placehold.co/400x533/0D4C92/FFFFFF?text=' . urlencode($city->name);

            // Get tagline or use default
            $tagline = $city->tagline ?? '';

            // Get city name
            $cityName = htmlspecialchars($city->name);
            $citySlug = $city->slug;

            // Short description for alt text
            $altText = $city->short_description
                ? htmlspecialchars(strip_tags($city->short_description))
                : "{$cityName}, Uzbekistan";
          @endphp

          <!-- City Card: {{ $cityName }} -->
          <article class="city-card">
            <a href="{{ url('/destinations/' . $citySlug) }}" class="city-card__link" aria-label="Discover {{ $cityName }}">
              <div class="city-card__media">
                @if($city->has_webp && $city->hero_image_webp_srcset)
                  {{-- Serve WebP with responsive sizes --}}
                  <picture>
                    <source
                      type="image/webp"
                      srcset="{{ $city->hero_image_webp_srcset }}"
                      sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 400px">
                    <img
                      src="{{ $imageUrl }}"
                      alt="{{ $altText }}"
                      width="400"
                      height="533"
                      loading="lazy"
                      decoding="async">
                  </picture>
                @else
                  {{-- Fallback to original image --}}
                  <img
                    src="{{ $imageUrl }}"
                    alt="{{ $altText }}"
                    width="400"
                    height="533"
                    loading="lazy"
                    decoding="async">
                @endif
              </div>
              <div class="city-card__content">
                <h3 class="city-card__title">{{ $cityName }}</h3>
                <p class="city-card__tagline">{{ $tagline }}</p>
                <p class="city-card__stats">
                  <i class="fas fa-route" aria-hidden="true"></i>
                  {{ $tourCount }} {{ $tourText }} available
                </p>
                <span class="city-card__cta">
                  Discover {{ $cityName }}
                  <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </span>
              </div>
            </a>
          </article>
        @endforeach
        @else
          <div class="empty-state">
            <p class="empty-state__message">No destinations available at the moment. Please check back later.</p>
          </div>
        @endif
      </div>

      <!-- View All Destinations CTA -->
      <div class="places__cta">
        <a href="{{ url('/destinations') }}" class="btn btn--primary btn--large" aria-label="Explore all destinations in Uzbekistan">
          <i class="fas fa-map-marked" aria-hidden="true"></i>
          Explore Destinations
        </a>
      </div>

    </div>
  </section>

  <!-- ========================================
       SECTION 6: TRAVELLER REVIEWS
  ========================================= -->
  <section class="reviews" id="reviews">
    <div class="container">

      <!-- Section Header -->
      <div class="reviews__header">
        <p class="section-eyebrow">What Travelers Say</p>
        <h2 class="section-title">Traveller Reviews</h2>
        <p class="section-subtitle">
          Real experiences from travelers who explored Uzbekistan with us
        </p>
      </div>

      <!-- Reviews Carousel (DYNAMIC) -->
      <div class="swiper reviews-swiper">
        <div class="swiper-wrapper">
          @if(!empty($reviews) && count($reviews) > 0)
          @foreach($reviews as $review)
            @php
              $avatarUrl = $review->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($review->reviewer_name) . '&size=60&background=0D4C92&color=fff';
              $location = $review->reviewer_location ?? 'Travel Enthusiast';
              $date = $review->created_at->format('F Y');
              $sourceIcon = $review->source === 'tripadvisor' ? 'fab fa-tripadvisor' : 'fas fa-star';
              $sourceName = ucfirst($review->source ?? 'Website');
              $reviewerName = $review->reviewer_name;
              $reviewTitle = $review->title;
              $reviewContent = $review->content;
            @endphp

            <!-- Review Slide: {{ $reviewerName }} -->
            <div class="swiper-slide">
              <article class="review-card">
                <div class="review-card__header">
                  <img src="{{ $avatarUrl }}" alt="{{ $reviewerName }}" class="review-card__avatar" width="60" height="60" loading="lazy">
                  <div class="review-card__author">
                    <h3 class="review-card__name">{{ $reviewerName }}</h3>
                    <p class="review-card__location"><i class="fas fa-map-marker-alt"></i> {{ $location }}</p>
                  </div>
                </div>
                <div class="review-card__rating">
                  <div class="stars" aria-label="Rated 5 out of 5 stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                  </div>
                  <p class="review-card__date">Traveled in {{ $date }}</p>
                </div>
                <div class="review-card__content">
                  <i class="fas fa-quote-left review-card__quote"></i>
                  <h4 class="review-card__title">{{ $reviewTitle }}</h4>
                  <p class="review-card__text">{{ $reviewContent }}</p>
                  <p class="review-card__source"><i class="{{ $sourceIcon }}"></i> Reviewed on {{ $sourceName }}</p>
                </div>
              </article>
            </div>
          @endforeach
          @else
            <div class="swiper-slide">
              <div class="empty-state">
                <p class="empty-state__message">No reviews available at the moment. Please check back later.</p>
              </div>
            </div>
          @endif
        </div>

        <!-- Swiper Navigation -->
        <div class="swiper-button-prev reviews-swiper-prev"></div>
        <div class="swiper-button-next reviews-swiper-next"></div>

        <!-- Swiper Pagination -->
        <div class="swiper-pagination reviews-swiper-pagination"></div>
      </div>


    </div>
  </section>

  <!-- =====================================================
       SECTION 7: TRAVEL INSIGHTS & BLOG PREVIEW
       ===================================================== -->
  <section class="blog-preview" id="blog" aria-labelledby="blog-title">

    <!-- JSON-LD Structured Data for SEO (Dynamic) -->
    @if(!empty($blogPosts) && count($blogPosts) > 0)
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "ItemList",
      "name": "Jahongir Travel Blog Articles",
      "description": "Travel insights, tips, and guides for visiting Uzbekistan",
      "itemListElement": [
        @foreach($blogPosts as $index => $post)
        {
          "@@type": "BlogPosting",
          "position": {{ $index + 1 }},
          "headline": "{{ $post->title }}",
          "image": "{{ $post->featured_image ? asset('storage/' . $post->featured_image) : asset('images/default-blog.svg') }}",
          "datePublished": "{{ $post->published_at ? $post->published_at->toIso8601String() : '' }}",
          "dateModified": "{{ $post->updated_at ? $post->updated_at->toIso8601String() : '' }}",
          "author": {
            "@@type": "Organization",
            "name": "Jahongir Travel"
          },
          "publisher": {
            "@@type": "Organization",
            "name": "Jahongir Travel",
            "logo": {
              "@@type": "ImageObject",
              "url": "{{ asset('images/logo.png') }}"
            }
          },
          "mainEntityOfPage": {
            "@@type": "WebPage",
            "@id": "{{ url('/blog/' . $post->slug) }}"
          },
          "description": "{{ strip_tags($post->excerpt ?? Str::limit($post->content, 200)) }}",
          "articleSection": "{{ $post->category->name ?? 'Travel Tips' }}"{{ $post->reading_time ? ',' : '' }}
          @if($post->reading_time)
          "wordCount": {{ $post->reading_time * 200 }},
          @endif
          "inLanguage": "en-US",
          "url": "{{ url('/blog/' . $post->slug) }}"
        }{{ $loop->last ? '' : ',' }}
        @endforeach
      ]
    }
    </script>
    @endif

    <div class="container">

      <!-- Section Header -->
      <header class="section-header text-center">
        <p class="section-eyebrow">FROM OUR EXPERTS</p>
        <h2 id="blog-title" class="section-title">Travel Insights & Tips</h2>
        <p class="section-subtitle">Insider knowledge to make your Silk Road journey unforgettable</p>
      </header>

      <!-- Blog Grid (DYNAMIC) -->
        @if(!empty($blogPosts) && count($blogPosts) > 0)
      <div class="blog-grid">
        @foreach($blogPosts as $post)
          @php
            $featuredImage = $post->featured_image
                ? asset('storage/' . $post->featured_image)
                : asset('images/default-blog.svg');

            $categoryName = $post->category?->name ?? 'Uncategorized';
            $publishedDate = $post->published_at->format('M d, Y');
            $publishedDatetime = $post->published_at->format('Y-m-d');
            $readingTime = $post->reading_time ?? 5;
          @endphp

          <!-- Blog Card: {{ $post->title }} -->
          <article class="blog-card">
            <a href="{{ url('/blog/' . $post->slug) }}" class="blog-card__link">
              <div class="blog-card__media">
                @if($post->featured_image_webp && $post->image_processing_status === 'completed')
                  {{-- Serve WebP with fallback --}}
                  <picture>
                    <source
                      type="image/webp"
                      srcset="{{ asset('storage/' . $post->featured_image_webp) }}">
                    <img
                      src="{{ $featuredImage }}"
                      alt="{{ $post->title }}"
                      width="800"
                      height="450"
                      loading="lazy"
                      fetchpriority="low"
                      decoding="async">
                  </picture>
                @else
                  {{-- Fallback to original image --}}
                  <img
                    src="{{ $featuredImage }}"
                    alt="{{ $post->title }}"
                    width="800"
                    height="450"
                    loading="lazy"
                    fetchpriority="low"
                    decoding="async">
                @endif
                <span class="blog-card__category" data-category="{{ $post->category?->slug }}">{{ $categoryName }}</span>
              </div>
              <div class="blog-card__content">
                <h3 class="blog-card__title">{{ $post->title }}</h3>
                <p class="blog-card__excerpt">
                  {{ $post->excerpt }}
                </p>
                <div class="blog-card__meta">
                  <time class="blog-card__date" datetime="{{ $publishedDatetime }}">{{ $publishedDate }}</time>
                  <span class="blog-card__reading-time" aria-label="Reading time">
                    <i class="far fa-clock" aria-hidden="true"></i> {{ $readingTime }} min read
                  </span>
                </div>
              </div>
            </a>
          </article>
        @endforeach
      </div>
        @else
          <div class="empty-state">
            <p class="empty-state__message">No blog posts available at the moment. Please check back later.</p>
          </div>
        @endif

      <!-- Section Footer CTA -->
      <div class="blog-preview__footer">
        <a href="{{ url('/blog') }}" class="btn btn--large btn--ghost" aria-label="View all travel articles and guides">
          View All Articles
          <i class="fas fa-arrow-right" aria-hidden="true"></i>
        </a>
      </div>

    </div>
  </section>
@endsection

@push('scripts')
  <!-- HTMX Library -->
  <script src="https://unpkg.com/htmx.org@1.9.10"></script>

  <!-- Swiper JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>


  <!-- Reviews Carousel -->
  <script src="{{ asset('js/reviews-carousel.js') }}"></script>
@endpush
