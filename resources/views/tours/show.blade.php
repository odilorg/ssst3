<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#1a5490">

  <!-- Preconnect to Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <!-- Preload Critical Fonts -->
  <link rel="preload" href="https://fonts.gstatic.com/s/poppins/v20/pxiEyp8kv8JHgFVrJJfecg.woff2" as="font" type="font/woff2" crossorigin>

  <!-- SEO Meta Tags -->
  <title>{{ $tour->title }} | Jahongir Travel</title>
  <meta name="description" content="{{ $tour->short_description ?? Str::limit(strip_tags($tour->long_description), 160) }}">
  <link rel="canonical" href="{{ url('/tours/' . $tour->slug) }}">
  <meta name="robots" content="index, follow">
  <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large">

  <!-- Hreflang Alternates -->
  <link rel="alternate" hreflang="en" href="{{ url('/tours/' . $tour->slug) }}">
  <link rel="alternate" hreflang="ru" href="{{ url('/ru/tours/' . $tour->slug) }}">
  <link rel="alternate" hreflang="fr" href="{{ url('/fr/tours/' . $tour->slug) }}">
  <link rel="alternate" hreflang="x-default" href="{{ url('/tours/' . $tour->slug) }}">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Jahongir Travel">
  <meta property="og:locale" content="en_US">
  <meta property="og:title" content="{{ $tour->title }} | Jahongir Travel">
  <meta property="og:description" content="{{ $tour->short_description ?? Str::limit(strip_tags($tour->long_description), 160) }}">
  <meta property="og:image" content="{{ $tour->hero_image ? asset('storage/' . $tour->hero_image) : asset('images/default-tour.jpg') }}">
  <meta property="og:url" content="{{ url('/tours/' . $tour->slug) }}">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{{ $tour->title }} | Jahongir Travel">
  <meta name="twitter:description" content="{{ $tour->short_description ?? Str::limit(strip_tags($tour->long_description), 160) }}">
  <!-- Google Fonts: Poppins, Inter & Playfair Display -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

  <!-- Critical CSS - Inlined for Performance -->
  <style>
    /* Reset & Base */
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    html{font-size:16px;-webkit-text-size-adjust:100%}
    body{font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;line-height:1.6;color:#1E1E1E;background:#FAF8F4;margin:0;-webkit-font-smoothing:antialiased}
    img{display:block;max-width:100%;height:auto}
    h1,h2,h3,h4,h5,h6{font-weight:600;line-height:1.3;margin-top:0;margin-bottom:0.5rem}
    h1{font-size:clamp(1.75rem,4vw,2.5rem)}
    h2{font-size:clamp(1.5rem,3vw,2rem)}
    p{margin-bottom:1rem}
    a{color:#0D4C92;text-decoration:none}
    a:hover{text-decoration:underline}

    /* Utilities */
    .is-hidden{display:none!important}
    .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}

    /* Skip Link */
    .skip-link{position:absolute;top:-40px;left:0;background:#0D4C92;color:#fff;padding:8px 16px;text-decoration:none;z-index:100;transition:top 0.2s}
    .skip-link:focus{top:0}

    /* SVG Icons */
    .icon{display:inline-block;vertical-align:middle;fill:currentColor;flex-shrink:0}
    .icon--star{color:#F4B400}

    /* Container */
    .container{max-width:1200px;margin:0 auto;padding:0 1.5rem}

    /* Buttons (minimal) */
    .btn{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border:none;border-radius:6px;font-size:1rem;font-weight:500;cursor:pointer;transition:all 0.2s;min-height:44px;justify-content:center}
    .btn--primary{background:#0D4C92;color:#fff}
    .btn--accent{background:#F4B400;color:#1E1E1E}

    /* Skeleton Loaders */
    .skeleton{background:linear-gradient(90deg,#E3E3E3 0%,#F5F5F5 50%,#E3E3E3 100%);background-size:200% 100%;animation:skeleton-loading 1.5s ease-in-out infinite;border-radius:4px}
    @keyframes skeleton-loading{0%{background-position:200% 0}100%{background-position:-200% 0}}
    .skeleton--hero{height:400px;width:100%;border-radius:8px}
    .skeleton--title{height:32px;width:70%;margin-bottom:16px}
    .skeleton--text{height:16px;width:100%;margin-bottom:8px}
    .skeleton--thumb{height:80px;width:100%;border-radius:4px}

    /* Basic Layout - Prevent FOUC */
    .site-header{background:#fff;position:sticky;top:0;z-index:50}
    .tour-hero{margin:2rem 0}
    .breadcrumbs{padding:1rem 0;font-size:0.875rem}

    /* Form Elements */
    input,select,textarea{font-family:inherit;font-size:1rem;padding:10px 12px;border:1px solid #E3E3E3;border-radius:4px;min-height:44px;width:100%}
    input:focus,select:focus,textarea:focus{outline:2px solid #0D4C92;outline-offset:2px}

    /* Accessibility - Focus visible */
    *:focus-visible{outline:2px solid #0D4C92;outline-offset:2px}

    /* Navigation - Critical (matching index.html .nav--sticky styles) */
    .nav{position:relative;background:rgba(255,255,255,0.95);backdrop-filter:blur(10px);box-shadow:0 2px 8px rgba(0,0,0,0.05)}
    .nav .container{display:flex;align-items:center;justify-content:space-between;gap:32px;padding:16px 24px}
    .nav__logo{text-decoration:none}
    .nav__logo-text{font-family:"Poppins",sans-serif;font-size:1.5rem;font-weight:600;color:#0D4C92}
    .nav__logo-text strong{color:#F4B400}
    .nav__menu{display:flex;list-style:none;margin:0;padding:0;gap:32px;align-items:center}
    .nav__menu li{margin:0;padding:0}
    .nav__menu a{color:#1E1E1E;text-decoration:none;font-weight:500;font-size:1rem;transition:color 0.3s ease;position:relative;display:inline-block;padding:4px 0}
    .nav__menu a:hover,.nav__menu a:focus{color:#F4B400}
    .nav__cta{flex-shrink:0;display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:#F4B400;color:#1E1E1E;border:none;border-radius:6px;font-weight:500;font-size:1rem;text-decoration:none;min-height:44px;transition:all 0.2s}
    .nav__toggle{display:none;background:none;border:none;cursor:pointer;padding:8px;min-height:44px;min-width:44px}
    .nav__toggle-icon{display:block;width:28px;height:2px;background:#1E1E1E;position:relative;transition:background 0.3s}
    .nav__toggle-icon::before,.nav__toggle-icon::after{content:'';position:absolute;width:28px;height:2px;background:#1E1E1E;left:0;transition:all 0.3s}
    .nav__toggle-icon::before{top:-8px}
    .nav__toggle-icon::after{bottom:-8px}
    .nav__toggle[aria-expanded="true"] .nav__toggle-icon{background:transparent}
    .nav__toggle[aria-expanded="true"] .nav__toggle-icon::before{top:0;transform:rotate(45deg)}
    .nav__toggle[aria-expanded="true"] .nav__toggle-icon::after{bottom:0;transform:rotate(-45deg)}
    @media (max-width:900px){
      .nav__toggle{display:inline-flex;align-items:center;justify-content:center}
      .nav__menu{display:none;position:absolute;top:100%;left:0;right:0;background:#fff;flex-direction:column;gap:16px;padding:16px;box-shadow:0 4px 6px rgba(0,0,0,0.1)}
      .nav__menu.is-open{display:flex}
      .nav__cta{display:none}
    }

    /* Footer - Critical (Unified Simple Design) */
    .site-footer{background:#2c3e50;color:#fff;padding:3rem 0 1rem}
    .footer-main{display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr;gap:2rem;padding-bottom:2rem}
    .footer-brand{color:rgba(255,255,255,0.9)}
    .footer-brand__text{font-size:1.25rem;font-weight:600;margin-bottom:0.75rem}
    .footer-brand p{margin:0.5rem 0;font-size:0.875rem;line-height:1.6}
    .footer-brand a{color:rgba(255,255,255,0.8);text-decoration:none;transition:color .2s}
    .footer-brand a:hover{color:#fff}
    .footer-col{color:rgba(255,255,255,0.9)}
    .footer-nav__title{font-size:1rem;font-weight:600;margin-bottom:1rem;color:#fff}
    .footer-nav__list{list-style:none;margin:0;padding:0}
    .footer-nav__list li{margin-bottom:0.5rem}
    .footer-nav__list a{color:rgba(255,255,255,0.75);text-decoration:none;font-size:0.875rem;transition:color .2s}
    .footer-nav__list a:hover{color:#fff}
    .footer-social__list{list-style:none;margin:0;padding:0}
    .footer-social__list li{margin-bottom:0.5rem}
    .footer-social__list a{color:rgba(255,255,255,0.75);text-decoration:none;font-size:0.875rem;transition:color .2s}
    .footer-social__list a:hover{color:#fff}
    .footer-bottom{border-top:1px solid rgba(255,255,255,0.15);padding-top:1.5rem;margin-top:1rem}
    .footer-bottom__wrap{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;font-size:0.875rem}
    .footer-bottom__legal{display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap}
    .footer-bottom__legal a{color:rgba(255,255,255,0.7);text-decoration:none;transition:color .2s}
    .footer-bottom__legal a:hover{color:#fff}
    @media (max-width:900px){
      .footer-main{grid-template-columns:1fr 1fr;gap:2rem}
    }
    @media (max-width:560px){
      .footer-main{grid-template-columns:1fr;gap:2rem}
    }

    /* Loading State */
    @media (prefers-reduced-motion:reduce){*{animation-duration:0.01ms!important;animation-iteration-count:1!important;transition-duration:0.01ms!important}}
  </style>

  <!-- Tour details stylesheet - guaranteed load -->
  <link rel="preload" href="tour-details.css" as="style">
  <link rel="stylesheet" href="tour-details.css">
<!-- Sidebar scrolling fix -->  <link rel="stylesheet" href="sidebar-fix.css">

  <!-- Tour reviews stylesheet -->
  <link rel="stylesheet" href="{{ asset('css/tour-reviews.css') }}">

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="images/favicon.png">

  <!-- Tour JSON-LD Schema -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Tour",
    "name": "Samarkand City Tour: Registan Square and Historic Monuments",
    "description": "Explore the magnificent Registan Square, Shah-i-Zinda necropolis, and Bibi-Khanym Mosque on this comprehensive 4-hour walking tour of Samarkand's UNESCO World Heritage sites.",
    "image": [
      "https://jahongirtravel.com/images/tours/samarkand/hero-main.webp",
      "https://jahongirtravel.com/images/tours/samarkand/registan-1.webp",
      "https://jahongirtravel.com/images/tours/samarkand/shah-i-zinda.webp"
    ],
    "provider": {
      "@type": "Organization",
      "name": "Jahongir Travel",
      "url": "https://jahongirtravel.com",
      "logo": "https://jahongirtravel.com/images/logo.png",
      "telephone": "+998-90-123-45-67",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Samarkand",
        "addressCountry": "UZ"
      }
    },
    "touristType": "Private Activity",
    "duration": "PT4H",
    "inLanguage": ["en", "ru", "fr"],
    "availableLanguage": [
      {
        "@type": "Language",
        "name": "English"
      },
      {
        "@type": "Language",
        "name": "Russian"
      },
      {
        "@type": "Language",
        "name": "French"
      }
    ],
    "itinerary": {
      "@type": "ItemList",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "item": {
            "@type": "TouristAttraction",
            "name": "Registan Square"
          }
        },
        {
          "@type": "ListItem",
          "position": 2,
          "item": {
            "@type": "TouristAttraction",
            "name": "Shah-i-Zinda Necropolis"
          }
        },
        {
          "@type": "ListItem",
          "position": 3,
          "item": {
            "@type": "TouristAttraction",
            "name": "Bibi-Khanym Mosque"
          }
        }
      ]
    },
    "offers": {
      "@type": "Offer",
      "price": "50.00",
      "priceCurrency": "USD",
      "priceSpecification": {
        "@type": "PriceSpecification",
        "price": "50.00",
        "priceCurrency": "USD",
        "valueAddedTaxIncluded": true
      },
      "availability": "https://schema.org/InStock",
      "validFrom": "2025-01-01",
      "priceValidUntil": "2025-12-31",
      "url": "https://jahongirtravel.com/tours/samarkand-city-tour",
      "seller": {
        "@type": "Organization",
        "name": "Jahongir Travel"
      }
    },
    "includesObject": [
      {
        "@type": "Thing",
        "name": "Hotel pickup and drop-off"
      },
      {
        "@type": "Thing",
        "name": "Professional guide"
      },
      {
        "@type": "Thing",
        "name": "Entrance fees to monuments"
      },
      {
        "@type": "Thing",
        "name": "Bottled water"
      }
    ],
    "additionalInfo": "Not included: Tips, personal expenses, lunch",
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "5.0",
      "reviewCount": "47",
      "bestRating": "5",
      "worstRating": "1"
    },
    "review": [
      {
        "@type": "Review",
        "reviewRating": {
          "@type": "Rating",
          "ratingValue": "5",
          "bestRating": "5"
        },
        "author": {
          "@type": "Person",
          "name": "Sarah Mitchell"
        },
        "datePublished": "2024-10-15",
        "reviewBody": "Absolutely breathtaking! Our guide was incredibly knowledgeable about the history of Samarkand and made every monument come alive with stories. The Registan Square at sunset was magical. Highly recommend this tour to anyone visiting Uzbekistan."
      },
      {
        "@type": "Review",
        "reviewRating": {
          "@type": "Rating",
          "ratingValue": "5",
          "bestRating": "5"
        },
        "author": {
          "@type": "Person",
          "name": "James Chen"
        },
        "datePublished": "2024-09-28",
        "reviewBody": "Perfect tour for photography enthusiasts! Our guide knew all the best spots and angles for photos. The Shah-i-Zinda necropolis was stunning with its blue tiles. Great pace, not rushed at all. Worth every penny!"
      },
      {
        "@type": "Review",
        "reviewRating": {
          "@type": "Rating",
          "ratingValue": "5",
          "bestRating": "5"
        },
        "author": {
          "@type": "Person",
          "name": "Emma Dubois"
        },
        "datePublished": "2024-08-12",
        "reviewBody": "An unforgettable experience! The guide spoke excellent French and English. The small group size meant we could ask lots of questions. Bibi-Khanym Mosque was incredible. Hotel pickup was punctual. Thank you, Jahongir Travel!"
      },
      {
        "@type": "Review",
        "reviewRating": {
          "@type": "Rating",
          "ratingValue": "5",
          "bestRating": "5"
        },
        "author": {
          "@type": "Person",
          "name": "Michael O'Brien"
        },
        "datePublished": "2024-07-22",
        "reviewBody": "Best tour we did in Central Asia! The architecture is mind-blowing and our guide's passion for Samarkand's history was contagious. Four hours flew by. Great value for money. Would definitely book with Jahongir Travel again."
      }
    ]
  }
  </script>
  <link rel="stylesheet" href="css/gallery-lightbox.css">
  <link rel="stylesheet" href="tour-details-gallery-addon.css">
</head>
<body>

  <!-- Skip to Main Content (Accessibility) -->
  <a href="#main-content" class="skip-link">Skip to main content</a>

  <!-- =====================================================
       SITE HEADER / NAVIGATION
       ===================================================== -->
  <header class="site-header" role="banner">
    <!-- Navigation Bar -->
    <nav class="nav" aria-label="Main navigation">
      <div class="container">
        <a href="/" class="nav__logo">
          <span class="nav__logo-text">Jahongir <strong>Travel</strong></span>
        </a>

        <ul class="nav__menu" id="navMenu">
          <li><a href="/">Home</a></li>
          <li><a href="/tours/">Tours</a></li>
          <li><a href="/destinations/">Destinations</a></li>
          <li><a href="/about/">About Us</a></li>
          <li><a href="/contact/">Contact</a></li>
        </ul>

        <a href="tel:+998915550808" class="btn btn--accent nav__cta">
          <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M6.62 10.79a15.09 15.09 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.24 11.72 11.72 0 003.67.59 1 1 0 011 1v3.54a1 1 0 01-1 1A18.5 18.5 0 013 5a1 1 0 011-1h3.55a1 1 0 011 1 11.72 11.72 0 00.59 3.67 1 1 0 01-.25 1.11z"/>
          </svg>
          +998 91 555 08 08
        </a>

        <button class="nav__toggle" id="navToggle" aria-label="Toggle navigation menu" aria-expanded="false">
          <span class="nav__toggle-icon"></span>
        </button>
      </div>
    </nav>
  </header>

  <!-- =====================================================
       SECTION 2: TOUR HEADER INFO (Title, Rating, Meta, Tabs)
       ===================================================== -->
  <section class="tour-header"
           hx-get="{{ url('/partials/tours/samarkand-city-toursregistan-square-and-historical/hero') }}"
           hx-trigger="load"
           hx-swap="innerHTML"
           data-tour-slug="samarkand-city-toursregistan-square-and-historical">

    <!-- Loading Skeleton -->
    <div class="container">
      <div class="skeleton skeleton--text" style="width: 40%; height: 16px; margin-bottom: 1rem;"></div>
      <div class="skeleton skeleton--title" style="height: 40px; width: 80%; margin-bottom: 1rem;"></div>
      <div class="skeleton skeleton--text" style="width: 30%; height: 20px;"></div>
    </div>

  </section>

  <!-- =====================================================
       SECTION 3: TOUR HERO GALLERY
       ===================================================== -->
  <section class="tour-hero">
    <div class="container">

      <!-- Skeleton Loader (hidden once loaded) -->
      <div class="tour-hero__skeleton" aria-hidden="true">
        <div class="skeleton skeleton--hero"></div>
        <div class="skeleton skeleton--thumbnails">
          <div class="skeleton skeleton--thumb"></div>
          <div class="skeleton skeleton--thumb"></div>
          <div class="skeleton skeleton--thumb"></div>
          <div class="skeleton skeleton--thumb"></div>
        </div>
      </div>

      <!-- Actual Gallery -->
      <!-- Actual Gallery - Loaded via HTMX -->
      <div class="tour-hero__gallery is-hidden"
           hx-get=""
           hx-trigger="load"
           hx-swap="innerHTML"
           data-tour-slug="samarkand-city-toursregistan-square-and-historical">
      </div>
    </div>
  </section>

  <!-- Section Navigation with Arrows -->
  <div class="container section-nav-wrapper">
    <nav class="section-nav" aria-label="Tour sections">
      <button class="section-nav__btn section-nav__btn--prev" aria-label="Scroll left" hidden>
        ‹
      </button>

      <div class="section-nav__scroller" id="sectionScroller">
        <a href="#overview" class="is-active">Overview</a>
        <a href="#highlights">Highlights</a>
        <a href="#includes">Included</a>
        <a href="#cancellation">Cancellation</a>
        <a href="#itinerary">Itinerary</a>
        <a href="#meeting-point">Meeting Point</a>
        <a href="#know-before">Know Before</a>
        <a href="#faq">FAQ</a>
      </div>

      <button class="section-nav__btn section-nav__btn--next" aria-label="Scroll right" hidden>
        ›
      </button>
    </nav>
  </div>


  <!-- =====================================================
       TWO-COLUMN LAYOUT: MAIN CONTENT + BOOKING SIDEBAR
       ===================================================== -->
  <div class="tour-content-wrapper">
    <div class="container">
      <div class="tour-layout">

        <!-- LEFT COLUMN: Main Tour Content -->
        <main class="tour-main-content" id="main-content">

          <!-- Overview Section -->
          <section class="tour-overview" id="overview"
                   hx-get="{{ url('/partials/tours/samarkand-city-toursregistan-square-and-historical/overview') }}"
                   hx-trigger="load"
                   hx-swap="innerHTML"
                   data-tour-slug="samarkand-city-toursregistan-square-and-historical">

            <!-- Loading Skeleton -->
            <h2 class="section-title">Overview</h2>
            <div class="skeleton skeleton--text" style="width: 90%; height: 16px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton--text" style="width: 85%; height: 16px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton--text" style="width: 92%; height: 16px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton--text" style="width: 88%; height: 16px; margin-bottom: 0.5rem;"></div>

          </section>

          <!-- Highlights Section -->
          <section class="tour-highlights" id="highlights"
                   hx-get="{{ url('/partials/tours/samarkand-city-toursregistan-square-and-historical/highlights') }}"
                   hx-trigger="revealed"
                   hx-swap="innerHTML"
                   data-tour-slug="samarkand-city-toursregistan-square-and-historical">

            <!-- Loading Skeleton -->
            <h2 class="section-title">Highlights</h2>
            <div class="skeleton skeleton--text" style="width: 95%; height: 16px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton--text" style="width: 92%; height: 16px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton--text" style="width: 88%; height: 16px; margin-bottom: 0.5rem;"></div>

          </section>

          <!-- Includes/Excludes Section -->
          <section class="tour-includes-excludes" id="includes"
                   hx-get="{{ url('/partials/tours/samarkand-city-toursregistan-square-and-historical/included-excluded') }}"
                   hx-trigger="revealed"
                   hx-swap="innerHTML"
                   data-tour-slug="samarkand-city-toursregistan-square-and-historical">

            <!-- Loading Skeleton -->
            <h2 class="section-title">What's Included & Excluded</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
              <div>
                <div class="skeleton skeleton--text" style="width: 90%; height: 16px; margin-bottom: 0.5rem;"></div>
                <div class="skeleton skeleton--text" style="width: 85%; height: 16px; margin-bottom: 0.5rem;"></div>
                <div class="skeleton skeleton--text" style="width: 92%; height: 16px; margin-bottom: 0.5rem;"></div>
              </div>
              <div>
                <div class="skeleton skeleton--text" style="width: 88%; height: 16px; margin-bottom: 0.5rem;"></div>
                <div class="skeleton skeleton--text" style="width: 93%; height: 16px; margin-bottom: 0.5rem;"></div>
                <div class="skeleton skeleton--text" style="width: 87%; height: 16px; margin-bottom: 0.5rem;"></div>
              </div>
            </div>

          </section>

          <!-- Cancellation Policy Section -->
          <section class="tour-cancellation" id="cancellation"
                   data-tour-slug="samarkand-city-toursregistan-square-and-historical"
                   hx-get="{{ url('/partials/tours/samarkand-city-toursregistan-square-and-historical/cancellation') }}"
                   hx-trigger="revealed"
                   hx-swap="innerHTML">
            <div class="loading-spinner">Loading cancellation policy...</div>
          </section>

          <!-- Itinerary Section -->
          <section class="tour-itinerary" id="itinerary"
                   hx-get="{{ url('/partials/tours/samarkand-city-toursregistan-square-and-historical/itinerary') }}"
                   hx-trigger="revealed"
                   hx-swap="innerHTML"
                   aria-label="Tour itinerary"
                   data-tour-slug="samarkand-city-toursregistan-square-and-historical">

            <!-- Loading Skeleton -->
            <div class="itinerary-header">
              <h2 class="section-title">Tour Itinerary</h2>
            </div>
            <div class="skeleton skeleton--text" style="width: 92%; height: 16px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton--text" style="width: 88%; height: 16px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton--text" style="width: 90%; height: 16px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton--text" style="width: 85%; height: 16px; margin-bottom: 0.5rem;"></div>

          </section>

          <!-- Meeting Point & Pickup Section -->
          <section class="tour-meeting" id="meeting-point">
            <h2 class="section-title">Meeting Point & Pickup</h2>

            <div class="meeting-grid">
              <div class="meeting-info">
                <div class="meeting-info__item">
                  <svg class="icon icon--hotel" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3 2a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V4a2 2 0 00-2-2H3zm0 2h14v10H3V4zm2 2v6h10V6H5zm2 2h6v2H7V8z"/></svg>
                  <div>
                    <h3>Hotel Pickup Included</h3>
                    <p>Free pickup from any hotel within Samarkand city center (5km radius). Please provide your hotel name when booking.</p>
                  </div>
                </div>

                <div class="meeting-info__item">
                  <svg class="icon icon--map-marker" width="16" height="20" viewBox="0 0 16 20" fill="currentColor" aria-hidden="true"><path d="M8 0C3.589 0 0 3.589 0 8c0 7 8 12 8 12s8-5 8-12c0-4.411-3.589-8-8-8zm0 11a3 3 0 110-6 3 3 0 010 6z"/></svg>
                  <div>
                    <h3>Alternative Meeting Point</h3>
                    <p>If you prefer, you can meet us at <strong>Registan Square West Gate</strong> at 09:30 AM. Look for your guide holding a "Jahongir Travel" sign.</p>
                    <p><strong>Address:</strong> Registan Street, Samarkand 140100, Uzbekistan</p>
                  </div>
                </div>
              </div>

              <!-- Google Map Embed -->
              <div class="meeting-map" aria-label="Map to meeting point">
                <iframe
                  loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3071.234567890123!2d66.97567890000001!3d39.65444440000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3f4d191506193833%3A0x594b01f4e2303d38!2sRegistan!5e0!3m2!1sen!2s!4v1234567890123!5m2!1sen!2s"
                  width="600"
                  height="360"
                  style="border:0;"
                  allowfullscreen=""
                  title="Map showing Registan Square meeting point">
                </iframe>
              </div>
            </div>
          </section>

          <!-- Know Before You Go Section -->
          <section class="tour-know-before" id="know-before"
                   hx-get="{{ url('/partials/tours/5-day-silk-road-classic/requirements') }}"
                   hx-trigger="revealed"
                   hx-swap="innerHTML"
                   data-tour-slug="samarkand-city-toursregistan-square-and-historical">

            <!-- Loading Skeleton -->
            <h2 class="section-title">Know Before You Go</h2>
            <div class="skeleton skeleton--text" style="width: 95%; height: 16px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 92%; height: 16px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 90%; height: 16px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 88%; height: 16px; margin-bottom: 1rem;"></div>

          </section>

          <!-- FAQ Section -->
          <section class="tour-faq" id="faq"
                   hx-get="{{ url('/partials/tours/samarkand-city-toursregistan-square-and-historical/faqs') }}"
                   hx-trigger="revealed"
                   hx-swap="innerHTML"
                   data-tour-slug="samarkand-city-toursregistan-square-and-historical">

            <!-- Loading Skeleton -->
            <h2 class="section-title">Frequently Asked Questions</h2>
            <div class="skeleton skeleton--text" style="width: 95%; height: 16px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 92%; height: 16px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 90%; height: 16px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 88%; height: 16px; margin-bottom: 1rem;"></div>

          </section>

          <!-- Extra Services Section -->
          <section class="tour-extras" id="extras"
                   hx-get="{{ url('/partials/tours/samarkand-city-toursregistan-square-and-historical/extras') }}"
                   hx-trigger="revealed"
                   hx-swap="innerHTML"
                   data-tour-slug="samarkand-city-toursregistan-square-and-historical">

            <!-- Loading Skeleton -->
            <h2 class="section-title">Extra Services</h2>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
              <div class="skeleton skeleton--thumb"></div>
              <div class="skeleton skeleton--thumb"></div>
              <div class="skeleton skeleton--thumb"></div>
              <div class="skeleton skeleton--thumb"></div>
            </div>

          </section>

          <!-- Customer Reviews Section -->
          <section class="tour-reviews" id="reviews"
                   hx-get="{{ url('/partials/tours/samarkand-city-toursregistan-square-and-historical/reviews') }}"
                   hx-trigger="revealed"
                   hx-swap="innerHTML"
                   data-tour-slug="samarkand-city-toursregistan-square-and-historical">

            <!-- Loading Skeleton -->
            <div class="reviews-header">
              <h2 class="section-title">Customer Reviews</h2>
            </div>
            <div class="skeleton skeleton--text" style="width: 100%; height: 80px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 100%; height: 80px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 100%; height: 80px; margin-bottom: 1rem;"></div>

          </section>

        </main>

        <!-- RIGHT COLUMN: Booking Sidebar -->
        <aside class="booking-sidebar" data-sticky="true">

          <!-- Tour Data for JavaScript -->
          <script type="application/json" id="tour-data">
            {
              "id": "samarkand-city-tour",
              "name": "Samarkand City Tour: Registan Square and Historic Monuments",
              "pricePerPerson": 50.00,
              "currency": "USD",
              "maxGuests": 10,
              "minGuests": 1,
              "duration": "4 hours"
            }
          </script>

          <!-- Screen Reader Live Region for Dynamic Updates -->
          <div aria-live="polite" aria-atomic="true" class="sr-only" id="booking-status"></div>

          <!-- Skeleton Loader -->
          <div class="booking-sidebar__skeleton" aria-hidden="true">
            <div class="skeleton skeleton--sidebar-top"></div>
            <div class="skeleton skeleton--sidebar-inputs"></div>
            <div class="skeleton skeleton--sidebar-buttons"></div>
          </div>

          <!-- Booking Card -->
          <div class="booking-card">

            <!-- Price Header -->
            <div class="booking-card__header">
              <div class="booking-price">
                <span class="price-label">from</span>
                <span class="price-amount" data-base-price="50.00">$50.00</span>
                <span class="price-unit">/person</span>
              </div>
            </div>

            <!-- Booking Form -->
            <form class="booking-form" id="booking-form" data-form-type="booking">

              <!-- Date Picker -->
              <div class="form-group">
                <label for="tour-date" class="form-label">
                  Date
                </label>
                <input
                  type="date"
                  id="tour-date"
                  name="tour-date"
                  class="form-input"
                  required
                  aria-required="true"
                  aria-describedby="date-hint date-error"
                  autocomplete="off"
                  min=""
                  data-min-date-offset="1">
                <span id="date-hint" class="form-hint">Select your preferred tour date (at least 24 hours in advance)</span>
                <span id="date-error" class="form-error" role="alert"></span>
              </div>

              <!-- Guest Selector -->
              <div class="form-group">
                <label for="tour-guests" class="form-label">
                  Guest
                </label>
                <select
                  id="tour-guests"
                  name="tour-guests"
                  class="form-input"
                  required
                  aria-required="true"
                  aria-describedby="guests-hint guests-error"
                  autocomplete="off"
                  data-price-calculator="true">
                  <option value="1">1 guest</option>
                  <option value="2" selected>2 guests</option>
                  <option value="3">3 guests</option>
                  <option value="4">4 guests</option>
                  <option value="5">5 guests</option>
                  <option value="6">6 guests</option>
                  <option value="7">7 guests</option>
                  <option value="8">8 guests</option>
                  <option value="9">9 guests</option>
                  <option value="10">10 guests</option>
                </select>
                <span id="guests-hint" class="form-hint">Maximum 10 guests per booking</span>
                <span id="guests-error" class="form-error" role="alert"></span>
              </div>

              <!-- Action Button -->
              <div class="form-actions">
                <button type="button" class="btn btn--primary btn--block" id="check-availability">
                  Check availability
                </button>
              </div>

            </form>

            <!-- Price Breakdown -->
            <div class="price-breakdown" data-breakdown-visible="true">
              <h3 class="breakdown-title">Price Breakdown</h3>
              <div class="breakdown-items">
                <div class="breakdown-item">
                  <span class="breakdown-label">
                    <span class="breakdown-guests" data-guests="2">2 guests</span> ×
                    <span class="breakdown-unit-price" data-unit-price="50.00">$50.00</span>
                  </span>
                  <span class="breakdown-value" data-subtotal="100.00">$100.00</span>
                </div>
                <div class="breakdown-item breakdown-item--total">
                  <span class="breakdown-label">Total</span>
                  <span class="breakdown-value breakdown-total" data-total="100.00">$100.00</span>
                </div>
              </div>
              <p class="breakdown-note">Free cancellation up to 24 hours before the tour</p>
            </div>

            <!-- Trust Badges -->
            <div class="trust-badges">
              <div class="badge-item">
                <svg class="icon icon--shield" width="18" height="20" viewBox="0 0 18 20" fill="currentColor" aria-hidden="true"><path d="M9 0L0 3v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V3L9 0zm0 2.18l7 2.09v5.23c0 4.65-3.19 8.98-7 10.05-3.81-1.07-7-5.4-7-10.05V4.27l7-2.09z"/></svg>
                <span>Secure payments</span>
              </div>
              <div class="badge-item">
                <svg class="icon icon--headset" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 0C4.5 0 0 4.5 0 10v5a3 3 0 003 3h2v-8H2v-2a8 8 0 1116 0v2h-3v8h2a3 3 0 003-3v-5c0-5.5-4.5-10-10-10z"/></svg>
                <span>24/7 support</span>
              </div>
              <div class="badge-item">
                <svg class="icon icon--undo" width="18" height="18" viewBox="0 0 18 18" fill="currentColor" aria-hidden="true"><path d="M2 8a6 6 0 1110.89 3.5.5.5 0 10.78.63A7 7 0 103 8h3l-4-4-4 4h3z"/></svg>
                <span>Free cancellation</span>
              </div>
            </div>

            <!-- Booking Clarification -->
            <div class="booking-clarification">
              <p class="clarification-text">
                <svg class="icon icon--clock" width="18" height="18" viewBox="0 0 18 18" fill="currentColor" aria-hidden="true"><path d="M9 0a9 9 0 100 18A9 9 0 009 0zm4 10H9a1 1 0 01-1-1V4a1 1 0 112 0v4h3a1 1 0 010 2z"/></svg>
                <strong>Confirmation within 24 hours</strong> — we'll verify availability and send you a confirmation.
              </p>
              <p class="clarification-note">
                No payment required now. Pay after confirmation.
              </p>
            </div>

            <!-- Request to Book Button -->
            <button type="button" class="btn btn--accent btn--block btn--large" id="request-booking">
              <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/></svg>
              Request to Book
            </button>

            <!-- Why Book Section -->
            <div class="booking-benefits">
              <h3 class="benefits-title">Why book with Jahongir Travel?</h3>
              <ul class="benefits-list">
                <li class="benefit-item">
                  <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/></svg>
                  <span>Best price guarantee</span>
                </li>
                <li class="benefit-item">
                  <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/></svg>
                  <span>Free cancellation (24h)</span>
                </li>
                <li class="benefit-item">
                  <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/></svg>
                  <span>Expert local guides</span>
                </li>
                <li class="benefit-item">
                  <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/></svg>
                  <span>24/7 customer support</span>
                </li>
              </ul>
            </div>

            <!-- WhatsApp Contact -->
            <div class="booking-contact">
              <p class="contact-text">Questions? Contact us directly:</p>
              <a href="https://wa.me/998901234567" class="btn btn--whatsapp btn--block" target="_blank" rel="noopener noreferrer">
                <svg class="icon icon--whatsapp" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.05 0C4.5 0 0 4.48 0 10c0 1.77.46 3.43 1.27 4.87L.05 19.1l4.35-1.14A9.95 9.95 0 0010.05 20c5.52 0 10-4.48 10-10s-4.48-10-10-10zm5.89 14.21c-.24.68-1.41 1.28-1.95 1.35-.51.06-1.02.24-3.44-.74-3.11-1.25-5.11-4.39-5.27-4.59-.15-.2-1.24-1.65-1.24-3.15s.78-2.23 1.06-2.53c.28-.3.61-.38.81-.38.2 0 .41.01.59.01.19 0 .44-.07.69.52.25.61.86 2.1.93 2.25.08.15.13.33.03.53-.1.2-.15.33-.3.51-.15.18-.32.4-.46.54-.15.15-.31.31-.13.61.18.3.79 1.31 1.7 2.12 1.17 1.04 2.16 1.37 2.46 1.52.3.15.48.13.66-.08.18-.2.76-.89.96-1.19.2-.3.41-.25.69-.15.28.1 1.78.84 2.08.99.3.15.5.23.58.35.07.13.07.74-.17 1.42z"/></svg>
                WhatsApp: +998 90 123 45 67
              </a>
            </div>

          </div>

        </aside>

      </div>
    </div>
  </div>

  <!-- Mobile Floating CTA (Task 13) -->
  <!-- Hidden on desktop, visible on mobile/tablet only via CSS -->
  <div class="mobile-booking-cta" data-mobile-only="true">
    <div class="mobile-cta__container">
      <div class="mobile-cta__price">
        <span class="mobile-cta__amount" data-mobile-price="50.00">$50.00</span>
        <span class="mobile-cta__unit">per person</span>
      </div>
      <button type="button" class="btn btn--accent mobile-cta__button" data-scroll-to="booking-form" aria-label="Scroll to booking form">
        <svg class="icon icon--calendar-check" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6 2a2 2 0 00-2 2v1H2a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2h-2V4a2 2 0 00-2-2H6zm1 2h4v2H7V4zM2 9h14v8H2V9zm11.707 1.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
        Book Now
      </button>
    </div>
  </div>

  <!-- =====================================================
       FOOTER
       ===================================================== -->
  <footer class="site-footer">
    <div class="container footer-main footer-main--desktop">
      <div class="footer-brand">
        <div class="footer-brand__text">Jahongir Travel</div>
        <p>Tailor-made Uzbekistan tours since 2012.</p>
        <p><a href="mailto:info@jahongir-travel.uz">info@jahongir-travel.uz</a></p>
        <p><a href="tel:+998915550808">+998 91 555 08 08</a></p>
        <p>Samarkand, Chirokchi 4</p>
      </div>

      <nav class="footer-col footer-nav" aria-label="Company">
        <div class="footer-nav__title">Company</div>
        <ul class="footer-nav__list">
          <li><a href="/about/">About us</a></li>
          <li><a href="/careers/">Careers</a></li>
          <li><a href="/blog/">Blog</a></li>
          <li><a href="/partners/">Partner</a></li>
          <li><a href="/contact/">Contact</a></li>
        </ul>
      </nav>

      <nav class="footer-col footer-nav" aria-label="Services">
        <div class="footer-nav__title">Services</div>
        <ul class="footer-nav__list">
          <li><a href="/tours/">Tour booking</a></li>
          <li><a href="/visa/">Visa online</a></li>
          <li><a href="/guides/">Travel guide</a></li>
          <li><a href="/car-service/">Car service</a></li>
          <li><a href="/sim/">SIM &amp; eSIM</a></li>
        </ul>
      </nav>

      <nav class="footer-col footer-nav" aria-label="Help">
        <div class="footer-nav__title">Need help?</div>
        <ul class="footer-nav__list">
          <li><a href="/faqs/">FAQs</a></li>
          <li><a href="/support/">Customer care</a></li>
          <li><a href="/safety/">Safety tips</a></li>
          <li><a href="/privacy/">Privacy policy</a></li>
          <li><a href="/terms/">Terms of use</a></li>
        </ul>
      </nav>

      <div class="footer-col">
        <div class="footer-nav__title">Connect</div>
        <ul class="footer-social__list">
          <li><a href="https://facebook.com/jahongirtravel" target="_blank" rel="noopener noreferrer">Facebook</a></li>
          <li><a href="https://instagram.com/jahongirtravel" target="_blank" rel="noopener noreferrer">Instagram</a></li>
          <li><a href="https://twitter.com/jahongirtravel" target="_blank" rel="noopener noreferrer">Twitter</a></li>
          <li><a href="https://youtube.com/@jahongirtravel" target="_blank" rel="noopener noreferrer">YouTube</a></li>
          <li><a href="https://pinterest.com/jahongirtravel" target="_blank" rel="noopener noreferrer">Pinterest</a></li>
        </ul>
      </div>
    </div>

    <div class="container footer-bottom">
      <div class="footer-bottom__wrap">
        <div>© 2025 Jahongir Travel. All rights reserved.</div>
        <div class="footer-bottom__legal">
          <a href="/privacy/">Privacy</a>
          <span> • </span>
          <a href="/terms/">Terms</a>
          <span> • </span>
          <a href="/cookies/">Cookies</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- =====================================================
       JAVASCRIPT
       ===================================================== -->
  <!-- Dynamic Tour Slug Detection (MUST run before HTMX) -->
  <script>
    (function() {
      // Get tour slug from server-side rendering
      const tourSlug = '{{ $tour->slug }}';
      
      console.log('[Tour] Slug from server:', tourSlug);

      if (tourSlug) {
        // Update all sections with dynamic tour slug
        const sections = document.querySelectorAll('[data-tour-slug]');
        const backendUrl = '{{ url("/partials/tours") }}';

        sections.forEach(function(section) {
          // Map section class names to partial endpoint names
          const classToPartialMap = {
            'tour-header': 'hero',
            'tour-hero__gallery': 'gallery',
            'tour-overview': 'overview',
            'tour-highlights': 'highlights',
            'tour-includes-excludes': 'included-excluded',
            'tour-cancellation': 'cancellation',
            'tour-itinerary': 'itinerary',
            'tour-faq': 'faqs',
            'tour-extras': 'extras',
            'tour-reviews': 'reviews',
            'tour-know-before': 'requirements'
          };

          // Get the first class name from the element
          const className = section.className.split(' ')[0];
          const partialName = classToPartialMap[className];

          if (partialName) {
            const dynamicUrl = backendUrl + '/' + tourSlug + '/' + partialName;
            section.setAttribute('hx-get', dynamicUrl);
            section.setAttribute('data-tour-slug', tourSlug);
            console.log('[Tour] Updated ' + className + ' → ' + dynamicUrl);
          } else {
            console.warn('[Tour] Unknown section class:', className);
          }
        });
      }
    })();
  </script>

  <!-- HTMX Library -->
  <script src="js/htmx.min.js"></script>

  <!-- HTMX Debug Script -->
  <script>
    console.log('[HTMX] Page loaded - tour-details.html');
    console.log('[HTMX] Backend URL: {{ url(\"/\") }}');

    // HTMX event listeners for debugging and error handling
    document.body.addEventListener('htmx:beforeRequest', function(evt) {
      console.log('[HTMX] Loading:', evt.detail.pathInfo.requestPath);
    });

    document.body.addEventListener('htmx:afterSwap', function(evt) {
      console.log('[HTMX] Loaded successfully:', evt.detail.pathInfo.requestPath);
    });

    document.body.addEventListener('htmx:responseError', function(evt) {
      console.error('[HTMX] Error:', evt.detail.pathInfo.requestPath, 'Status:', evt.detail.xhr.status);
      evt.detail.target.innerHTML = '<div style="padding:20px;background:#fee;border:1px solid #c33;color:#c33;">Failed to load content. Please refresh the page.</div>';
    });

    document.body.addEventListener('htmx:sendError', function(evt) {
      console.error('[HTMX] Network error:', evt.detail);
    });
  </script>

  <!-- Tour Details JavaScript -->
  <script src="tour-details.js" defer></script>
  <script src="js/gallery-lightbox.js" defer></script>

  <!-- Tour Reviews JavaScript -->
  <script src="{{ asset('js/tour-reviews.js') }}" defer></script>

  <!-- Main JavaScript -->
  <script src="js/main.js" defer></script>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/998915550808?text=Hi!%20I'm%20interested%20in%20learning%20more%20about%20your%20tours%20in%20Uzbekistan."
       class="whatsapp-float"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="Chat with us on WhatsApp">
        <i class="fab fa-whatsapp"></i>
        <span class="whatsapp-float__tooltip">Chat with us!</span>
    </a>

</body>
</html>
