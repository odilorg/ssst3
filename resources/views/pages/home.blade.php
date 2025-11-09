@extends('layouts.main')

@section('title', 'Jahongir Travel - Discover the Magic of Uzbekistan | Silk Road Tours')
@section('meta_description', 'Discover Uzbekistan with Jahongir Travel - Expert guided tours of the ancient Silk Road, featuring Samarkand, Bukhara, Khiva, and more.')
@section('meta_keywords', 'Uzbekistan tours, Silk Road travel, Samarkand tours, Bukhara, Khiva, Central Asia travel')
@section('canonical', 'https://jahongirtravel.com/')

@section('structured_data')
{
  "@context": "https://schema.org",
  "@type": "TravelAgency",
  "name": "Jahongir Travel",
  "description": "Expert guided tours in Uzbekistan and the Silk Road",
  "url": "https://jahongirtravel.com",
  "telephone": "+998 99 123 4567",
  "email": "info@@jahongirtravel.com",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Samarkand",
    "addressCountry": "UZ"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
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
             alt="Registan Square in Samarkand, Uzbekistan at sunset"
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
              <i class="fas fa-shield-alt" aria-hidden="true"></i>
            </div>
            <div class="badge__content">
              <strong class="badge__title">Trusted</strong>
              <p class="badge__text">10 years trusted, 100% money-protected.</p>
            </div>
          </div>

          <div class="badge">
            <div class="badge__icon">
              <i class="fas fa-globe" aria-hidden="true"></i>
            </div>
            <div class="badge__content">
              <strong class="badge__title">Worldwide</strong>
              <p class="badge__text">150+ tours across Uzbekistan & Central Asia.</p>
            </div>
          </div>

          <div class="badge">
            <div class="badge__icon">
              <i class="fas fa-leaf" aria-hidden="true"></i>
            </div>
            <div class="badge__content">
              <strong class="badge__title">Sustainable</strong>
              <p class="badge__text">100% carbon offset. We care for nature and communities.</p>
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
          <h2>Why We're Your Perfect Travel Partner</h2>
          <p>For over a decade, Jahongir Travel has been guiding guests beyond postcards — into the living heart of Uzbekistan's culture, cuisine, and craftsmanship.</p>
          <p class="micro-proof"><i class="fas fa-check-circle" aria-hidden="true"></i> Trusted by 2,400+ travelers since 2012</p>

          <div class="why-us__contacts">
            <a href="tel:+998991234567" class="contact-link" aria-label="Call Jahongir Travel">
              <i class="fas fa-phone" aria-hidden="true"></i> +998 99 123 4567
            </a>
            <a href="mailto:info@@jahongirtravel.com" class="contact-link" aria-label="Email Jahongir Travel">
              <i class="fas fa-envelope" aria-hidden="true"></i> Email Us
            </a>
            <a href="https://wa.me/998991234567" class="contact-link" target="_blank" rel="noopener noreferrer" aria-label="Chat with Jahongir Travel on WhatsApp">
              <i class="fab fa-whatsapp" aria-hidden="true"></i> WhatsApp
            </a>
          </div>

          <hr class="section-divider">

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

          <div class="why-us__cta-wrapper">
            <a href="{{ url('/tours') }}" class="btn btn--primary btn--large">
              <i class="fas fa-route" aria-hidden="true"></i>
              Plan My Trip
            </a>
          </div>
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
            <img src="{{ asset('images/uzbek-cuizine.webp') }}"
                 alt="Travelers enjoying authentic Uzbek cuisine"
                 width="400"
                 height="300"
                 loading="lazy"
                 decoding="async">
          </div>
          <div class="why-us__badge">
            <div class="trust-badge">
              <i class="fas fa-star" aria-hidden="true"></i>
              <strong>4.9</strong>
              <span class="divider">|</span>
              <span>2,400+ Happy Travelers</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Section 3: Trending Activities -->
  <section class="activities" id="activities">
    <!-- JSON-LD Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ItemList",
      "name": "Trending Activities in Uzbekistan",
      "itemListElement": [
        {"@type": "ListItem", "position": 1, "name": "Cultural & Historical", "url": "https://jahongirtravel.com/tours/category/cultural-tours/"},
        {"@type": "ListItem", "position": 2, "name": "Mountain & Adventure", "url": "https://jahongirtravel.com/tours/category/adventure-tours/"},
        {"@type": "ListItem", "position": 3, "name": "Family & Educational", "url": "https://jahongirtravel.com/tours/category/family-tours/"},
        {"@type": "ListItem", "position": 4, "name": "Desert & Nomadic", "url": "https://jahongirtravel.com/tours/category/desert-tours/"},
        {"@type": "ListItem", "position": 5, "name": "City Walks & Local Life", "url": "https://jahongirtravel.com/tours/category/city-walks/"},
        {"@type": "ListItem", "position": 6, "name": "Food & Craft", "url": "https://jahongirtravel.com/tours/category/food-craft-tours/"}
      ]
    }
    </script>

    <div class="container">

      <!-- Section Header -->
      <div class="section-header">
        <p class="section-eyebrow">WHAT MOVES YOU</p>
        <h2 class="section-header__title">Trending Activities in Uzbekistan</h2>
        <p class="section-header__subtitle">Choose your style of travel — from ancient history to mountain adventures</p>
      </div>

      <!-- Activity Cards Grid (DYNAMIC) -->
      <div class="activities__grid">
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
              <i class="activity-card__icon {{ $category->icon }}" aria-hidden="true"></i>
              <h3 class="activity-card__title">{{ $category->translated_name }}</h3>
              <p class="activity-card__description">{{ $category->translated_description }}</p>
              <span class="activity-card__link">Explore Tours <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
            </div>
          </a>
        @endforeach
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

    <!-- JSON-LD Schema: TouristTrip for Featured Tours -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ItemList",
      "name": "Popular Uzbekistan Tours",
      "itemListElement": [
        {
          "@type": "TouristTrip",
          "position": 1,
          "name": "5-Day Silk Road Classic: Samarkand, Bukhara & Khiva",
          "description": "Experience the iconic cities of the Silk Road with expert local guides. Visit UNESCO World Heritage sites in Samarkand, Bukhara, and Khiva.",
          "touristType": "Cultural & Historical",
          "itinerary": {
            "@type": "ItemList",
            "itemListElement": [
              {"@type": "City", "name": "Samarkand"},
              {"@type": "City", "name": "Bukhara"},
              {"@type": "City", "name": "Khiva"}
            ]
          },
          "offers": {
            "@type": "Offer",
            "url": "https://jahongirtravel.com/tours/silk-road-classic/",
            "price": "890",
            "priceCurrency": "USD",
            "availability": "https://schema.org/InStock",
            "priceSpecification": {
              "@type": "UnitPriceSpecification",
              "price": "890",
              "priceCurrency": "USD",
              "referenceQuantity": {"@type": "QuantitativeValue", "value": "1", "unitText": "person"}
            }
          },
          "provider": {
            "@type": "TravelAgency",
            "name": "Jahongir Travel"
          },
          "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "5",
            "reviewCount": "148"
          }
        },
        {
          "@type": "TouristTrip",
          "position": 2,
          "name": "3-Day Chimgan Mountain Adventure & Charvak Lake",
          "description": "Escape to the mountains for trekking, nature, and outdoor adventures in Uzbekistan's stunning Chimgan region.",
          "touristType": "Mountain & Adventure",
          "itinerary": {
            "@type": "ItemList",
            "itemListElement": [
              {"@type": "City", "name": "Chimgan"},
              {"@type": "City", "name": "Charvak"}
            ]
          },
          "offers": {
            "@type": "Offer",
            "url": "https://jahongirtravel.com/tours/mountain-adventure/",
            "price": "450",
            "priceCurrency": "USD",
            "availability": "https://schema.org/InStock"
          },
          "provider": {
            "@type": "TravelAgency",
            "name": "Jahongir Travel"
          },
          "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.5",
            "reviewCount": "92"
          }
        },
        {
          "@type": "TouristTrip",
          "position": 3,
          "name": "7-Day Cultural Immersion: Crafts, Cuisine & Traditions",
          "description": "Deep dive into Uzbek culture through hands-on craft workshops, traditional cooking classes, and immersive local experiences.",
          "touristType": "Cultural Immersion",
          "itinerary": {
            "@type": "ItemList",
            "itemListElement": [
              {"@type": "City", "name": "Samarkand"},
              {"@type": "City", "name": "Bukhara"}
            ]
          },
          "offers": {
            "@type": "Offer",
            "url": "https://jahongirtravel.com/tours/cultural-immersion/",
            "price": "1290",
            "priceCurrency": "USD",
            "availability": "https://schema.org/InStock"
          },
          "provider": {
            "@type": "TravelAgency",
            "name": "Jahongir Travel"
          },
          "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "5",
            "reviewCount": "215"
          }
        },
        {
          "@type": "TouristTrip",
          "position": 4,
          "name": "4-Day Fergana Valley: Pottery, Silk & Ancient Cities",
          "description": "Discover the lesser-known Fergana Valley, famous for traditional pottery, silk weaving, and authentic Uzbek hospitality.",
          "touristType": "Cultural & Crafts",
          "itinerary": {
            "@type": "ItemList",
            "itemListElement": [
              {"@type": "City", "name": "Tashkent"},
              {"@type": "City", "name": "Fergana"},
              {"@type": "City", "name": "Kokand"}
            ]
          },
          "offers": {
            "@type": "Offer",
            "url": "https://jahongirtravel.com/tours/fergana-valley/",
            "price": "680",
            "priceCurrency": "USD",
            "availability": "https://schema.org/InStock"
          },
          "provider": {
            "@type": "TravelAgency",
            "name": "Jahongir Travel"
          },
          "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.5",
            "reviewCount": "78"
          }
        },
        {
          "@type": "TouristTrip",
          "position": 5,
          "name": "10-Day Grand Silk Road: Complete Uzbekistan Experience",
          "description": "The ultimate Uzbekistan journey covering all major cities and UNESCO sites along the ancient Silk Road.",
          "touristType": "Grand Tour",
          "offers": {
            "@type": "Offer",
            "url": "https://jahongirtravel.com/tours/grand-silk-road/",
            "price": "1850",
            "priceCurrency": "USD",
            "availability": "https://schema.org/InStock"
          },
          "provider": {
            "@type": "TravelAgency",
            "name": "Jahongir Travel"
          },
          "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "5",
            "reviewCount": "320"
          }
        },
        {
          "@type": "TouristTrip",
          "position": 6,
          "name": "3-Day Express: Samarkand & Bukhara Highlights",
          "description": "Perfect for travelers with limited time. See the highlights of Samarkand and Bukhara in just 3 days.",
          "touristType": "Express Tour",
          "itinerary": {
            "@type": "ItemList",
            "itemListElement": [
              {"@type": "City", "name": "Samarkand"},
              {"@type": "City", "name": "Bukhara"}
            ]
          },
          "offers": {
            "@type": "Offer",
            "url": "https://jahongirtravel.com/tours/express-highlights/",
            "price": "540",
            "priceCurrency": "USD",
            "availability": "https://schema.org/InStock"
          },
          "provider": {
            "@type": "TravelAgency",
            "name": "Jahongir Travel"
          },
          "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.5",
            "reviewCount": "164"
          }
        }
      ]
    }
    </script>

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
           hx-get="http://127.0.0.1:8000/partials/tours?per_page=6"
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
      "@context": "https://schema.org",
      "@type": "ItemList",
      "name": "Top Places to Travel in Uzbekistan",
      "itemListElement": [
        {
          "@type": "Place",
          "position": 1,
          "name": "Samarkand",
          "description": "The Jewel of the Silk Road. Historic city famous for Registan Square, Shah-i-Zinda necropolis, and Gur-e-Amir mausoleum.",
          "geo": {
            "@type": "GeoCoordinates",
            "latitude": "39.6542",
            "longitude": "66.9597"
          },
          "address": {
            "@type": "PostalAddress",
            "addressLocality": "Samarkand",
            "addressCountry": "UZ"
          },
          "url": "https://jahongirtravel.com/destinations/samarkand/",
          "touristType": "Cultural & Historical"
        },
        {
          "@type": "Place",
          "position": 2,
          "name": "Bukhara",
          "description": "Living Museum of Central Asia. Ancient city with over 140 architectural monuments and a UNESCO World Heritage old town.",
          "geo": {
            "@type": "GeoCoordinates",
            "latitude": "39.7747",
            "longitude": "64.4286"
          },
          "address": {
            "@type": "PostalAddress",
            "addressLocality": "Bukhara",
            "addressCountry": "UZ"
          },
          "url": "https://jahongirtravel.com/destinations/bukhara/",
          "touristType": "Cultural & Historical"
        },
        {
          "@type": "Place",
          "position": 3,
          "name": "Khiva",
          "description": "Ancient Desert Fortress. UNESCO-protected Itchan Kala fortress city with perfectly preserved Silk Road architecture.",
          "geo": {
            "@type": "GeoCoordinates",
            "latitude": "41.3775",
            "longitude": "60.3642"
          },
          "address": {
            "@type": "PostalAddress",
            "addressLocality": "Khiva",
            "addressCountry": "UZ"
          },
          "url": "https://jahongirtravel.com/destinations/khiva/",
          "touristType": "Cultural & Historical"
        },
        {
          "@type": "Place",
          "position": 4,
          "name": "Tashkent",
          "description": "Modern Heart of Uzbekistan. Capital city blending Soviet architecture, vibrant bazaars, and contemporary culture.",
          "geo": {
            "@type": "GeoCoordinates",
            "latitude": "41.2995",
            "longitude": "69.2401"
          },
          "address": {
            "@type": "PostalAddress",
            "addressLocality": "Tashkent",
            "addressCountry": "UZ"
          },
          "url": "https://jahongirtravel.com/destinations/tashkent/",
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
        @foreach($cities as $city)
          @php
            $tourCount = $city->tour_count;
            $tourText = $tourCount === 1 ? 'tour' : 'tours';

            // Get city image - use featured_image or placeholder
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
                <img
                  src="{{ $imageUrl }}"
                  alt="{{ $altText }}"
                  width="400"
                  height="533"
                  loading="lazy"
                  decoding="async"
                >
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
          @foreach($reviews as $review)
            @php
              $avatarUrl = $review->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($review->reviewer_name) . '&size=60&background=0D4C92&color=fff';
              $location = $review->reviewer_location ?? 'Travel Enthusiast';
              $date = $review->created_at->format('F Y');
              $sourceIcon = $review->source === 'tripadvisor' ? 'fab fa-tripadvisor' : 'fas fa-star';
              $sourceName = ucfirst($review->source ?? 'Website');
              $reviewerName = htmlspecialchars($review->reviewer_name);
              $reviewTitle = htmlspecialchars($review->title);
              $reviewContent = htmlspecialchars($review->content);
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
        </div>

        <!-- Swiper Navigation -->
        <div class="swiper-button-prev reviews-swiper-prev"></div>
        <div class="swiper-button-next reviews-swiper-next"></div>

        <!-- Swiper Pagination -->
        <div class="swiper-pagination reviews-swiper-pagination"></div>
      </div>

      <!-- TripAdvisor Badge and CTA -->
      <div class="reviews__footer">
        <div class="reviews__badge">
          <i class="fab fa-tripadvisor" aria-hidden="true"></i>
          <div class="reviews__badge-content">
            <p class="reviews__badge-title">Rated Excellent on TripAdvisor</p>
            <div class="stars" aria-label="Rated 4.9 out of 5 stars">
              <i class="fas fa-star" aria-hidden="true"></i>
              <i class="fas fa-star" aria-hidden="true"></i>
              <i class="fas fa-star" aria-hidden="true"></i>
              <i class="fas fa-star" aria-hidden="true"></i>
              <i class="fas fa-star" aria-hidden="true"></i>
            </div>
            <p class="reviews__badge-stats">4.9 / 5 based on 2,400+ reviews</p>
          </div>
        </div>
        <a href="https://www.tripadvisor.com/jahongir-travel" target="_blank" rel="noopener noreferrer" class="btn btn--primary btn--large">
          <i class="fab fa-tripadvisor" aria-hidden="true"></i>
          Read All Reviews
        </a>
      </div>

    </div>
  </section>

  <!-- =====================================================
       SECTION 7: TRAVEL INSIGHTS & BLOG PREVIEW
       ===================================================== -->
  <section class="blog-preview" id="blog" aria-labelledby="blog-title">

    <!-- JSON-LD Structured Data for SEO -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ItemList",
      "name": "Jahongir Travel Blog Articles",
      "description": "Travel insights, tips, and guides for visiting Uzbekistan",
      "itemListElement": [
        {
          "@type": "BlogPosting",
          "position": 1,
          "headline": "Best Time to Visit Uzbekistan: A Season-by-Season Guide",
          "image": "https://jahongirtravel.com/images/blog-best-time-visit.svg",
          "datePublished": "2024-11-15",
          "dateModified": "2024-11-15",
          "author": {
            "@type": "Organization",
            "name": "Jahongir Travel"
          },
          "publisher": {
            "@type": "Organization",
            "name": "Jahongir Travel",
            "logo": {
              "@type": "ImageObject",
              "url": "https://jahongirtravel.com/images/logo.png"
            }
          },
          "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "https://jahongirtravel.com/blog/best-time-visit-uzbekistan"
          },
          "description": "Discover the ideal months for your Uzbekistan adventure, from spring blooms in Samarkand to golden autumn in Bukhara",
          "articleSection": "Travel Tips",
          "wordCount": 1200,
          "inLanguage": "en-US",
          "url": "https://jahongirtravel.com/blog/best-time-visit-uzbekistan"
        },
        {
          "@type": "BlogPosting",
          "position": 2,
          "headline": "Hidden Gems Along the Silk Road: Off-the-Beaten-Path Destinations",
          "image": "https://jahongirtravel.com/images/blog-hidden-gems.svg",
          "datePublished": "2024-11-08",
          "dateModified": "2024-11-08",
          "author": {
            "@type": "Organization",
            "name": "Jahongir Travel"
          },
          "publisher": {
            "@type": "Organization",
            "name": "Jahongir Travel",
            "logo": {
              "@type": "ImageObject",
              "url": "https://jahongirtravel.com/images/logo.png"
            }
          },
          "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "https://jahongirtravel.com/blog/hidden-gems-silk-road"
          },
          "description": "Venture beyond Samarkand and Bukhara to discover lesser-known treasures like Nurata, Shakhrisabz, and the Aral Sea region",
          "articleSection": "Destinations",
          "wordCount": 1500,
          "inLanguage": "en-US",
          "url": "https://jahongirtravel.com/blog/hidden-gems-silk-road"
        },
        {
          "@type": "BlogPosting",
          "position": 3,
          "headline": "A Foodie's Guide to Uzbek Cuisine: Must-Try Dishes and Where to Find Them",
          "image": "https://jahongirtravel.com/images/blog-uzbek-cuisine.svg",
          "datePublished": "2024-10-28",
          "dateModified": "2024-10-28",
          "author": {
            "@type": "Organization",
            "name": "Jahongir Travel"
          },
          "publisher": {
            "@type": "Organization",
            "name": "Jahongir Travel",
            "logo": {
              "@type": "ImageObject",
              "url": "https://jahongirtravel.com/images/logo.png"
            }
          },
          "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "https://jahongirtravel.com/blog/uzbek-cuisine-guide"
          },
          "description": "From sizzling plov to hand-pulled lagman noodles, explore the rich flavors of Central Asian cuisine at authentic local spots",
          "articleSection": "Culture",
          "wordCount": 1350,
          "inLanguage": "en-US",
          "url": "https://jahongirtravel.com/blog/uzbek-cuisine-guide"
        }
      ]
    }
    </script>

    <div class="container">

      <!-- Section Header -->
      <header class="section-header text-center">
        <p class="section-eyebrow">FROM OUR EXPERTS</p>
        <h2 id="blog-title" class="section-title">Travel Insights & Tips</h2>
        <p class="section-subtitle">Insider knowledge to make your Silk Road journey unforgettable</p>
      </header>

      <!-- Blog Grid (DYNAMIC) -->
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
                <img
                  src="{{ $featuredImage }}"
                  alt="{{ $post->title }}"
                  width="800"
                  height="450"
                  loading="lazy"
                  fetchpriority="low"
                  decoding="async">
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

  <!-- Main JavaScript -->
  <script src="{{ asset('js/main.js') }}" defer></script>

  <!-- Reviews Carousel -->
  <script src="{{ asset('js/reviews-carousel.js') }}"></script>
@endpush
