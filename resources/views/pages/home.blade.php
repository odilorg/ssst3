@extends('layouts.main')

@section('title', 'Jahongir Travel - Discover the Magic of Uzbekistan | Silk Road Tours')
@section('meta_description', 'Discover Uzbekistan with Jahongir Travel - Expert guided tours of the ancient Silk Road, featuring Samarkand, Bukhara, Khiva, and more.')
@section('meta_keywords', 'Uzbekistan tours, Silk Road travel, Samarkand tours, Bukhara, Khiva, Central Asia travel')
@section('canonical', url('/'))

@section('structured_data')
[
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
  },
  {
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
      {
        "@@type": "Question",
        "name": "{{ __('ui.faq_items.prior_experience.question') }}",
        "acceptedAnswer": {
          "@@type": "Answer",
          "text": "No prior experience required! Our workshops are designed for complete beginners through advanced practitioners. Skilled artisans adapt their teaching to your skill level, ensuring everyone creates something meaningful. Whether you've never touched clay or you're a practicing potter, you'll learn traditional Uzbek techniques that date back centuries."
        }
      },
      {
        "@@type": "Question",
        "name": "{{ __('ui.faq_items.children.question') }}",
        "acceptedAnswer": {
          "@@type": "Answer",
          "text": "Yes, for ages 12 and up. Craft workshops are hands-on and engaging for teens and adults. Children aged 12-17 must be accompanied by a participating adult. For families with younger children (under 12), we offer customized private workshops where artisans can adapt activities to suit all ages."
        }
      },
      {
        "@@type": "Question",
        "name": "{{ __('ui.faq_items.languages.question') }}",
        "acceptedAnswer": {
          "@@type": "Answer",
          "text": "All workshops include English-speaking guides. While artisans typically speak Uzbek or Russian, our expert guides translate technical instruction and cultural context in real-time. Russian-speaking travelers can request direct instruction in Russian."
        }
      },
      {
        "@@type": "Question",
        "name": "{{ __('ui.faq_items.private.question') }}",
        "acceptedAnswer": {
          "@@type": "Answer",
          "text": "Absolutely! Private workshops are available for individuals, couples, or small groups (up to 6 people). You get one-on-one time with skilled artisans and a fully customized itinerary. Private journeys start at $1,290 per person (2-3 participants) and include personalized instruction, flexible scheduling, and custom craft combinations."
        }
      },
      {
        "@@type": "Question",
        "name": "{{ __('ui.faq_items.whats_included.question') }}",
        "acceptedAnswer": {
          "@@type": "Answer",
          "text": "Everything you need for an immersive experience: Instruction from skilled artisans (12+ hours), all materials tools and kiln/loom fees, English-speaking expert guides, transportation between workshop locations, artisan homestay accommodation (2 nights), traditional meals with host families, and your finished craft pieces to take home. Not included: International flights, travel insurance, personal expenses, and optional activities outside the workshop schedule."
        }
      },
      {
        "@@type": "Question",
        "name": "{{ __('ui.faq_items.cancellation.question') }}",
        "acceptedAnswer": {
          "@@type": "Answer",
          "text": "Free cancellation up to 14 days before departure with a full refund minus payment processing fees (3%). Cancellations 7-14 days before: 50% refund. Cancellations less than 7 days: No refund (but you can transfer to a future workshop date within 12 months). We strongly recommend purchasing travel insurance to protect your investment against unforeseen circumstances."
        }
      }
    ]
  }
]

  <!-- ========================================
       MOBILE STICKY CTA
       ======================================== -->
  <div class="mobile-sticky-cta">
    <a href="{{ url('/' . app()->getLocale() . '/tours') }}" class="mobile-sticky-cta__button">
      <i class="fas fa-calendar-check" aria-hidden="true"></i>
{{ __('ui.mobile_cta.check_availability') }}
    </a>
  </div>

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
      font-size: var(--text-sm);
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
      font-weight: var(--weight-semibold);
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
        font-size: var(--text-base); /* Was 0.9375rem */ /* Slightly larger on mobile */
      }
    }

    /* Tablet Responsive */
    @media (min-width: 501px) and (max-width: 768px) {
      .places__grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
      }
    }

    /* ========================================
       HIGH-IMPACT UI/UX FIXES
       ======================================== */

    /* Fix 2: Strengthen Heading Typography (Removed !important) */
    .why-us__intro h2 {
      font-size: var(--text-4xl); /* 36px mobile */
      font-weight: var(--weight-bold); /* Bold */
      line-height: 1.2;
      color: #1a1a1a; /* Darker for better contrast */
      margin-bottom: 1rem;
      letter-spacing: -0.01em; /* Slightly tighter for large headings */
    }

    @media (min-width: 768px) {
      .why-us__intro h2 {
        font-size: var(--text-5xl); /* Was 2.75rem */ /* 44px desktop */
      }
    }

    /* Fix 3: Social Proof Styling */
    .why-us__social-proof {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
      margin: 1.5rem 0;
      padding: 1rem 0;
    }

    .rating-badge {
      display: flex;
      align-items: center;
      gap: 8px;
      background: #f8f9fa;
      padding: 8px 16px;
      border-radius: 6px;
      border: 1px solid #e9ecef;
    }

    .rating-badge .stars {
      color: #fbbf24;
      font-size: var(--text-base);
      letter-spacing: 2px;
    }

    .rating-badge strong {
      font-size: var(--text-lg);
      font-weight: var(--weight-bold);
      color: #1a1a1a;
    }

    .rating-badge .rating-source {
      font-size: var(--text-sm);
      color: #6b7280;
    }

    .social-proof-separator {
      color: #d1d5db;
      font-weight: var(--weight-regular); /* Changed from 300 for font optimization */
    }

    .social-proof-stat {
      font-size: var(--text-base); /* Was 0.9375rem */
      color: #4b5563;
      font-weight: var(--weight-medium);
    }

    /* Fix 1: Enhanced CTA Button */
    .btn--hero {
      background: linear-gradient(135deg, #D2691E 0%, #A0522D 100%);
      color: white;
      font-size: var(--text-lg);
      padding: 1rem 2rem;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(210, 105, 30, 0.3);
      transition: all 0.3s ease;
      border: none;
      font-weight: var(--weight-semibold);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .btn--hero:hover {
      background: linear-gradient(135deg, #A0522D 0%, #8B4513 100%);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(210, 105, 30, 0.4);
    }

    .cta-subtext {
      margin-top: 12px;
      font-size: var(--text-sm);
      color: #6b7280;
      text-align: center;
    }

    /* Fix 4: Pricing Preview */
    .pricing-preview {
      background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
      border-left: 4px solid #D2691E;
      padding: 1.25rem 1.5rem;
      border-radius: 8px;
      margin: 1.5rem 0;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .pricing-preview__label {
      font-size: var(--text-xs); /* Was 0.8125rem */
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #92400e;
      font-weight: var(--weight-semibold);
      margin: 0 0 4px 0;
    }

    .pricing-preview__amount {
      font-size: var(--text-4xl); /* Was 2rem */
      font-weight: var(--weight-bold);
      color: #1a1a1a;
      margin: 0;
      line-height: 1;
    }

    .pricing-preview__duration {
      font-size: var(--text-base);
      font-weight: var(--weight-regular);
      color: #6b7280;
    }

    .pricing-preview__link {
      display: inline-block;
      margin-top: 8px;
      font-size: var(--text-base); /* Was 0.9375rem */
      color: #D2691E;
      font-weight: var(--weight-semibold);
      text-decoration: none;
      transition: color 0.2s ease;
    }

    .pricing-preview__link:hover {
      color: #A0522D;
      text-decoration: underline;
    }

    /* Responsive Adjustments */
    @media (max-width: 767px) {
      .why-us__social-proof {
        flex-direction: column;
        align-items: flex-start;
      }

      .pricing-preview {
        padding: 1rem 1.25rem;
      }

      .pricing-preview__amount {
        font-size: var(--text-3xl); /* Was 1.75rem */
      }
    }

    /* ========================================
       SINGLE HERO IMAGE LAYOUT (Option B)
       ======================================== */

    /* Hero Image Container */
    .why-us__media--hero {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    .why-us__hero-image {
      width: 100%;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .why-us__hero-image:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.16);
    }

    .why-us__hero-image img {
      width: 100%;
      height: auto;
      display: block;
      object-fit: cover;
      aspect-ratio: 4 / 3;
    }

    /* Inline Credentials Below Image */
    .why-us__credentials {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-wrap: wrap;
      gap: 12px 16px;
      padding: 1rem;
      background: #f8f9fa;
      border-radius: 8px;
      border: 1px solid #e9ecef;
    }

    .credential-item {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: var(--text-sm);
      color: #4b5563;
      font-weight: var(--weight-medium);
    }

    .credential-item i {
      color: #D2691E;
      font-size: var(--text-base);
    }

    .credential-separator {
      color: #d1d5db;
      font-weight: var(--weight-regular); /* Changed from 300 for font optimization */
      font-size: var(--text-lg);
    }

    /* Responsive - Mobile */
    @media (max-width: 767px) {
      .why-us__credentials {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
      }

      .credential-separator {
        display: none;
      }

      .credential-item {
        width: 100%;
      }
    }

    /* Responsive - Tablet */
    @media (min-width: 768px) and (max-width: 1024px) {
      .why-us__hero-image img {
        aspect-ratio: 16 / 10;
      }
    }

  /* ============================================================
     P0 CONVERSION OPTIMIZATION FIXES  
     ChatGPT GPT-4o Vision Analysis - Expected: +30-40% conversions
     ============================================================ */

  /* P0-2: Enhanced CTA Button (+10% conversions) */
  .btn--accent.hero__cta-primary,
  .hero__cta-primary {
    min-height: 56px !important;
    font-size: 18px !important;
    font-weight: 700 !important;
    background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%) !important;
    box-shadow: 0 8px 24px rgba(74, 144, 226, 0.4) !important;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease !important;
    padding: 16px 40px !important;
    min-width: 280px;
  }

  .btn--accent.hero__cta-primary:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 12px 36px rgba(74, 144, 226, 0.5) !important;
  }

  /* P0-3: Prominent Trust Signals (+5-10% conversions) */
  .hero__trust-microline {
    background: rgba(255, 255, 255, 0.95) !important;
    padding: 16px 24px !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15) !important;
    margin: 24px 0 !important;
  }

  .hero__trust-microline span {
    font-size: 16px !important;
    font-weight: 600 !important;
    color: #1a1a1a !important;
  }

  /* P0-5: Accessibility - Better Contrast */
  .hero__title,
  .hero__sub {
    text-shadow: 0 2px 8px rgba(0,0,0,0.6) !important;
  }

  /* P0-6: Sticky Navigation */
  .navbar {
    position: sticky !important;
    top: 0 !important;
    z-index: 1000 !important;
    background: rgba(255, 255, 255, 0.98) !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
  }

  /* Fix hero section overlap with sticky navbar */
  .hero {
    padding-top: 80px !important;
  }

  @media (max-width: 768px) {
    .hero__cta-primary {
      width: 100%;
      font-size: 16px !important;
    }
  }
  </style>

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
             alt="Traditional Uzbek craft workshop with artisan demonstrating pottery"
             width="1920"
             height="1080"
             sizes="100vw"
             fetchpriority="high">
      </picture>
    </div>

    <!-- Hero Content -->
    <div class="hero__content">
      <div class="container container--wide">
        <div class="hero__text hero__text--narrow">
          <h1 id="hero-heading" class="hero__title">{{ __('ui.hero.title') }}</h1>
          <p class="hero__sub">{{ __('ui.hero.subtitle') }}</p>

          {{-- Trust Microline - appears immediately without scrolling --}}
          <p class="hero__trust-microline">
            <span class="trust-stars">★★★★☆</span>
            <span>4.4/5 on GetYourGuide</span>
            <span class="trust-separator">·</span>
            <span>{{ __('ui.hero.rating_local_artisans') }}</span>
            <span class="trust-separator">·</span>
            <span>{{ __('ui.hero.rating_small_groups') }}</span>
            <span class="trust-separator">·</span>
            <span>{{ __('ui.hero.rating_authentic_workshops') }}</span>
          </p>

          <div class="hero__cta">
            {{-- Primary CTA --}}
            <a href="{{ url('/' . app()->getLocale() . '/tours') }}" class="btn btn--accent btn--large btn--pill hero__cta-primary">
              {{ __('ui.hero.cta_button') }}
              <i class="fas fa-arrow-right" aria-hidden="true" style="margin-left: 8px;"></i>
            </a>

            {{-- Secondary CTA - Text link --}}
            <a href="#itinerary-snapshot" class="hero__cta-secondary">
              {{ __('ui.hero.view_itinerary') }} <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </a>
          </div>
        </div>

        <!-- Hero Badges (Compact Trust Signals) -->
        <div class="hero__badges">
          <div class="badge">
            <div class="badge__icon">
              <i class="fas fa-users" aria-hidden="true"></i>
            </div>
            <div class="badge__content">
              <strong class='badge__title'>{{ __('ui.badges.family_run_title') }}</strong>
              <p class='badge__text'>{{ __('ui.badges.family_run_text') }}</p>
            </div>
          </div>

          <div class="badge">
            <div class="badge__icon">
              <i class="fas fa-hands" aria-hidden="true"></i>
            </div>
            <div class="badge__content">
              <strong class='badge__title'>{{ __('ui.badges.hands_on_title') }}</strong>
              <p class='badge__text'>{{ __('ui.badges.hands_on_text') }}</p>
            </div>
          </div>

          <div class="badge">
            <div class="badge__icon">
              <i class="fas fa-leaf" aria-hidden="true"></i>
            </div>
            <div class="badge__content">
              <strong class='badge__title'>{{ __('ui.badges.eco_friendly_title') }}</strong>
              <p class='badge__text'>{{ __('ui.badges.eco_friendly_text') }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Gradient overlay for readability -->
    <div class="hero__overlay" aria-hidden="true"></div>
  </section>



  <!-- ========================================
       TRUST BAR (Immediately After Hero)
       ======================================== -->
  <section class="trust-bar">
    <div class="container">
      <div class="trust-bar__grid">
        <div class="trust-bar__item">
          <i class="fas fa-palette trust-bar__icon" aria-hidden="true"></i>
          <span class='trust-bar__text'>{!! __('ui.trust.artisan_workshops') !!}</span>
        </div>
        <div class="trust-bar__item">
          <i class="fas fa-route trust-bar__icon" aria-hidden="true"></i>
          <span class='trust-bar__text'>{{ __('ui.trust.craft_focused') }}</span>
        </div>
        <div class="trust-bar__item">
          <i class="fas fa-users trust-bar__icon" aria-hidden="true"></i>
          <span class='trust-bar__text'>{{ __('ui.trust.small_groups') }}</span>
        </div>
        <div class="trust-bar__item">
          <i class="fas fa-map-marker-alt trust-bar__icon" aria-hidden="true"></i>
          <span class='trust-bar__text'>{{ __('ui.trust.designed_by_locals') }}</span>
        </div>
        <div class="trust-bar__item">
          <i class="fas fa-file-invoice-dollar trust-bar__icon" aria-hidden="true"></i>
          <span class='trust-bar__text'>{{ __('ui.trust.transparent_pricing') }}</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ========================================
       DIFFERENTIATION SECTION
       ======================================== -->
  <section class="differentiation">
    <div class="container">
      <div class="differentiation__header">
        <h2 class='differentiation__title'>{{ __('ui.differentiation.title') }}</h2>
      </div>
      <div class="differentiation__grid">
        <div class="differentiation__item">
          <i class="fas fa-hands differentiation__icon" aria-hidden="true"></i>
          <h3>{{ __('ui.differentiation.more_workshops_title') }}</h3>
          <p>{{ __('ui.differentiation.more_workshops_text') }}</p>
        </div>
        <div class="differentiation__item">
          <i class="fas fa-home differentiation__icon" aria-hidden="true"></i>
          <h3>{{ __('ui.differentiation.real_studios_title') }}</h3>
          <p>{{ __('ui.differentiation.real_studios_text') }}</p>
        </div>
        <div class="differentiation__item">
          <i class="fas fa-hourglass-half differentiation__icon" aria-hidden="true"></i>
          <h3>{{ __('ui.differentiation.balanced_pacing_title') }}</h3>
          <p>{{ __('ui.differentiation.balanced_pacing_text') }}</p>
        </div>
        <div class="differentiation__item">
          <i class="fas fa-heart differentiation__icon" aria-hidden="true"></i>
          <h3>{{ __('ui.differentiation.cultural_understanding_title') }}</h3>
          <p>{{ __('ui.differentiation.cultural_understanding_text') }}</p>
        </div>
      </div>
    </div>
  </section>



  <!-- Section 2: Why We're Your Perfect Travel Partner -->
  <section class="why-us" id="why-us">
    <div class="container">
      <div class="why-us__grid">

        <!-- Left Column: Content -->
        <div class="why-us__intro">
          <span class='eyebrow'>{{ __('ui.why_choose_us.eyebrow') }}</span>
          <h2>{{ __('ui.why_choose_us.title') }}</h2>
          <p>{{ __('ui.why_choose_us.intro') }}</p>
          {{-- Social Proof with Specific Data --}}
          <div class="why-us__social-proof">
            <div class="rating-badge">
              <span class="stars">★★★★☆</span>
              <strong>4.4</strong>
              <span class="rating-source">on GetYourGuide (31 reviews)</span>
            </div>
            <span class="social-proof-separator">•</span>
            <span class="social-proof-stat">{{ __('ui.why_choose_us.workshops_hosted') }}</span>
          </div>

          {{-- Single Primary CTA --}}
          <div class="why-us__cta-wrapper">
            <a href="{{ url('/' . app()->getLocale() . '/tours') }}" class="btn btn--primary btn--large btn--hero">
              {{ __('ui.why_choose_us.cta_button') }}
              <i class="fas fa-arrow-right" aria-hidden="true" style="margin-left: 8px;"></i>
            </a>
            <p class='cta-subtext'>{{ __('ui.why_choose_us.cta_subtext') }}</p>
          </div>

          {{-- CRITICAL FIX #1: Enhanced Pricing with Inclusion Checklist --}}
          <div class="pricing-preview pricing-preview--enhanced">
            {{-- PHASE 1.4: Context label added --}}
            <p class='pricing-preview__label'><span class='pricing-context'>{{ __('ui.pricing.label') }}:</span> {{ __('ui.pricing.package_name') }}</p>
            <p class="pricing-preview__amount">{{ __('ui.pricing.amount') }} <span class="pricing-preview__duration">{{ __('ui.pricing.per_person') }}</span></p>

            {{-- What's Included Checklist (CRITICAL for conversion) --}}
            <ul class="pricing-includes">
              <li><i class="fas fa-check" aria-hidden="true"></i> {{ __('ui.pricing.includes.pottery_instruction') }}</li>
              <li><i class='fas fa-check' aria-hidden='true'></i> {{ __('ui.pricing.includes.materials') }}</li>
              <li><i class='fas fa-check' aria-hidden='true'></i> {{ __('ui.pricing.includes.guide') }}</li>
              <li><i class='fas fa-check' aria-hidden='true'></i> {{ __('ui.pricing.includes.transport') }}</li>
              <li><i class='fas fa-check' aria-hidden='true'></i> {{ __('ui.pricing.includes.homestay') }}</li>
              <li><i class='fas fa-check' aria-hidden='true'></i> {{ __('ui.pricing.includes.meals') }}</li>
              <li><i class='fas fa-check' aria-hidden='true'></i> {{ __('ui.pricing.includes.keep_pottery') }}</li>
            </ul>

            {{-- PHASE 1.2: Strengthened value anchor --}}
            <p class="pricing-value">
              <strong>{{ __('ui.pricing.comparable_private') }}</strong> {{ __('ui.pricing.price_range') }}<br>
              <span class="pricing-value-savings">{{ __('ui.pricing.savings') }}</span>
            </p>

            <a href="{{ url('/' . app()->getLocale() . '/tours') }}" class="pricing-preview__link">{{ __('ui.pricing.view_all_packages') }}</a>
          </div>

          <div class="why-us__divider"></div>
          <ul class="benefits">
            <li>
              <i class="benefits__icon fas fa-map-marked-alt" aria-hidden="true"></i>
              <div class="benefits__content">
                <strong>{{ __('ui.features.local_expertise_title') }}</strong>
                <p>{{ __('ui.features.local_expertise_text') }}</p>
              </div>
            </li>
            <li>
              <i class="benefits__icon fas fa-route" aria-hidden="true"></i>
              <div class="benefits__content">
                <strong>{{ __('ui.features.personalized_title') }}</strong>
                <p>{{ __('ui.features.personalized_text') }}</p>
              </div>
            </li>
            <li>
              <i class="benefits__icon fas fa-headset" aria-hidden="true"></i>
              <div class="benefits__content">
                <strong>{{ __('ui.features.multilingual_support_title') }}</strong>
                <p>{{ __('ui.features.multilingual_support_text') }}</p>
              </div>
            </li>
          </ul>
        </div>

        <!-- Right Column: Single Hero Image -->
        <div class="why-us__media why-us__media--hero">
          <div class="why-us__hero-image">
            <img src="{{ asset('images/craft-pottery.webp') }}"
                 alt="Artisan demonstrating traditional Uzbek blue pottery glazing technique"
                 width="800"
                 height="600"
                 loading="lazy"
                 decoding="async"
                 class="hero-image">
          </div>

          <!-- Inline Credentials Below Image -->
          <div class="why-us__credentials">
            <div class="credential-item">
              <i class="fas fa-award" aria-hidden="true"></i>
              <span>{{ __('ui.signals.licensed') }}</span>
            </div>
            <span class="credential-separator">•</span>
            <div class="credential-item">
              <i class="fas fa-calendar-check" aria-hidden="true"></i>
              <span>{{ __('ui.signals.established') }}</span>
            </div>
            <span class="credential-separator">•</span>
            <div class="credential-item">
              <i class="fas fa-shield-alt" aria-hidden="true"></i>
              <span>{{ __('ui.signals.verified') }}</span>
            </div>
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
          "url": "{{ url('/' . app()->getLocale() . '/tours/category/' . $category->slug) }}"
        }{{ $loop->last ? '' : ',' }}
        @endforeach
      ]
    }
    </script>
    @endif

    <div class="container">

      <!-- Section Header -->
      <div class="section-header">
        <p class='section-eyebrow'>{{ __('ui.crafts.eyebrow') }}</p>
        <h2 class='section-header__title'>{{ __('ui.crafts.title') }}</h2>
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
          <a href="{{ url('/' . app()->getLocale() . '/tours/category/' . $category->slug) }}" class="activity-card" aria-label="Explore {{ $category->translated_name }} tours">
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
              <span class='activity-card__link'>{{ __('ui.crafts.explore_tours') }} <i class='fas fa-arrow-right' aria-hidden='true'></i></span>
            </div>
          </a>
        @endforeach
        @else
          <div class="empty-state">
            <p class='empty-state__message'>{{ __('ui.crafts.no_activities') }}</p>
          </div>
        @endif
      </div>

      <!-- View All Tours Link -->
      <div class="activities__cta">
        <a href="{{ url('/' . app()->getLocale() . '/tours') }}" class="btn btn--primary btn--large" aria-label="View all Uzbekistan tours">
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
            "url": "{{ url('/' . app()->getLocale() . '/tours/' . $tour->slug) }}",
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

      <!-- Section Header (IMPROVED: Marketing + UI/UX + Value Prop) -->
      <div class="tours__header tours__header--enhanced">
        <p class='section-eyebrow'>{{ __('ui.immersion.eyebrow') }}</p>

        {{-- FIX 4: Emotional Hook Heading --}}
        <h2 class='section-title'>{{ __('ui.immersion.title') }}</h2>

        {{-- FIX 5: USP (Unique Selling Proposition) --}}
        <p class="section-usp">
          <i class="fas fa-award" aria-hidden="true"></i>
          Learn traditional crafts from skilled local artisans in authentic working workshops
        </p>

        {{-- FIX 6: Enhanced Description with Specifics --}}
        <p class="section-subtitle">
          From 2-day pottery intensives to 12-day multi-craft odysseys.
          Experience <strong>Gijduvan blue glazing</strong>, <strong>Margilan silk weaving</strong>, and <strong>Bukhara suzani embroidery</strong> with local craftspeople.
          Small groups (max 6), 100% hands-on workshops, artisan homestays.
        </p>

        {{-- FIX 1: Social Proof --}}
        <div class="social-proof-compact">
          <span class="rating-badge">
            <span class="stars">★★★★★</span>
            <strong>5.0</strong>
            <span class="rating-source">on TripAdvisor</span>
          </span>
          <span class="separator">•</span>
          <span class="stat">{{ __('ui.why_choose_us.workshops_hosted') }}</span>
        </div>

        {{-- FIX 7: Icon Highlights --}}
        <div class="journey-highlights">
          <div class="highlight-item">
            <i class="fas fa-users" aria-hidden="true"></i>
            <span>{{ __('ui.immersion.max_travelers') }}</span>
          </div>
          <div class="highlight-item">
            <i class="fas fa-certificate" aria-hidden="true"></i>
            <span>{{ __('ui.immersion.local_experts') }}</span>
          </div>
          <div class="highlight-item">
            <i class="fas fa-hands" aria-hidden="true"></i>
            <span>100% Hands-On</span>
          </div>
        </div>
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

      {{-- FIX 8: Urgency Indicator --}}
      <div class="urgency-notice">
        <i class="fas fa-info-circle" aria-hidden="true"></i>
        <span>{{ __('ui.immersion.urgency') }}</span>
      </div>

      <!-- View All CTA (IMPROVED) -->
      <div class="tours__cta tours__cta--enhanced">
        {{-- FIX 2 & 3: Better CTA Copy + Terracotta Color --}}
        <a href="{{ url('/' . app()->getLocale() . '/tours') }}" class="btn btn--craft-journey btn--large" aria-label="Explore available 2025 craft workshop calendar">
          <i class="fas fa-calendar-alt" aria-hidden="true"></i>
          Explore 2025 Workshop Calendar
        </a>

        {{-- FIX 9: Secondary CTA --}}
        <a href="#how-it-works" class="link-secondary">
          How craft workshops work
          <i class="fas fa-arrow-right" aria-hidden="true"></i>
        </a>

        {{-- FIX 10: Free Cancellation Badge --}}
        <p class="guarantee-badge">
          <i class="fas fa-shield-check" aria-hidden="true"></i>
          Free cancellation up to 14 days before departure
        </p>
      </div>

    </div>
  </section>

  <!-- ========================================
       SECTION 5: MEET THE ARTISANS
  ========================================= -->
  <section class="places" id="masters">

    <!-- JSON-LD Schema: Master Artisans -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "ItemList",
      "name": "Local Artisans of Uzbekistan",
      "description": "Skilled local craftspeople teaching pottery, silk weaving, suzani embroidery, and traditional crafts",
      "itemListElement": [
        {
          "@@type": "Person",
          "position": 1,
          "name": "Alisher Nazirov",
          "jobTitle": "Traditional Potter",
          "description": "Skilled ceramics artisan in Gijduvan teaching traditional 'Gijduvan blue' glaze technique.",
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
          "description": "Established silk factory in Margilan with traditional machines. Watch silk being spun from cocoons using traditional methods.",
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
          "name": "Embroiderers of Bukhara",
          "jobTitle": "Suzani Embroidery Artisans",
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
          "name": "Miniature Painting Artisans",
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
        <p class='section-eyebrow'>{{ __('ui.artisans.eyebrow') }}</p>
        <h2 class='section-title'>{{ __('ui.artisans.title') }}</h2>
        <p class="section-subtitle">
          Visit working workshops — from family studios to established local craft centers. Workshops vary by tour.
        </p>
      </div>

      <!-- Masters Grid -->
      <div class="places__grid">
        <!-- Master 1: Alisher Nazirov - Gijduvan Pottery -->
        <article class="city-card master-card">
          <a href="{{ url('/' . app()->getLocale() . '/tours/ceramics-miniature-painting-uzbekistan') }}" class="city-card__link" aria-label="Learn pottery with Alisher Nazirov">
            <div class="city-card__media">
              <img
                src="https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=400&h=533&fit=crop&q=80"
                alt="Potter shaping clay on pottery wheel in Gijduvan"
                width="400"
                height="533"
                loading="lazy"
                decoding="async"
                >
              <div class="card-gradient-overlay"></div>
            </div>
            <div class="city-card__content">
              <h3 class="city-card__title">Alisher Nazirov</h3>
              <p class="city-card__tagline">Traditional Potter • 5th Generation</p>
              <p class="city-card__stats">
                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                Gijduvan • Ceramics & Glazing
              </p>
              <p class="card-description">
                Learn traditional "Gijduvan blue" glaze techniques in a family-run pottery workshop.
              </p>
              {{-- PHASE 1.3: Artisan → Journey linking --}}
              <p class="master-journey-link">
                <i class="fas fa-route" aria-hidden="true"></i>
                Included in Samarkand & Gijduvan journeys
              </p>
              <span class="city-card__cta">
                View Pottery Workshops<i class="fas fa-arrow-right" aria-hidden="true"></i>
              </span>
            </div>
          </a>
        </article>

        <!-- Master 2: Yodgorlik Silk Factory -->
        <article class="city-card master-card">
          <a href="{{ url('/' . app()->getLocale() . '/tours/textile-immersion-uzbekistan') }}" class="city-card__link" aria-label="Learn silk weaving at Yodgorlik Factory">
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
              <p class="city-card__tagline">Silk Weavers • Margilan</p>
              <p class="city-card__stats">
                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                Margilan • Silk Weaving & Ikat
              </p>
              <p class="card-description">
                Watch silk being spun from cocoons on 100-year-old machines. Learn traditional ikat dyeing.
              </p>
              {{-- PHASE 1.3: Artisan → Journey linking --}}
              <p class="master-journey-link">
                <i class="fas fa-route" aria-hidden="true"></i>
                Included in Margilan & Fergana Valley journeys
              </p>
              <span class="city-card__cta">
                View Silk Workshops<i class="fas fa-arrow-right" aria-hidden="true"></i>
              </span>
            </div>
          </a>
        </article>

        <!-- Master 3: Suzani Embroidery -->
        <article class="city-card master-card">
          <a href="{{ url('/' . app()->getLocale() . '/tours/textile-immersion-uzbekistan') }}" class="city-card__link" aria-label="Learn suzani embroidery with local artisans">
            <div class="city-card__media">
              <img
                src="https://images.unsplash.com/photo-1452860606245-08befc0ff44b?w=400&h=533&fit=crop&q=80"
                alt="Embroiderer working on traditional suzani textile in Bukhara"
                width="400"
                height="533"
                loading="lazy"
                decoding="async"
                >
              <div class="card-gradient-overlay"></div>
            </div>
            <div class="city-card__content">
              <h3 class="city-card__title">Suzani Embroidery</h3>
              <p class="city-card__tagline">5+ Generations of Family Designs</p>
              <p class="city-card__stats">
                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                Bukhara • Textile Embroidery
              </p>
              <p class="card-description">
                Learn chain stitch and satin stitch from families who've passed down patterns for 5 generations.
              </p>
              {{-- PHASE 1.3: Artisan → Journey linking --}}
              <p class="master-journey-link">
                <i class="fas fa-route" aria-hidden="true"></i>
                Included in Bukhara & Textile Immersion journeys
              </p>
              <span class="city-card__cta">
                View Embroidery Classes<i class="fas fa-arrow-right" aria-hidden="true"></i>
              </span>
            </div>
          </a>
        </article>

        <!-- Master 4: Miniature Painting -->
        <article class="city-card master-card">
          <a href="{{ url('/' . app()->getLocale() . '/tours/ceramics-miniature-painting-uzbekistan') }}" class="city-card__link" aria-label="Learn Persian miniature painting">
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
              <h3 class="city-card__title">Miniature Painting</h3>
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
        <a href="{{ url('/' . app()->getLocale() . '/tours') }}" class="btn btn--primary btn--large" aria-label="Explore craft immersion journeys">
          <i class="fas fa-users" aria-hidden="true"></i>
          See All Craft Visits
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
        <p class='section-eyebrow'>{{ __('ui.reviews.eyebrow') }}</p>
        <h2 class='section-title'>{{ __('ui.reviews.title') }}</h2>
        <p class="section-subtitle">
          Real experiences from travelers who explored Uzbekistan's crafts with us
        </p>
      </div>

      <!-- FEATURED REVIEW (Highlighted, Emotional) -->
      <div class="featured-review">
        <div class="featured-review__quote">
          <i class="fas fa-quote-left featured-review__quote-icon" aria-hidden="true"></i>
          <blockquote class="featured-review__text">
            "I expected a nice tour. What I got was so much more — genuine connections with craftspeople who opened their homes and hearts to us. Watching Alisher shape clay with techniques his great-grandfather used, hearing the stories behind each pattern at the suzani workshop... this wasn't tourism, this was immersion. I came back with real skills, handmade pieces I created myself, and memories that still make me smile. If you want to truly understand Uzbekistan through its living traditions, this is the experience."
          </blockquote>
        </div>
        <div class="featured-review__author">
          <img src="https://ui-avatars.com/api/?name=Catherine+Thompson&size=80&background=0D4C92&color=fff" alt="Catherine Thompson" class="featured-review__avatar" width="80" height="80">
          <div class="featured-review__info">
            <h3 class="featured-review__name">Catherine Thompson</h3>
            <p class="featured-review__location">
              <span class="country-flag" aria-label="Australia">🇦🇺</span> Melbourne, Australia
            </p>
            <div class="featured-review__rating">
              <span class="stars">★★★★★</span>
              <span class="featured-review__source">
                <i class='fab fa-tripadvisor' aria-hidden='true'></i> {{ __('ui.reviews.verified_tripadvisor') }}
              </span>
            </div>
          </div>
        </div>
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
                  <p class="review-card__location"><span class="country-flag">🇬🇧</span> United Kingdom</p>
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
                  <p class="review-card__location"><span class="country-flag">🇺🇸</span> United States</p>
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
                  <p class="review-card__location"><span class="country-flag">🇫🇷</span> France</p>
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
        <p class='section-eyebrow'>{{ __('ui.blog.eyebrow') }}</p>
        <h2 id='blog-title' class='section-title'>{{ __('ui.blog.title') }}</h2>
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
            <p class='empty-state__message'>{{ __('ui.blog.no_posts') }}</p>
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

  {{-- Audience Filtering Section (HIGH: Protects brand + improves reviews) --}}
  <section class="audience-filter">
    <div class="container">
      <div class="audience-filter__content">
        <h2 class='audience-filter__title'>{{ __('ui.audience.title') }}</h2>

        <div class="audience-filter__grid">
          {{-- Perfect For --}}
          <div class="audience-filter__card audience-filter__card--positive">
            <div class="audience-filter__icon">
              <i class="fas fa-check-circle" aria-hidden="true"></i>
            </div>
            <h3 class='audience-filter__heading'>{{ __('ui.audience.perfect_for_title') }}</h3>
            <ul class="audience-filter__list">
              <li><i class="fas fa-check" aria-hidden="true"></i> You enjoy hands-on learning and creative activities</li>
              <li><i class="fas fa-check" aria-hidden="true"></i> You prefer small groups (max 6 travelers)</li>
              <li><i class="fas fa-check" aria-hidden="true"></i> You value cultural depth over fast sightseeing</li>
              <li><i class="fas fa-check" aria-hidden="true"></i> You want authentic connections with local artisans</li>
              <li><i class="fas fa-check" aria-hidden="true"></i> You appreciate slow travel and immersive experiences</li>
            </ul>
          </div>

          {{-- Not Ideal For --}}
          <div class="audience-filter__card audience-filter__card--negative">
            <div class="audience-filter__icon">
              <i class="fas fa-info-circle" aria-hidden="true"></i>
            </div>
            <h3 class="audience-filter__heading">{{ __('ui.audience_extended.not_ideal_title') }}</h3>
            <ul class="audience-filter__list">
              <li><i class="fas fa-times" aria-hidden="true"></i> {{ __('ui.audience_extended.not_ideal.many_attractions') }}</li>
              <li><i class="fas fa-times" aria-hidden="true"></i> {{ __('ui.audience_extended.not_ideal.large_tours') }}</li>
              <li><i class="fas fa-times" aria-hidden="true"></i> {{ __('ui.audience_extended.not_ideal.luxury_hotels') }}</li>
              <li><i class="fas fa-times" aria-hidden="true"></i> {{ __('ui.audience_extended.not_ideal.pre_planned') }}</li>
              <li><i class="fas fa-times" aria-hidden="true"></i> {{ __('ui.audience_extended.not_ideal.homestays') }}</li>
            </ul>
          </div>
        </div>

        <p class="audience-filter__footer">
          <strong>{{ __('ui.audience_extended.footer') }}</strong> <a href="{{ url('/contact') }}">{{ __('ui.audience_extended.footer_link') }}</a> {{ __('ui.audience_extended.footer_text') }}
        </p>
      </div>
    </div>
  </section>

    <!-- ========================================
       SAMPLE ITINERARY SNAPSHOT
       ======================================== -->
  <section class="itinerary-snapshot" id="itinerary-snapshot">
    <div class="container">
      <div class="itinerary-snapshot__header">
        <p class='section-eyebrow'>{{ __('ui.sample_day.eyebrow') }}</p>
        <h2 class='section-title'>{{ __('ui.sample_day.title') }}</h2>
        <p class="section-subtitle">A glimpse into the balanced pace of our workshops — time to learn, create, and absorb.</p>
      </div>

      <div class="itinerary-snapshot__timeline">
        <div class="timeline-item">
          <div class="timeline-item__time">
            <i class="fas fa-sun" aria-hidden="true"></i>
            <span>{{ __('ui.sample_day.morning.label') }}</span>
          </div>
          <div class="timeline-item__content">
            <h3>{{ __('ui.sample_day.morning.title') }}</h3>
            <p>{{ __('ui.sample_day.morning.description') }}</p>
          </div>
        </div>

        <div class="timeline-item">
          <div class="timeline-item__time">
            <i class="fas fa-utensils" aria-hidden="true"></i>
            <span>{{ __('ui.sample_day.midday.label') }}</span>
          </div>
          <div class="timeline-item__content">
            <h3>{{ __('ui.sample_day.midday.title') }}</h3>
            <p>{{ __('ui.sample_day.midday.description') }}</p>
          </div>
        </div>

        <div class="timeline-item">
          <div class="timeline-item__time">
            <i class="fas fa-hands" aria-hidden="true"></i>
            <span>{{ __('ui.sample_day.afternoon.label') }}</span>
          </div>
          <div class="timeline-item__content">
            <h3>{{ __('ui.sample_day.afternoon.title') }}</h3>
            <p>{{ __('ui.sample_day.afternoon.description') }}</p>
          </div>
        </div>

        <div class="timeline-item">
          <div class="timeline-item__time">
            <i class="fas fa-moon" aria-hidden="true"></i>
            <span>{{ __('ui.sample_day.evening.label') }}</span>
          </div>
          <div class="timeline-item__content">
            <h3>{{ __('ui.sample_day.evening.title') }}</h3>
            <p>{{ __('ui.sample_day.evening.description') }}</p>
          </div>
        </div>
      </div>

      <div class="itinerary-snapshot__cta">
        <a href="{{ url('/' . app()->getLocale() . '/tours') }}" class="btn btn--primary btn--large">
          {{ __('ui.itinerary.see_full') }}
          <i class="fas fa-arrow-right" aria-hidden="true"></i>
        </a>
      </div>
    </div>
  </section>

{{-- FAQ Section (CRITICAL for SEO + Conversion) --}}
  <section class="faq-section">
    <div class="container">
      <div class="faq-section__header">
        <p class='section-eyebrow'>{{ __('ui.faq_extended.eyebrow') }}</p>
        <h2 class='section-title'>{{ __('ui.faq_extended.title') }}</h2>
        <p class='section-subtitle'>{{ __('ui.faq_extended.subtitle') }}</p>
      </div>

      <div class="faq-grid">
        {{-- CRITICAL FAQ: Expectation Management (Open by Default) --}}
        <details class="faq-item faq-item--critical" open itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <summary class="faq-item__question" itemprop="name">
            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
            {{ __('ui.faq_extended.famous_masters.question') }}
            <i class="fas fa-chevron-down faq-item__icon" aria-hidden="true"></i>
          </summary>
          <div class="faq-item__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">
              <p><strong>{{ __('ui.faq_extended.famous_masters.answer_1') }}</strong></p>
              <p>{{ __('ui.faq_extended.famous_masters.answer_2') }}</p>
            </div>
          </div>
        </details>

        {{-- CRITICAL FAQ: Shopping Tour Clarification (Open by Default) --}}
        <details class="faq-item faq-item--critical" open itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <summary class="faq-item__question" itemprop="name">
            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
            {{ __('ui.faq_extended.shopping_tour.question') }}
            <i class="fas fa-chevron-down faq-item__icon" aria-hidden="true"></i>
          </summary>
          <div class="faq-item__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">
              <p><strong>{{ __('ui.faq_extended.shopping_tour.answer_1') }}</strong></p>
              <p>{{ __('ui.faq_extended.shopping_tour.answer_2') }}</p>
            </div>
          </div>
        </details>

        {{-- CRITICAL FAQ: Hands-on vs Observation --}}
        <details class="faq-item faq-item--critical" open itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <summary class="faq-item__question" itemprop="name">
            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
            {{ __('ui.faq_extended.hands_on.question') }}
            <i class="fas fa-chevron-down faq-item__icon" aria-hidden="true"></i>
          </summary>
          <div class="faq-item__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">
              <p><strong>{{ __('ui.faq_extended.hands_on.answer_1') }}</strong></p>
              <p>{{ __('ui.faq_extended.hands_on.answer_2') }}</p>
            </div>
          </div>
        </details>


        {{-- FAQ Item 1: Prior Experience --}}
        <details class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <summary class="faq-item__question" itemprop="name">
            <i class="fas fa-question-circle" aria-hidden="true"></i>
            {{ __('ui.faq_items.prior_experience.question') }}
            <i class="fas fa-chevron-down faq-item__icon" aria-hidden="true"></i>
          </summary>
          <div class="faq-item__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">
              <p><strong>{{ __('ui.faq_items.prior_experience.answer_1') }}</strong> {{ __('ui.faq_items.prior_experience.answer_2') }}</p>
              <p>{{ __('ui.faq_items.prior_experience.answer_3') }}</p>
            </div>
          </div>
        </details>

        {{-- FAQ Item 2: Children --}}
        <details class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <summary class="faq-item__question" itemprop="name">
            <i class="fas fa-question-circle" aria-hidden="true"></i>
            {{ __('ui.faq_items.children.question') }}
            <i class="fas fa-chevron-down faq-item__icon" aria-hidden="true"></i>
          </summary>
          <div class="faq-item__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">
              <p><strong>{{ __('ui.faq_items.children.answer_1') }}</strong> {{ __('ui.faq_items.children.answer_2') }}</p>
              <p>{{ __('ui.faq_items.children.answer_3') }}</p>
            </div>
          </div>
        </details>

        {{-- FAQ Item 3: Languages --}}
        <details class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <summary class="faq-item__question" itemprop="name">
            <i class="fas fa-question-circle" aria-hidden="true"></i>
            {{ __('ui.faq_items.languages.question') }}
            <i class="fas fa-chevron-down faq-item__icon" aria-hidden="true"></i>
          </summary>
          <div class="faq-item__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">
              <p><strong>{{ __('ui.faq_items.languages.answer_1') }}</strong> {{ __('ui.faq_items.languages.answer_2') }}</p>
              <p>{{ __('ui.faq_items.languages.answer_3') }}</p>
            </div>
          </div>
        </details>

        {{-- FAQ Item 4: Private Journeys --}}
        <details class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <summary class="faq-item__question" itemprop="name">
            <i class="fas fa-question-circle" aria-hidden="true"></i>
            {{ __('ui.faq_items.private.question') }}
            <i class="fas fa-chevron-down faq-item__icon" aria-hidden="true"></i>
          </summary>
          <div class="faq-item__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">
              <p><strong>{{ __('ui.faq_items.private.answer_1') }}</strong> {{ __('ui.faq_items.private.answer_2') }}</p>
              <p>{{ __('ui.faq_items.private.answer_3') }} <a href="{{ url('/contact') }}">{{ __('ui.faq_items.private.contact_link') }}</a>.</p>
            </div>
          </div>
        </details>

        {{-- FAQ Item 5: What's Included --}}
        <details class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <summary class="faq-item__question" itemprop="name">
            <i class="fas fa-question-circle" aria-hidden="true"></i>
            {{ __('ui.faq_items.whats_included.question') }}
            <i class="fas fa-chevron-down faq-item__icon" aria-hidden="true"></i>
          </summary>
          <div class="faq-item__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">
              <p><strong>{{ __('ui.faq_items.whats_included.answer_intro') }}</strong></p>
              <ul>
                <li>{{ __('ui.faq_items.whats_included.includes.instruction') }}</li>
                <li>{{ __('ui.faq_items.whats_included.includes.materials') }}</li>
                <li>{{ __('ui.faq_items.whats_included.includes.guides') }}</li>
                <li>{{ __('ui.faq_items.whats_included.includes.transport') }}</li>
                <li>{{ __('ui.faq_items.whats_included.includes.accommodation') }}</li>
                <li>{{ __('ui.faq_items.whats_included.includes.meals') }}</li>
                <li>{{ __('ui.faq_items.whats_included.includes.keep_pieces') }}</li>
              </ul>
              <p><strong>{{ __('ui.faq_items.whats_included.not_included') }}</strong> {{ __('ui.faq_items.whats_included.not_included_text') }}</p>
            </div>
          </div>
        </details>

        {{-- FAQ Item 6: Cancellation --}}
        <details class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <summary class="faq-item__question" itemprop="name">
            <i class="fas fa-question-circle" aria-hidden="true"></i>
            {{ __('ui.faq_items.cancellation.question') }}
            <i class="fas fa-chevron-down faq-item__icon" aria-hidden="true"></i>
          </summary>
          <div class="faq-item__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">
              <p><strong>{{ __('ui.faq_items.cancellation.answer_1') }}</strong> {{ __('ui.faq_items.cancellation.answer_2') }}</p>
              <p>{{ __('ui.faq_items.cancellation.answer_3') }}<br>{{ __('ui.faq_items.cancellation.answer_4') }}</p>
              <p>{{ __('ui.faq_items.cancellation.answer_5') }}</p>
            </div>
          </div>
        </details>
      </div>

      {{-- FAQ Footer CTA --}}
      <div class="faq-section__footer">
        <p class="faq-section__footer-text">{{ __('ui.faq_footer.text') }}</p>
        <a href="{{ url('/contact') }}" class="btn btn--primary btn--large">
          <i class="fas fa-envelope" aria-hidden="true"></i> {{ __('ui.faq_footer.button') }}
        </a>
        <p class="faq-section__response-time">
          <i class="fas fa-clock" aria-hidden="true"></i> {{ __('ui.faq_footer.response_time') }}
        </p>
      </div>
    </div>
  </section>

  
  <!-- ========================================
       FINAL CTA (Pre-Footer)
       ======================================== -->
  <section class="final-cta">
    <div class="container">
      <div class="final-cta__content">
        <h2 class="final-cta__title">{{ __('ui.final_cta.title') }}</h2>
        <p class="final-cta__subtitle">{{ __('ui.final_cta.subtitle') }}</p>
        <div class="final-cta__buttons">
          <a href="{{ url('/' . app()->getLocale() . '/tours') }}" class="btn btn--accent btn--large btn--pill">
      {{ __('ui.mobile_cta.check_availability') }}
            <i class="fas fa-calendar-check" aria-hidden="true"></i>
          </a>
          <a href="{{ url('/contact') }}" class="btn btn--outline btn--large">
            <i class="fas fa-comments" aria-hidden="true"></i> {{ __('ui.final_cta.button_contact') }}
          </a>
        </div>
        <p class="final-cta__note">{{ __('ui.final_cta.note') }}</p>
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
    <span class="whatsapp-float__text">{{ __('ui.whatsapp.text') }}</span>
  </a>

  <!-- ========================================
       MOBILE STICKY CTA
       ======================================== -->
  <div class="mobile-sticky-cta">
    <a href="{{ url('/' . app()->getLocale() . '/tours') }}" class="mobile-sticky-cta__button">
      <i class="fas fa-calendar-check" aria-hidden="true"></i>
{{ __('ui.mobile_cta.check_availability') }}
    </a>
  </div>

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
  font-size: var(--text-sm);
  font-weight: var(--weight-medium);
}

.trust-badge-item i {
  font-size: var(--text-base);
  color: #4ade80;
}

@media (max-width: 640px) {
  .hero__trust-badges {
    gap: 16px;
  }

  .trust-badge-item {
    font-size: var(--text-xs); /* Was 0.8125rem */
  }

  .trust-badge-item i {
    font-size: var(--text-base); /* Was 0.9375rem */
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
  font-weight: var(--weight-semibold);
  font-size: var(--text-base); /* Was 0.9375rem */
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
  font-size: var(--text-2xl);
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
    font-size: var(--text-3xl); /* Was 1.75rem */
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

/* ========================================
   CRAFT JOURNEY SECTION IMPROVEMENTS
   ======================================== */

/* USP (Unique Selling Proposition) */
.section-usp {
  font-size: var(--text-lg);
  font-weight: var(--weight-medium);
  color: #D2691E;
  margin: 1rem auto 1.5rem;
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
  border-left: 4px solid #D2691E;
  border-radius: 6px;
  max-width: 800px;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.section-usp i {
  font-size: 1.25rem;
  flex-shrink: 0;
}

/* Enhanced subtitle with bold craft names */
.section-subtitle strong {
  color: var(--color-text);
  font-weight: var(--weight-semibold);
}

/* Social Proof Compact */
.social-proof-compact {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 0.75rem 1rem;
  margin: 1.5rem auto;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e9ecef;
  max-width: 600px;
}

.social-proof-compact .rating-badge {
  display: flex;
  align-items: center;
  gap: 6px;
}

.social-proof-compact .stars {
  color: #fbbf24;
  font-size: var(--text-base);
  letter-spacing: 2px;
}

.social-proof-compact strong {
  font-size: var(--text-lg);
  font-weight: var(--weight-bold);
  color: var(--color-text);
}

.social-proof-compact .rating-source {
  font-size: var(--text-sm);
  color: #6b7280;
}

.social-proof-compact .separator {
  color: #d1d5db;
  font-weight: var(--weight-regular);
}

.social-proof-compact .stat {
  font-size: var(--text-sm);
  color: #4b5563;
  font-weight: var(--weight-medium);
}

/* Journey Highlights */
.journey-highlights {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 2rem;
  margin: 1.5rem auto;
  flex-wrap: wrap;
}

.highlight-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  min-width: 120px;
}

.highlight-item i {
  font-size: 2rem;
  color: #D2691E;
}

.highlight-item span {
  font-size: var(--text-sm);
  font-weight: var(--weight-semibold);
  color: var(--color-text);
  text-align: center;
}

/* Urgency Notice */
.urgency-notice {
  background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);
  border-left: 4px solid #f59e0b;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  margin: 2rem auto;
  max-width: 700px;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  box-shadow: 0 2px 8px rgba(245, 158, 11, 0.15);
}

.urgency-notice i {
  font-size: 1.25rem;
  color: #f59e0b;
  flex-shrink: 0;
}

.urgency-notice span {
  font-size: var(--text-sm);
  color: #92400e;
  font-weight: var(--weight-medium);
}

/* Craft Journey Button (Terracotta theme) */
.btn--craft-journey {
  background: linear-gradient(135deg, #D2691E 0%, #A0522D 100%);
  color: white;
  border: none;
  padding: 1rem 2rem;
  font-size: var(--text-lg);
  font-weight: var(--weight-semibold);
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(210, 105, 30, 0.3);
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
}

.btn--craft-journey:hover {
  background: linear-gradient(135deg, #A0522D 0%, #8B4513 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(210, 105, 30, 0.4);
  color: white;
}

.btn--craft-journey i {
  font-size: 1.125rem;
}

/* Enhanced CTA Section */
.tours__cta--enhanced {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  margin-top: 3rem;
}

/* Secondary Link */
.link-secondary {
  color: #6b7280;
  font-size: var(--text-sm);
  font-weight: var(--weight-medium);
  text-decoration: none;
  transition: color 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.link-secondary:hover {
  color: #D2691E;
  text-decoration: underline;
}

.link-secondary i {
  font-size: 0.875rem;
  transition: transform 0.2s ease;
}

.link-secondary:hover i {
  transform: translateX(3px);
}

/* Guarantee Badge */
.guarantee-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.25rem;
  background: #f0fdf4;
  border: 1px solid #86efac;
  border-radius: 6px;
  margin-top: 1rem;
  font-size: var(--text-sm);
  color: #166534;
  font-weight: var(--weight-medium);
}

.guarantee-badge i {
  color: #22c55e;
  font-size: var(--text-base);
}

/* Responsive Adjustments */
@media (max-width: 767px) {
  .section-usp {
    font-size: var(--text-base);
    padding: 0.75rem 1rem;
  }

  .social-proof-compact {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }

  .social-proof-compact .separator {
    display: none;
  }

  .journey-highlights {
    gap: 1.5rem;
  }

  .highlight-item {
    min-width: 100px;
  }

  .highlight-item i {
    font-size: 1.75rem;
  }

  .urgency-notice {
    padding: 0.875rem 1rem;
  }

  .btn--craft-journey {
    width: 100%;
    justify-content: center;
    padding: 0.875rem 1.5rem;
  }

  .guarantee-badge {
    font-size: 0.8125rem;
  }
}

@media (min-width: 768px) and (max-width: 1024px) {
  .journey-highlights {
    gap: 1.75rem;
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
  <script src="{{ asset('js/reviews-carousel.js') }}?v={{ filemtime(public_path('js/reviews-carousel.js')) }}" defer></script>
@endpush
