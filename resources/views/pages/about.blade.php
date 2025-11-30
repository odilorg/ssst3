@extends('layouts.main')

@section('title', 'About Us - Jahongir Travel | Family-Run Silk Road Tours Since 2012')
@section('meta_description', 'Meet the family behind Jahongir Travel. Since 2012, we have been crafting authentic Silk Road journeys from our home in Samarkand with local hospitality and expert care.')
@section('canonical', url('/about'))

{{-- Open Graph --}}
@section('og_type', 'website')
@section('og_url', url('/about'))
@section('og_title', 'About Us - Jahongir Travel')
@section('og_description', 'Meet the family behind Jahongir Travel. Since 2012, we have been crafting authentic Silk Road journeys.')
@section('og_image', asset('images/og-about.jpg'))

{{-- Structured Data - Organization --}}
@section('structured_data')
{
  "@@context": "https://schema.org",
  "@@type": "TravelAgency",
  "name": "Jahongir Travel",
  "description": "Family-run tour operator in Samarkand offering authentic Silk Road journeys since 2012.",
  "url": "{{ url('/') }}",
  "logo": "{{ asset('images/logo.png') }}",
  "image": "{{ asset('images/og-about.jpg') }}",
  "telephone": "+998915550808",
  "email": "info@jahongirtravel.com",
  "address": {
    "@@type": "PostalAddress",
    "streetAddress": "Samarkand",
    "addressLocality": "Samarkand",
    "addressCountry": "UZ"
  },
  "geo": {
    "@@type": "GeoCoordinates",
    "latitude": "39.6542",
    "longitude": "66.9597"
  },
  "foundingDate": "2012",
  "founder": {
    "@@type": "Person",
    "name": "Jahongir Karimov"
  },
  "areaServed": ["Uzbekistan", "Central Asia", "Silk Road"],
  "priceRange": "$$",
  "aggregateRating": {
    "@@type": "AggregateRating",
    "ratingValue": "5",
    "reviewCount": "1000",
    "bestRating": "5"
  },
  "sameAs": [
    "https://www.tripadvisor.com/Attraction_Review-g298068-d12345678-Reviews-Jahongir_Travel-Samarkand.html"
  ]
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
      "name": "About Us",
      "item": "{{ url('/about') }}"
    }
  ]
}
</script>
@endpush

@push('styles')
<style>
    /* Why We Are Best Section */
    .why-best {
        padding: 80px 0;
        background: #f9fafb;
    }

    .section-heading {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1rem;
        font-family: 'Playfair Display', serif;
    }

    .section-tagline {
        font-size: 1.125rem;
        color: #666;
        margin-bottom: 3rem;
    }

    .text-center {
        text-align: center;
    }

    .icon-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }

    .icon-card {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .icon-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .icon-card__icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1.5rem;
        background: linear-gradient(135deg, #1a5490 0%, #2c7abf 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.75rem;
    }

    .icon-card__title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.75rem;
    }

    .icon-card__text {
        font-size: 0.9375rem;
        color: #666;
        line-height: 1.6;
    }

    /* Our Story Section */
    .our-story {
        padding: 80px 0;
        background: white;
    }

    .two-col-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
    }

    .eyebrow {
        display: inline-block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #2c7abf;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 1rem;
    }

    .two-col-section__content p {
        font-size: 1.0625rem;
        line-height: 1.8;
        color: #444;
        margin-bottom: 1.5rem;
    }

    .two-col-section__images {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1rem;
        position: relative;
    }

    .story-image {
        width: 100%;
        height: auto;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

    .story-image--large {
        grid-column: 1 / 2;
        grid-row: 1 / 3;
    }

    .story-image--small {
        grid-column: 2 / 3;
        grid-row: 2 / 3;
    }

    .btn {
        display: inline-block;
        padding: 0.875rem 2rem;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid transparent;
    }

    .btn--outline-coral {
        color: #2c7abf;
        border-color: #2c7abf;
        background: transparent;
    }

    .btn--outline-coral:hover {
        background: #2c7abf;
        color: white;
    }

    .btn--primary {
        background: #2c7abf;
        color: white;
        border-color: #2c7abf;
    }

    .btn--primary:hover {
        background: #1a5490;
        border-color: #1a5490;
    }

    .btn--large {
        padding: 1.125rem 2.5rem;
        font-size: 1.125rem;
    }

    /* Stats Showcase */
    .stats-showcase {
        padding: 80px 0;
        background: linear-gradient(135deg, #1a5490 0%, #2c7abf 100%);
        color: white;
    }

    .stats-showcase__intro {
        margin-bottom: 3rem;
    }

    .stats-showcase .section-heading {
        color: white;
    }

    .stats-showcase__subtitle {
        font-size: 1.125rem;
        color: rgba(255, 255, 255, 0.9);
        max-width: 800px;
        margin: 0 auto;
    }

    .stat-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
    }

    .stat-card {
        text-align: center;
        padding: 2rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        backdrop-filter: blur(10px);
    }

    .stat-card__number {
        font-size: 3rem;
        font-weight: 700;
        color: white;
        margin-bottom: 0.5rem;
        font-family: 'Playfair Display', serif;
    }

    .stat-card__label {
        font-size: 1rem;
        color: rgba(255, 255, 255, 0.9);
    }

    /* Team Section */
    .team-section {
        padding: 80px 0;
        background: #f9fafb;
    }

    .team-section__header {
        margin-bottom: 3rem;
    }

    .team-section__subtitle {
        font-size: 1.125rem;
        color: #666;
        max-width: 600px;
        margin: 0 auto;
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2.5rem;
        margin-bottom: 3rem;
    }

    .team-member {
        text-align: center;
    }

    .team-member__photo {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 1.5rem;
        border: 4px solid white;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

    .team-member__name {
        font-size: 1.375rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
    }

    .team-member__position {
        font-size: 1rem;
        color: #666;
    }

    .team-section__cta {
        text-align: center;
    }

    /* Trust Section */
    .trust-section {
        padding: 80px 0;
        background: white;
    }

    .trust-strip {
        text-align: center;
        margin-bottom: 3rem;
        padding: 2rem;
        background: linear-gradient(135deg, #f9fafb 0%, #fff 100%);
        border-radius: 12px;
    }

    .trust-strip__title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 1.5rem;
    }

    .trust-strip__ratings {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 3rem;
        flex-wrap: wrap;
    }

    .trust-rating {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1rem;
        color: #555;
    }

    .trust-rating i {
        color: #f59e0b;
        font-size: 1.25rem;
    }

    .trust-rating strong {
        color: #1a1a1a;
        font-weight: 600;
    }

    /* Featured Review */
    .featured-review {
        max-width: 900px;
        margin: 0 auto;
        background: #f9fafb;
        padding: 2.5rem;
        border-radius: 12px;
        border-left: 4px solid #1a5490;
    }

    .review-author {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .review-author__photo {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00aa6c 0%, #00b97e 100%);
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .review-author__photo span {
        color: white;
        font-weight: 700;
        font-size: 1.125rem;
        letter-spacing: 0.5px;
    }

    .review-author__info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .review-author__name {
        font-weight: 600;
        color: #1a1a1a;
        font-size: 1rem;
    }

    .review-author__badge {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #666;
    }

    .review-author__badge i {
        color: #00aa6c;
        font-size: 1rem;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .review-stars {
        display: flex;
        gap: 0.25rem;
        color: #f59e0b;
        font-size: 1.125rem;
    }

    .review-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.25rem;
    }

    .review-tour {
        font-weight: 600;
        color: #1a5490;
        font-size: 0.9375rem;
    }

    .review-date {
        font-size: 0.875rem;
        color: #666;
    }

    .review-content p {
        font-size: 0.9375rem;
        line-height: 1.8;
        color: #444;
        margin-bottom: 1rem;
    }

    .review-content p:last-child {
        margin-bottom: 0;
        font-weight: 500;
        color: #1a5490;
    }

    /* Help Section */
    .help-section {
        padding: 80px 0;
        background: linear-gradient(135deg, #f9fafb 0%, #fff 100%);
    }

    .help-section__subtitle {
        font-size: 1.125rem;
        color: #666;
        max-width: 700px;
        margin: 0 auto 2.5rem;
    }

    .help-primary-cta {
        text-align: center;
        margin-bottom: 3rem;
    }

    .help-primary-cta__note {
        margin-top: 1rem;
        font-size: 0.9375rem;
        color: #666;
    }

    .help-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        max-width: 800px;
        margin: 0 auto;
    }

    .help-option {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        text-align: center;
    }

    .help-option__icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 1.5rem;
        background: linear-gradient(135deg, #1a5490 0%, #2c7abf 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .help-option__title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.75rem;
    }

    .help-option__text {
        font-size: 0.9375rem;
        color: #666;
        margin-bottom: 1.25rem;
        line-height: 1.6;
    }

    /* Responsive */
    @media (max-width: 768px) {
        /* Add container padding */
        .container {
            padding-left: 16px;
            padding-right: 16px;
        }

        /* Reduce section padding */
        .why-best,
        .our-story,
        .stats-showcase,
        .team-section,
        .trust-section,
        .help-section {
            padding: 60px 0;
        }

        .section-heading {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }

        .section-tagline {
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .two-col-section {
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .two-col-section__content p {
            font-size: 1rem;
        }

        .icon-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .icon-card {
            padding: 1.5rem;
        }

        .icon-card__icon {
            width: 56px;
            height: 56px;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .icon-card__title {
            font-size: 1.125rem;
        }

        .icon-card__text {
            font-size: 0.9rem;
        }

        .stat-cards {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .stat-card {
            padding: 1.5rem;
        }

        .stat-card__number {
            font-size: 2.5rem;
        }

        .stats-showcase__subtitle {
            font-size: 1rem;
        }

        .team-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .team-member__photo {
            width: 160px;
            height: 160px;
        }

        .team-member__name {
            font-size: 1.25rem;
        }

        .trust-strip {
            padding: 1.5rem;
        }

        .trust-strip__ratings {
            flex-direction: column;
            gap: 1rem;
        }

        .featured-review {
            padding: 1.5rem;
        }

        .review-content p {
            font-size: 0.9rem;
        }

        .help-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .help-option {
            padding: 1.5rem;
        }

        .help-section__subtitle {
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .two-col-section__images {
            grid-template-columns: 1fr;
        }

        .story-image--large,
        .story-image--small {
            grid-column: 1;
            grid-row: auto;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            font-size: 0.9375rem;
        }

        .btn--large {
            padding: 1rem 2rem;
            font-size: 1rem;
        }
    }

    /* Very Small Mobile (480px and below) */
    @media (max-width: 480px) {
        .container {
            padding-left: 12px;
            padding-right: 12px;
        }

        .why-best,
        .our-story,
        .stats-showcase,
        .team-section,
        .trust-section,
        .help-section {
            padding: 48px 0;
        }

        .section-heading {
            font-size: 1.75rem;
            line-height: 1.2;
        }

        .section-tagline {
            font-size: 0.9375rem;
        }

        .icon-card {
            padding: 1.25rem;
        }

        .icon-card__icon {
            width: 48px;
            height: 48px;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .icon-card__title {
            font-size: 1.0625rem;
        }

        .icon-card__text {
            font-size: 0.875rem;
        }

        .two-col-section__content p {
            font-size: 0.9375rem;
            margin-bottom: 1.25rem;
        }

        .eyebrow {
            font-size: 0.8125rem;
        }

        .stat-cards {
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }

        .stat-card {
            padding: 1.25rem;
        }

        .stat-card__number {
            font-size: 2.25rem;
        }

        .stat-card__label {
            font-size: 0.9375rem;
        }

        .stats-showcase__intro {
            margin-bottom: 2rem;
        }

        .stats-showcase__subtitle {
            font-size: 0.9375rem;
        }

        .team-member__photo {
            width: 140px;
            height: 140px;
            margin-bottom: 1.25rem;
        }

        .team-member__name {
            font-size: 1.125rem;
        }

        .team-member__position {
            font-size: 0.9375rem;
        }

        .testimonial-card {
            padding: 1.25rem;
        }

        .testimonial-card__title {
            font-size: 0.9375rem;
        }

        .testimonial-card__text {
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
        }

        .testimonial-card__author {
            gap: 0.75rem;
        }

        .testimonial-card__avatar {
            width: 40px;
            height: 40px;
        }

        .testimonial-card__author-name {
            font-size: 0.875rem;
        }

        .testimonial-card__author-location {
            font-size: 0.8125rem;
        }

        .tripadvisor-badge {
            padding: 1rem;
        }

        .tripadvisor-badge__logo {
            height: 32px;
        }

        .stars {
            font-size: 1rem;
        }

        .tripadvisor-badge__count {
            font-size: 0.875rem;
        }

        .help-option {
            padding: 1.25rem;
        }

        .help-option__icon {
            width: 48px;
            height: 48px;
            font-size: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .help-option__title {
            font-size: 1.125rem;
        }

        .help-option__text {
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .help-section__subtitle {
            font-size: 0.9375rem;
        }

        .help-primary-cta__note {
            font-size: 0.875rem;
        }

        .btn {
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
        }

        .btn--large {
            padding: 0.875rem 1.75rem;
            font-size: 0.9375rem;
        }
    }
</style>
@endpush

@section('content')

    <!-- =====================================================
         HERO SECTION
         ===================================================== -->
    <section class="about-hero" aria-labelledby="about-hero-heading">
      <div class="about-hero__overlay"></div>
      <div class="container">
        <div class="about-hero__content">
          <h1 id="about-hero-heading" class="about-hero__title">About Us</h1>
          <p class="about-hero__subtitle">
            Family-run in Samarkand since 2012.<br>
            We craft authentic Silk Road journeys with local hospitality and expert care.
          </p>
        </div>
      </div>
    </section>

    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb" aria-label="Breadcrumb" style="background: #f8f9fa; padding: 1rem 0;">
        <div class="container">
            <ol style="list-style: none; padding: 0; margin: 0; display: flex; align-items: center; flex-wrap: wrap;">
                <li style="display: flex; align-items: center;">
                    <a href="{{ url('/') }}" style="color: #1a5490; text-decoration: none;">Home</a>
                    <span style="margin: 0 0.5rem; color: #666;">/</span>
                </li>
                <li style="color: #666; font-weight: 500;" aria-current="page">About Us</li>
            </ol>
        </div>
    </nav>

    <!-- =====================================================
         WHY WE ARE BEST - ICON GRID
         ===================================================== -->
    <section class="why-best">
      <div class="container">
        <h2 class="section-heading text-center">Why Travelers Choose Jahongir Travel</h2>
        <p class="text-center section-tagline">Authenticity, care, and trust in every journey.</p>

        <div class="icon-grid">
          <div class="icon-card">
            <div class="icon-card__icon">
              <i class="fas fa-home" aria-hidden="true"></i>
            </div>
            <h3 class="icon-card__title">Local family hospitality</h3>
            <p class="icon-card__text">Born and raised in Samarkand, we treat every guest like family. Flexible, human support 24/7—we reply in 24 hours (often faster).</p>
          </div>

          <div class="icon-card">
            <div class="icon-card__icon">
              <i class="fas fa-hands-helping" aria-hidden="true"></i>
            </div>
            <h3 class="icon-card__title">Artisan partnerships</h3>
            <p class="icon-card__text">Direct connections with local craftspeople, family-run guesthouses, and authentic restaurants you won't find in guidebooks.</p>
          </div>

          <div class="icon-card">
            <div class="icon-card__icon">
              <i class="fas fa-dollar-sign" aria-hidden="true"></i>
            </div>
            <h3 class="icon-card__title">Transparent pricing</h3>
            <p class="icon-card__text">Clear, upfront quotes in UZS/USD with no hidden fees. No prepayment required for most day tours.</p>
          </div>

          <div class="icon-card">
            <div class="icon-card__icon">
              <i class="fas fa-map-marked-alt" aria-hidden="true"></i>
            </div>
            <h3 class="icon-card__title">Expert local planning</h3>
            <p class="icon-card__text">We plan, host, and personally guide journeys across Uzbekistan. Every detail handled with care and attention.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         OUR STORY - TWO COLUMN
         ===================================================== -->
    <section class="our-story">
      <div class="container">
        <div class="two-col-section">
          <div class="two-col-section__content">
            <span class="eyebrow">OUR STORY</span>
            <h2 class="section-heading">It feels like family (because it is)</h2>
            <p>Our story began in the heart of Samarkand, where our family opened the first Jahongir Guest House in 2012. What started as a cozy guesthouse welcoming travelers to experience authentic Uzbek hospitality has grown into a full-service travel company, but our values remain the same.</p>
            <p>Born and raised in Samarkand, our founders dreamed of sharing their homeland's hidden treasures with the world. Every tour we craft carries the care and attention we'd give our own family. From recommending the best local restaurants to ensuring you experience authentic cultural moments, <strong>we treat every traveler as part of our extended family.</strong></p>
            <p>If you're going to visit a new place, it should feel like coming home.</p>
            <a href="#team" class="btn btn--outline-coral">Meet our team</a>
          </div>

          <div class="two-col-section__images">
            <img src="/images/about/team-sunset.jpg" alt="Team silhouette at sunset" class="story-image story-image--large" loading="lazy">
            <img src="/images/about/workspace.jpg" alt="Team member planning tours" class="story-image story-image--small" loading="lazy">
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         STATS SHOWCASE
         ===================================================== -->
    <section class="stats-showcase">
      <div class="container">
        <div class="stats-showcase__intro">
          <h2 class="section-heading text-center">Over a decade of journeys that connect cultures</h2>
          <p class="text-center stats-showcase__subtitle">Since 2012, our family has guided travelers through the heart of Uzbekistan and beyond, building lasting relationships and unforgettable memories across the Silk Road.</p>
        </div>

        <div class="stat-cards">
          <div class="stat-card">
            <div class="stat-card__number">Thousands</div>
            <div class="stat-card__label">of tours organized since 2012</div>
          </div>

          <div class="stat-card">
            <div class="stat-card__number">12+</div>
            <div class="stat-card__label">years of experience</div>
          </div>

          <div class="stat-card">
            <div class="stat-card__number">Strong</div>
            <div class="stat-card__label">network of local partners</div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         LEADERSHIP TEAM
         ===================================================== -->
    <section class="team-section" id="team">
      <div class="container">
        <div class="team-section__header">
          <span class="eyebrow">LEADERSHIP TEAM</span>
          <h2 class="section-heading text-center">Our people are your people, too</h2>
          <p class="text-center team-section__subtitle">The dedicated team guiding your Uzbekistan journey to success</p>
        </div>

        <div class="team-grid">
          <div class="team-member">
            <img src="/images/about/team-member-1.jpg" alt="Jahongir Karimov" class="team-member__photo" loading="lazy">
            <h3 class="team-member__name">Jahongir Karimov</h3>
            <p class="team-member__position">Founder & CEO</p>
          </div>

          <div class="team-member">
            <img src="/images/about/team-member-2.jpg" alt="Dilshod Rahimov" class="team-member__photo" loading="lazy">
            <h3 class="team-member__name">Dilshod Rahimov</h3>
            <p class="team-member__position">Head of Operations</p>
          </div>

          <div class="team-member">
            <img src="/images/about/team-member-3.jpg" alt="Madina Sultanova" class="team-member__photo" loading="lazy">
            <h3 class="team-member__name">Madina Sultanova</h3>
            <p class="team-member__position">Chief Experience Officer</p>
          </div>
        </div>

        <div class="team-section__cta">
          <a href="/contact" class="btn btn--outline-coral">Meet our team</a>
        </div>
      </div>
    </section>

    <!-- =====================================================
         TRUST STRIP & FEATURED REVIEW
         ===================================================== -->
    <section class="trust-section">
      <div class="container">
        <!-- Trust Strip -->
        <div class="trust-strip">
          <h3 class="trust-strip__title">Loved by travelers worldwide</h3>
          <div class="trust-strip__ratings">
            <div class="trust-rating">
              <i class="fas fa-star"></i>
              <strong>4.9/5</strong> on Tripadvisor (1,200+ reviews)
            </div>
            <div class="trust-rating">
              <i class="fas fa-star"></i>
              <strong>5.0</strong> on Google Reviews
            </div>
          </div>
        </div>

        <!-- Featured Real Review -->
        <div class="featured-review">
          <div class="review-author">
            <div class="review-author__photo">
              <span>VT</span>
            </div>
            <div class="review-author__info">
              <div class="review-author__name">Verified TripAdvisor Traveler</div>
              <div class="review-author__badge">
                <i class="fab fa-tripadvisor"></i>
                <span>TripAdvisor Review</span>
              </div>
            </div>
          </div>

          <div class="review-header">
            <div class="review-stars">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <div class="review-meta">
              <span class="review-tour">Yurt camp tour</span>
              <span class="review-date">Oct 2024 • Family</span>
            </div>
          </div>

          <div class="review-content">
            <p>From the moment our tour began, everything was handled with care and professionalism. Our tour agent Bek personally met with us to walk through the itinerary and provided a dedicated contact number for emergencies. The journey in a comfortable SUV with our fantastic driver Farhod was smooth and relaxing.</p>

            <p>Arriving at Aydarkul Lake was breathtaking — a beautiful oasis in the middle of the desert that suddenly appeared over the hills. The yurt camp offered surprisingly modern facilities while keeping the authentic nomadic vibe. The night in the desert was magical — clear skies filled with stars, sitting by the fire sharing stories.</p>

            <p>The pottery masterclass at the Narzullaev family workshop in Gijduvan was interactive and creative. This tour was one of the most unique and enriching travel experiences we've had — well-organized, personal, and full of local charm. Highly recommended for anyone looking to experience the real heart of Uzbekistan!</p>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         HELP / CONTACT OPTIONS
         ===================================================== -->
    <section class="help-section">
      <div class="container">
        <h2 class="section-heading text-center">Let's plan your perfect Silk Road adventure together</h2>
        <p class="text-center help-section__subtitle">Not sure where to start? Our local experts are here to guide you every step of the way.</p>

        <!-- Primary CTA -->
        <div class="help-primary-cta">
          <a href="/tours" class="btn btn--primary btn--large">Plan my trip</a>
          <p class="help-primary-cta__note">Browse our tours and find your perfect Silk Road adventure</p>
        </div>

        <!-- Secondary Contact Options -->
        <div class="help-grid">
          <div class="help-option">
            <div class="help-option__icon">
              <i class="fab fa-whatsapp" aria-hidden="true"></i>
            </div>
            <h3 class="help-option__title">Chat on WhatsApp</h3>
            <p class="help-option__text">Quick questions? Message us on WhatsApp and get instant replies from our team.</p>
            <a href="https://wa.me/998915550808" target="_blank" rel="noopener" class="help-option__link"><i class="fab fa-whatsapp"></i> Chat on WhatsApp</a>
          </div>

          <div class="help-option">
            <div class="help-option__icon">
              <i class="fas fa-phone-alt" aria-hidden="true"></i>
            </div>
            <h3 class="help-option__title">Call now</h3>
            <p class="help-option__text">Speak with a local expert about your perfect Silk Road journey.</p>
            <a href="tel:+998915550808" class="help-option__link help-option__link--phone"><i class="fas fa-phone-alt"></i> +998 91 555 08 08</a>
          </div>
        </div>
      </div>
    </section>@endsection
