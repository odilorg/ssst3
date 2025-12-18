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

  <!-- Master Cards Hover Effects -->
  <style>
    /* Master Cards Base Styles */
    .master-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .master-card .city-card__link {
      display: block;
      height: 100%;
      text-decoration: none;
    }

    .master-card .city-card__media {
      position: relative;
    }

    .master-card .city-card__media img {
      object-fit: cover;
      width: 100%;
      height: auto;
    }

    /* Gradient Overlay */
    .card-gradient-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.7) 100%);
      pointer-events: none;
    }

    .master-card .city-card__content {
      position: relative;
      z-index: 2;
      padding: 20px 24px; /* Better breathing room */
    }

    /* Location/Stats Spacing */
    .master-card .city-card__stats {
      margin-top: 8px; /* Better separation from tagline */
    }

    /* Card Description Text */
    .card-description {
      font-size: 0.875rem;
      color: rgba(255, 255, 255, 0.9);
      margin-top: 12px; /* More breathing room */
      margin-bottom: 0; /* Remove bottom margin - let CTA control spacing */
      line-height: 1.5;
    }

    /* CTA Button */
    .master-card .city-card__cta {
      display: inline-block;
      margin-top: 16px; /* Consistent spacing from description */
      color: #fff;
      font-weight: 600;
      transition: transform 0.2s ease;
    }

    .master-card .city-card__cta i {
      margin-left: 0.5rem;
      transition: transform 0.2s ease;
    }

    /* Hover Effects */
    .master-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .master-card:hover .city-card__cta {
      transform: translateX(4px);
    }

    /* Mobile Touch Feedback */
    .master-card:active {
      transform: scale(0.98);
      transition: transform 0.1s ease;
    }

    /* Focus States for Accessibility */
    .master-card .city-card__link:focus {
      outline: 3px solid #27ae60;
      outline-offset: 4px;
      border-radius: 8px;
    }

    /* Add cursor pointer for better UX */
    .master-card {
      cursor: pointer;
    }

    /* Mobile Responsive - Single Column on Small Screens */
    @media (max-width: 500px) {
      .places__grid {
        grid-template-columns: 1fr !important;
        gap: 24px;
      }

      .master-card {
        max-width: 100%;
      }

      .card-description {
        font-size: 0.9375rem; /* Slightly larger on mobile */
      }
    }

    /* Tablet Responsive */
    @media (min-width: 501px) and (max-width: 768px) {
      .places__grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
      }
    }
  </style>
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
          <h1 id="hero-heading" class="hero__title">Live the Craft. Meet the Masters. Preserve the Tradition.</h1>
          <p class="hero__sub">Small-group craft immersion in Uzbekistan (Max 6 travelers)</p>

          <div class="hero__cta">
            <a href="{{ url('/tours') }}" class="btn btn--accent btn--large btn--pill">
              <i class="fas fa-map-marked-alt" aria-hidden="true"></i>
              View 2025 Craft Workshops
              <i class="fas fa-arrow-right" aria-hidden="true" style="margin-left: 8px;"></i>
            </a>

            <!-- Trust Badges -->
            <div class="hero__trust-badges">
              <div class="trust-badge-item">
                <i class="fas fa-lock" aria-hidden="true"></i>
                <span>Secure Booking</span>
              </div>
              <div class="trust-badge-item">
                <i class="fas fa-undo-alt" aria-hidden="true"></i>
                <span>Free Cancellation</span>
              </div>
              <div class="trust-badge-item">
                <i class="fas fa-headset" aria-hidden="true"></i>
                <span>24/7 Support</span>
              </div>
              <div class="trust-badge-item">
                <i class="fas fa-certificate" aria-hidden="true"></i>
                <span>Licensed Operator</span>
              </div>
            </div>
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
            <a href="mailto:info@jahongir-travel.uz" class="contact-link" aria-label="Email Jahongir Travel">
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
  {{-- HIDDEN: Shows "0 tours" - conversion killer. Using "Meet the Masters" section instead --}}
  @if(false)
  <section class="activities" id="activities">
    <!-- JSON-LD Schema (Dynamic) -->
    @if(!empty($categories) && count($categories) > 0)
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "ItemList",
      "name": "Featured Craft Workshops in Uzbekistan",
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
        <p class="section-eyebrow">MASTER THE CRAFTS</p>
        <h2 class="section-header__title">Learn From UNESCO Master Artisans</h2>
        <p class="section-header__subtitle">Hands-on workshops in pottery, silk weaving, suzani embroidery, and traditional crafts passed down through generations</p>
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
  @endif
  {{-- END HIDDEN SECTION --}}

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
        <p class="section-eyebrow">CRAFT IMMERSION JOURNEYS</p>
        <h2 class="section-title">Choose Your Craft Journey</h2>
        <p class="section-subtitle">
          From weekend pottery tasters to 12-day craft odysseys. Small groups (max 6), hands-on workshops, artisan homestays.
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
        <a href="{{ url('/tours') }}" class="btn btn--accent btn--large" aria-label="View all craft immersion journeys">
          <i class="fas fa-hands-helping" aria-hidden="true"></i>
          View All Craft Journeys
        </a>
      </div>

    </div>
  </section>

  <!-- ========================================
       SECTION 5: MEET THE MASTERS
  ========================================= -->
  <section class="places" id="masters">

    <!-- JSON-LD Schema: Master Artisans -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "ItemList",
      "name": "Master Artisans of Uzbekistan",
      "description": "UNESCO-recognized master craftspeople teaching pottery, silk weaving, suzani embroidery, and traditional crafts",
      "itemListElement": [
        {
          "@@type": "Person",
          "position": 1,
          "name": "Alisher Nazirov",
          "jobTitle": "UNESCO Master Potter (5th Generation)",
          "description": "UNESCO-recognized ceramics master in Gijduvan. 5th-generation potter teaching the famous 'Gijduvan blue' glaze technique.",
          "knowsAbout": ["Pottery", "Ceramics", "Traditional Uzbek Glazing"],
          "address": {
            "@@type": "PostalAddress",
            "addressLocality": "Gijduvan",
            "addressRegion": "Bukhara Province",
            "addressCountry": "UZ"
          }
        },
        {
          "@@type": "Organization",
          "position": 2,
          "name": "Yodgorlik Silk Factory",
          "description": "Legendary silk factory in Margilan with 100-year-old machines. Watch silk being spun from cocoons using traditional methods.",
          "knowsAbout": ["Silk Weaving", "Ikat Dyeing", "Textile Arts"],
          "address": {
            "@@type": "PostalAddress",
            "addressLocality": "Margilan",
            "addressRegion": "Fergana Valley",
            "addressCountry": "UZ"
          }
        },
        {
          "@@type": "Person",
          "position": 3,
          "name": "Master Embroiderers of Bukhara",
          "jobTitle": "Suzani Embroidery Masters",
          "description": "Learn suzani embroidery from families who have passed down designs for 5+ generations. Chain stitch, satin stitch, and traditional patterns.",
          "knowsAbout": ["Suzani Embroidery", "Traditional Textiles", "Chain Stitch"],
          "address": {
            "@@type": "PostalAddress",
            "addressLocality": "Bukhara",
            "addressCountry": "UZ"
          }
        },
        {
          "@@type": "Person",
          "position": 4,
          "name": "Miniature Painting Masters",
          "jobTitle": "Persian Miniature Artists",
          "description": "Study Persian miniature painting techniques in Bukhara. Learn pigment preparation, brush techniques, and traditional motifs.",
          "knowsAbout": ["Miniature Painting", "Persian Art", "Traditional Pigments"],
          "address": {
            "@@type": "PostalAddress",
            "addressLocality": "Bukhara",
            "addressCountry": "UZ"
          }
        }
      ]
    }
    </script>

    <div class="container--wide">

      <!-- Section Header -->
      <div class="places__header">
        <p class="section-eyebrow">THE ARTISANS</p>
        <h2 class="section-title">Meet the Masters</h2>
        <p class="section-subtitle">
          Learn from UNESCO-recognized craftspeople and families who have passed down skills for generations
        </p>
      </div>

      <!-- Masters Grid -->
      <div class="places__grid">
        <!-- Master 1: Alisher Nazirov - Gijduvan Pottery -->
        <article class="city-card master-card">
          <a href="{{ url('/tours/ceramics-miniature-painting-uzbekistan') }}" class="city-card__link" aria-label="Learn pottery with Alisher Nazirov">
            <div class="city-card__media">
              <img
                src="https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=400&h=533&fit=crop&q=80"
                alt="Master potter shaping clay on pottery wheel in Gijduvan"
                width="400"
                height="533"
                loading="lazy"
                decoding="async"
                >
              <div class="card-gradient-overlay"></div>
            </div>
            <div class="city-card__content">
              <h3 class="city-card__title">Alisher Nazirov</h3>
              <p class="city-card__tagline">UNESCO Master Potter (5th Generation)</p>
              <p class="city-card__stats">
                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                Gijduvan • Ceramics & Glazing
              </p>
              <p class="card-description">
                Learn the famous "Gijduvan blue" glaze technique from a 5th-generation UNESCO master.
              </p>
              <span class="city-card__cta">
                View Pottery Workshops<i class="fas fa-arrow-right" aria-hidden="true"></i>
              </span>
            </div>
          </a>
        </article>

        <!-- Master 2: Yodgorlik Silk Factory -->
        <article class="city-card master-card">
          <a href="{{ url('/tours/textile-immersion-uzbekistan') }}" class="city-card__link" aria-label="Learn silk weaving at Yodgorlik Factory">
            <div class="city-card__media">
              <img
                src="https://images.unsplash.com/photo-1616430285525-27165e1c45d8?w=400&h=533&fit=crop&q=80"
                alt="Vibrant silk fabrics with traditional ikat patterns at Yodgorlik Factory"
                width="400"
                height="533"
                loading="lazy"
                decoding="async"
                >
              <div class="card-gradient-overlay"></div>
            </div>
            <div class="city-card__content">
              <h3 class="city-card__title">Yodgorlik Silk Factory</h3>
              <p class="city-card__tagline">Legendary Silk Masters of Margilan</p>
              <p class="city-card__stats">
                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                Margilan • Silk Weaving & Ikat
              </p>
              <p class="card-description">
                Watch silk being spun from cocoons on 100-year-old machines. Learn traditional ikat dyeing.
              </p>
              <span class="city-card__cta">
                View Silk Workshops<i class="fas fa-arrow-right" aria-hidden="true"></i>
              </span>
            </div>
          </a>
        </article>

        <!-- Master 3: Suzani Embroidery -->
        <article class="city-card master-card">
          <a href="{{ url('/tours/textile-immersion-uzbekistan') }}" class="city-card__link" aria-label="Learn suzani embroidery with master embroiderers">
            <div class="city-card__media">
              <img
                src="https://images.unsplash.com/photo-1452860606245-08befc0ff44b?w=400&h=533&fit=crop&q=80"
                alt="Master embroiderer working on traditional suzani textile in Bukhara"
                width="400"
                height="533"
                loading="lazy"
                decoding="async"
                >
              <div class="card-gradient-overlay"></div>
            </div>
            <div class="city-card__content">
              <h3 class="city-card__title">Suzani Embroidery Masters</h3>
              <p class="city-card__tagline">5+ Generations of Family Designs</p>
              <p class="city-card__stats">
                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                Bukhara • Textile Embroidery
              </p>
              <p class="card-description">
                Learn chain stitch and satin stitch from families who've passed down patterns for 5 generations.
              </p>
              <span class="city-card__cta">
                View Embroidery Classes<i class="fas fa-arrow-right" aria-hidden="true"></i>
              </span>
            </div>
          </a>
        </article>

        <!-- Master 4: Miniature Painting -->
        <article class="city-card master-card">
          <a href="{{ url('/tours/ceramics-miniature-painting-uzbekistan') }}" class="city-card__link" aria-label="Learn Persian miniature painting">
            <div class="city-card__media">
              <img
                src="https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=400&h=533&fit=crop&q=80"
                alt="Artist painting intricate Persian miniature artwork in Bukhara"
                width="400"
                height="533"
                loading="lazy"
                decoding="async"
                >
              <div class="card-gradient-overlay"></div>
            </div>
            <div class="city-card__content">
              <h3 class="city-card__title">Miniature Painting Masters</h3>
              <p class="city-card__tagline">Persian Miniature Artists</p>
              <p class="city-card__stats">
                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                Bukhara • Visual Arts
              </p>
              <p class="card-description">
                Study Persian miniature painting techniques. Learn pigment preparation and brush techniques.
              </p>
              <span class="city-card__cta">
                View Painting Workshops<i class="fas fa-arrow-right" aria-hidden="true"></i>
              </span>
            </div>
          </a>
        </article>
      </div>

      <!-- View All Masters CTA -->
      <div class="places__cta">
        <a href="{{ url('/tours') }}" class="btn btn--primary btn--large" aria-label="Explore craft immersion journeys">
          <i class="fas fa-users" aria-hidden="true"></i>
          Meet All Our Master Artisans
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
          {{-- Fallback: Show TripAdvisor reviews --}}

          <!-- Review 1: Sarah Mitchell - UK -->
          <div class="swiper-slide">
            <article class="review-card">
              <div class="review-card__header">
                <img src="https://ui-avatars.com/api/?name=Sarah+Mitchell&size=60&background=0D4C92&color=fff" alt="Sarah Mitchell" class="review-card__avatar" width="60" height="60" loading="lazy">
                <div class="review-card__author">
                  <h3 class="review-card__name">Sarah Mitchell</h3>
                  <p class="review-card__location"><i class="fas fa-map-marker-alt"></i> United Kingdom</p>
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
                <p class="review-card__date">Traveled in October 2024</p>
              </div>
              <div class="review-card__content">
                <i class="fas fa-quote-left review-card__quote"></i>
                <h4 class="review-card__title">Terrific Guide, Amazing Experience</h4>
                <p class="review-card__text">Our guide Odil was terrific! He knew all the details about the workshops and spoke fluent English. Very gentle manners and went the extra mile to explain the pottery techniques. The Gijduvan blue glaze workshop was unforgettable. Strongly recommended!</p>
                <p class="review-card__source"><i class="fab fa-tripadvisor"></i> Reviewed on TripAdvisor</p>
              </div>
            </article>
          </div>

          <!-- Review 2: Michael Kim - USA -->
          <div class="swiper-slide">
            <article class="review-card">
              <div class="review-card__header">
                <img src="https://ui-avatars.com/api/?name=Michael+Kim&size=60&background=0D4C92&color=fff" alt="Michael Kim" class="review-card__avatar" width="60" height="60" loading="lazy">
                <div class="review-card__author">
                  <h3 class="review-card__name">Michael Kim</h3>
                  <p class="review-card__location"><i class="fas fa-map-marker-alt"></i> USA</p>
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
                <p class="review-card__date">Traveled in September 2024</p>
              </div>
              <div class="review-card__content">
                <i class="fas fa-quote-left review-card__quote"></i>
                <h4 class="review-card__title">Professional & Knowledgeable</h4>
                <p class="review-card__text">Atel, my tour guide, was very helpful and had in-depth knowledge about traditional Uzbek crafts. He goes the extra mile to explain things clearly and made sure we understood each step of the silk weaving process at Yodgorlik Factory. A truly authentic experience!</p>
                <p class="review-card__source"><i class="fab fa-tripadvisor"></i> Reviewed on TripAdvisor</p>
              </div>
            </article>
          </div>

          <!-- Review 3: Emma Laurent - France -->
          <div class="swiper-slide">
            <article class="review-card">
              <div class="review-card__header">
                <img src="https://ui-avatars.com/api/?name=Emma+Laurent&size=60&background=0D4C92&color=fff" alt="Emma Laurent" class="review-card__avatar" width="60" height="60" loading="lazy">
                <div class="review-card__author">
                  <h3 class="review-card__name">Emma Laurent</h3>
                  <p class="review-card__location"><i class="fas fa-map-marker-alt"></i> France</p>
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
                <p class="review-card__date">Traveled in August 2024</p>
              </div>
              <div class="review-card__content">
                <i class="fas fa-quote-left review-card__quote"></i>
                <h4 class="review-card__title">Warm, Family-Run Excellence</h4>
                <p class="review-card__text">The family-run approach makes all the difference. Very familiar atmosphere with attentive staff who speak excellent English. The suzani embroidery workshop was intimate and authentic. Located perfectly near Registan. Definitely recommendable!</p>
                <p class="review-card__source"><i class="fab fa-tripadvisor"></i> Reviewed on TripAdvisor</p>
              </div>
            </article>
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
        <p class="section-eyebrow">WORKSHOP GUIDES & TIPS</p>
        <h2 id="blog-title" class="section-title">Plan Your Craft Workshop Journey</h2>
        <p class="section-subtitle">Expert guides on pottery, silk weaving, and traditional crafts — everything you need to know before booking</p>
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
        <a href="{{ url('/blog') }}" class="btn btn--large btn--ghost" aria-label="View all craft workshop guides">
          Read More Workshop Guides
          <i class="fas fa-arrow-right" aria-hidden="true"></i>
        </a>
      </div>

    </div>
  </section>

  <!-- WhatsApp Floating Widget -->
  <a href="https://wa.me/998915550808?text=Hi!%20I%27m%20interested%20in%20craft%20workshops%20in%20Uzbekistan.%20Can%20you%20help%20me%20plan%20a%20trip%3F"
     class="whatsapp-float"
     target="_blank"
     rel="noopener noreferrer"
     aria-label="Chat with us on WhatsApp">
    <i class="fab fa-whatsapp" aria-hidden="true"></i>
    <span class="whatsapp-float__text">Chat with us!</span>
  </a>
@endsection

@push('styles')
<style>
/* Hero Trust Badges */
.hero__trust-badges {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 24px;
  margin-top: 20px;
  flex-wrap: wrap;
}

.trust-badge-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: rgba(255, 255, 255, 0.9);
  font-size: 0.875rem;
  font-weight: 500;
}

.trust-badge-item i {
  font-size: 1rem;
  color: #4ade80;
}

@media (max-width: 640px) {
  .hero__trust-badges {
    gap: 16px;
  }

  .trust-badge-item {
    font-size: 0.8125rem;
  }

  .trust-badge-item i {
    font-size: 0.9375rem;
  }
}

/* WhatsApp Floating Widget */
.whatsapp-float {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: #25D366;
  color: #fff;
  border-radius: 50px;
  padding: 16px 24px;
  display: flex;
  align-items: center;
  gap: 12px;
  box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
  transition: all 0.3s ease;
  z-index: 1000;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9375rem;
}

.whatsapp-float:hover {
  background: #128C7E;
  box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6);
  transform: translateY(-2px);
  color: #fff;
}

.whatsapp-float:active {
  transform: translateY(0) scale(0.98);
}

.whatsapp-float i {
  font-size: 1.5rem;
}

.whatsapp-float__text {
  white-space: nowrap;
}

/* Mobile: Icon only */
@media (max-width: 640px) {
  .whatsapp-float {
    width: 60px;
    height: 60px;
    padding: 0;
    justify-content: center;
    border-radius: 50%;
    bottom: 20px;
    right: 20px;
  }

  .whatsapp-float__text {
    display: none;
  }

  .whatsapp-float i {
    font-size: 1.75rem;
    margin: 0;
  }
}

/* Tablet: Show text on hover */
@media (min-width: 641px) and (max-width: 1024px) {
  .whatsapp-float__text {
    max-width: 0;
    overflow: hidden;
    transition: max-width 0.3s ease;
  }

  .whatsapp-float:hover .whatsapp-float__text {
    max-width: 150px;
  }
}
</style>
@endpush

@push('scripts')
  <!-- HTMX Library -->
  <script src="https://unpkg.com/htmx.org@1.9.10" defer></script>

  <!-- Swiper JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>


  <!-- Reviews Carousel -->
  <script src="{{ asset('js/reviews-carousel.js') }}" defer></script>
@endpush
