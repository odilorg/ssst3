@extends('layouts.main')

@section('title', 'About Us - Preserving Uzbekistan\'s Craft Heritage | Jahongir Travel')
@section('meta_description', 'We support local artisans and preserve traditional crafts through small-group immersive journeys. Supporting artisans and traditional crafts since 2012.')
@section('canonical', url('/about'))

{{-- Open Graph --}}
@section('og_type', 'website')
@section('og_url', url('/about'))
@section('og_title', 'About Us - Preserving Uzbekistan\'s Craft Heritage')
@section('og_description', 'Supporting local artisans and preserving traditional crafts through small-group craft immersion journeys.')
@section('og_image', asset('images/og-about-crafts.jpg'))

{{-- Structured Data - Organization --}}
@section('structured_data')
{
  "@@context": "https://schema.org",
  "@@type": "TravelAgency",
  "name": "Jahongir Travel",
  "description": "Craft tourism specialist supporting local artisans and preserving traditional Uzbek crafts since 2012.",
  "url": "{{ url('/') }}",
  "logo": "{{ asset('images/logo.png') }}",
  "image": "{{ asset('images/og-about-crafts.jpg') }}",
  "telephone": "+998915550808",
  "email": "info@jahongir-travel.uz",
  "address": {
    "@@type": "PostalAddress",
    "streetAddress": "Samarkand, Chirokchi 4",
    "addressLocality": "Samarkand",
    "addressCountry": "UZ"
  },
  "foundingDate": "2012",
  "founder": {
    "@@type": "Person",
    "name": "Jahongir Karimov"
  },
  "areaServed": ["Uzbekistan", "Central Asia"],
  "specialty": "Craft immersion tours and artisan workshops"
}
@endsection

@push('styles')
<style>
    /* ==========================================
       ABOUT PAGE - IMPROVED UI/UX (Dec 2024)
       ========================================== */

    /* Hero Section - Enhanced with trust elements */
    .about-hero {
        background: linear-gradient(135deg, rgba(30, 64, 175, 0.85) 0%, rgba(59, 130, 246, 0.85) 100%),
                    url('/images/silk-weaving-artisan.webp') center/cover no-repeat;
        color: white;
        padding: 220px 0 140px; /* Increased top padding to clear navbar + better breathing room */
        text-align: center;
        position: relative;
    }


    .about-hero__badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .about-hero__badge i {
        color: #60a5fa;  /* Light blue */
    }

    .about-hero__title {
        font-size: 3.25rem;
        font-weight: 700;
        margin-bottom: 2.5rem; /* Increased since subtitle removed */
        font-family: 'Playfair Display', serif;
        line-height: 1.2;
        max-width: 900px; /* Prevent overly long lines */
        margin-left: auto;
        margin-right: auto;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3); /* Add text shadow for better readability on image */
    }

    .about-hero__subtitle {
        font-size: 1.25rem;
        max-width: 720px;
        margin: 0 auto 3rem; /* Increased bottom margin since trust row removed */
        line-height: 1.8;
        opacity: 0.95;
    }

    .about-hero__cta {
        margin-top: 0; /* No extra margin needed */
    }

    .btn--hero {
        background: rgba(255,255,255,0.95);
        color: #2563eb;
        padding: 1rem 2.5rem;
        font-size: 1.0625rem;
        font-weight: 600;
        border-radius: 10px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);  /* Stronger shadow */
        border: 2px solid rgba(255,255,255,0.3);
    }

    .btn--hero:hover {
        background: white;
        transform: translateY(-4px);  /* More lift */
        box-shadow: 0 12px 35px rgba(0,0,0,0.35);  /* Much stronger shadow */
        border-color: white;
    }

    .btn--hero:active {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.25);
    }

    /* Section Styles - Improved spacing & typography */
    .section {
        padding: 100px 0;
    }

    .section--first {
        padding-top: 120px;  /* Extra top padding for first section after hero */
    }

    .section--gray {
        background: #F7F8FA;
        position: relative;
    }

    .section--gray::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 120px;
        height: 4px;
        background: linear-gradient(90deg,
            transparent 0%,
            #2563eb 25%,
            #60a5fa 50%,
            #2563eb 75%,
            transparent 100%);
        border-radius: 2px;
    }

    .section-heading {
        font-size: 2.75rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1rem;
        font-family: 'Playfair Display', serif;
        line-height: 1.2;
    }

    .section-tagline {
        font-size: 1.1875rem;
        color: #555;  /* Darker for better accessibility */
        margin-bottom: 3rem;
        max-width: 720px;
        line-height: 1.7;
    }

    .text-center {
        text-align: center;
    }

    .mx-auto {
        margin-left: auto;
        margin-right: auto;
    }

    .eyebrow {
        display: block;
        font-size: 0.9375rem;  /* Increased from 0.875rem */
        font-weight: 700;  /* Bolder: 600 → 700 */
        letter-spacing: 2.5px;  /* Wider spacing */
        text-transform: uppercase;
        margin-bottom: 1.25rem;  /* More space below */
    }

    /* Problem Section - Enhanced with imagery */
    .problem-section__content {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 3rem;
        align-items: start;
        max-width: 1100px;
        margin: 0 auto;
    }

    .problem-box {
        background: linear-gradient(135deg, #FDF8F5 0%, #FAF4EF 100%);
        border-left: 5px solid #C1876B;
        padding: 2.5rem;
        border-radius: 12px;
        margin: 2rem 0;
        box-shadow: 0 4px 20px rgba(193, 135, 107, 0.1);
    }

    .problem-box__text {
        font-size: 1.0625rem;
        line-height: 1.85;
        color: #444;
        margin-bottom: 1.25rem;
    }

    /* Pull-quote style for the stat */
    .problem-box__stat {
        font-size: 1.5rem;
        font-weight: 700;
        color: #A0826D;
        margin: 1.5rem 0;
        padding: 1.5rem;
        background: white;
        border-radius: 8px;
        position: relative;
        text-align: center;
        box-shadow: 0 2px 12px rgba(160, 130, 109, 0.15);
    }

    .problem-box__stat::before {
        content: '"';
        font-size: 4rem;
        color: rgba(160, 130, 109, 0.15);
        position: absolute;
        top: -10px;
        left: 15px;
        font-family: Georgia, serif;
        line-height: 1;
    }

    .problem-image {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }

    .problem-image img {
        width: 100%;
        height: 350px;
        object-fit: cover;
        display: block;
    }

    .problem-image__caption {
        background: #2563eb;
        color: white;
        padding: 1rem;
        font-size: 0.875rem;
        text-align: center;
    }

    /* Solution Grid - Enhanced cards (3x2 grid) */
    .solution-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        margin-top: 3rem;
    }

    .solution-card {
        background: white;
        padding: 2.5rem 2rem;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(0,0,0,0.04);
    }

    .solution-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.12);
    }

    .solution-card__icon {
        width: 72px;
        height: 72px;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.875rem;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }

    .solution-card__title {
        font-size: 1.3125rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.875rem;
    }

    .solution-card__text {
        font-size: 1rem;
        color: #555;
        line-height: 1.7;
    }

    /* Impact Stats - Bolder numbers */
    .impact-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
        margin-top: 3rem;
    }

    .impact-stat {
        text-align: center;
        padding: 2.5rem 1.5rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.04);
        transition: transform 0.3s ease;
    }

    .impact-stat:hover {
        transform: translateY(-4px);
    }

    .impact-stat__icon {
        width: 50px;
        height: 50px;
        margin: 0 auto 1rem;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        font-size: 1.25rem;
    }

    .impact-stat__number {
        font-size: 3.5rem;
        font-weight: 800;
        color: #2563eb;
        margin-bottom: 0.5rem;
        font-family: 'Playfair Display', serif;
        line-height: 1;
    }

    .impact-stat__label {
        font-size: 1rem;
        color: #555;
        line-height: 1.5;
    }

    .impact-stat__source {
        font-size: 0.75rem;
        color: #888;
        margin-top: 0.5rem;
        font-style: italic;
    }

    /* Partnerships - With icons */
    .partnership-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-top: 3rem;
    }

    .partnership-card {
        background: white;
        padding: 2rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(0,0,0,0.04);
    }

    .partnership-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .partnership-card__icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 1rem;
        background: linear-gradient(135deg, #FDF6F0 0%, #F5EBD8 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        font-size: 1.5rem;
    }

    .partnership-card__name {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
    }

    .partnership-card__role {
        font-size: 0.9375rem;
        color: #666;
        line-height: 1.5;
    }

    /* Ethical commitment box */
    .ethical-commitment {
        text-align: center;
        margin-top: 3rem;
        padding: 2rem;
        background: linear-gradient(135deg, #FDF6F0 0%, #F5EBD8 100%);
        border-radius: 12px;
        max-width: 750px;
        margin-left: auto;
        margin-right: auto;
        border: 1px solid rgba(201, 169, 97, 0.2);
    }

    .ethical-commitment p {
        font-size: 1.0625rem;
        color: #2563eb;
        margin: 0;
        line-height: 1.7;
    }

    .ethical-commitment strong {
        display: block;
        font-size: 1.1875rem;
        margin-bottom: 0.5rem;
    }

    /* Artisan Gallery Section */
    .artisan-gallery {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-top: 3rem;
    }

    .artisan-gallery__item {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        position: relative;
    }

    .artisan-gallery__item img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        display: block;
        transition: transform 0.5s ease;
    }

    .artisan-gallery__item:hover img {
        transform: scale(1.05);
    }

    .artisan-gallery__caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.85);
        color: white;
        padding: 1.25rem 1rem;
        font-size: 0.9375rem;
        backdrop-filter: blur(8px);
    }

    /* Testimonials Section */
    .testimonials {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        margin-top: 3rem;
    }

    .testimonial-card {
        background: white;
        padding: 2.5rem 2rem;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border-left: 4px solid #2563eb;
        position: relative;
    }

    .testimonial-card::before {
        content: '"';
        font-size: 4rem;
        color: rgba(37, 99, 235, 0.1);
        position: absolute;
        top: 1rem;
        left: 1.5rem;
        font-family: Georgia, serif;
        line-height: 1;
    }

    .testimonial-card__text {
        font-size: 1rem;
        color: #444;
        line-height: 1.7;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .testimonial-card__author {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e0e0e0;
    }

    .testimonial-card__avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.125rem;
    }

    .testimonial-card__info {
        flex: 1;
    }

    .testimonial-card__name {
        font-weight: 600;
        color: #1a1a1a;
        font-size: 0.9375rem;
        margin-bottom: 0.25rem;
    }

    .testimonial-card__meta {
        font-size: 0.8125rem;
        color: #888;
    }

    .testimonial-card__rating {
        color: #60a5fa;  /* Light blue */
        font-size: 0.875rem;
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: white;
        padding: 100px 0;
        text-align: center;
    }

    .cta-section__heading {
        font-size: 2.75rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        font-family: 'Playfair Display', serif;
    }

    .cta-section__text {
        font-size: 1.1875rem;
        margin-bottom: 2.5rem;
        opacity: 0.95;
        max-width: 620px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.7;
        color: white; /* Ensure subtitle is visible on blue background */
    }

    .btn {
        display: inline-block;
        padding: 1.125rem 2.5rem;
        font-size: 1.0625rem;
        font-weight: 600;
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn--white {
        background: white;
        color: #2563eb;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .btn--white:hover {
        background: #F7F8FA;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.25);
    }

    /* ==========================================
       RESPONSIVE - Mobile First Fixes
       ========================================== */
    @media (max-width: 1024px) {
        .solution-grid,
        .impact-stats,
        .partnership-grid,
        .testimonials {
            grid-template-columns: repeat(2, 1fr);
        }

        .problem-section__content {
            grid-template-columns: 1fr;
        }

        .problem-image {
            order: -1;
            max-width: 500px;
            margin: 0 auto 2rem;
        }
    }

    @media (max-width: 768px) {
        .about-hero {
            padding: 160px 0 100px; /* Increased padding for mobile to clear navbar + better breathing room */
        }

        .about-hero__title {
            font-size: 2.25rem;
            line-height: 1.3;
            max-width: 100%;
            padding: 0 1rem;
        }

        .about-hero__subtitle {
            font-size: 1.125rem;
            line-height: 1.7;
            padding: 0 1rem;
            margin-bottom: 2.5rem;
        }

        .about-hero__cta {
            padding: 0 1rem;
        }

        .btn--hero {
            width: 90%;  /* Wider button on mobile */
            max-width: 320px;
        }

        .section {
            padding: 70px 0;
        }

        .eyebrow {
            font-size: 0.8125rem;
            letter-spacing: 2px;
        }

        .section-heading {
            font-size: 2rem;
        }

        .section-tagline {
            font-size: 1.0625rem;
        }

        .solution-grid,
        .impact-stats,
        .partnership-grid,
        .testimonials {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .solution-card,
        .impact-stat,
        .partnership-card {
            padding: 2rem 1.5rem;
        }

        .impact-stat__number {
            font-size: 2.75rem;
        }

        .problem-box {
            padding: 1.75rem;
        }

        .problem-box__stat {
            font-size: 1.25rem;
            padding: 1.25rem;
        }

        .artisan-gallery {
            grid-template-columns: 1fr;
        }

        .artisan-gallery__item img {
            height: 220px;
        }

        .cta-section {
            padding: 70px 0;
        }

        .cta-section__heading {
            font-size: 2rem;
        }
    }

    @media (max-width: 480px) {
        .about-hero__title {
            font-size: 1.875rem;
        }

        .section-heading {
            font-size: 1.75rem;
        }

        .impact-stat__number {
            font-size: 2.5rem;
        }

        .btn {
            padding: 1rem 2rem;
            font-size: 1rem;
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
    {{-- Hero Section - Enhanced with trust elements --}}
    <section class="about-hero">
      <div class="container">
        <h1 class="about-hero__title">{{ __('ui.about_page.hero_title') }}</h1>
        <div class="about-hero__cta">
          <a href="{{ url('/tours') }}" class="btn--hero">
            <i class="fas fa-route" aria-hidden="true"></i> {{ __('ui.about_page.cta_explore') }}
          </a>
        </div>
      </div>
    </section>

    {{-- The Problem Section --}}
    <section class="section section--first">
      <div class="container">
        <span class="eyebrow text-center mx-auto" style="display: block; font-size: 0.875rem; font-weight: 600; color: #e74c3c; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 1rem;">{{ __('ui.about_page.problem_eyebrow') }}</span>
        <h2 class="section-heading text-center">{{ __('ui.about_page.problem_title') }}</h2>
        <p class="section-tagline text-center mx-auto">
          {{ __('ui.about_page.problem_subtitle') }}
        </p>

        <div class="problem-section__content">
          <div class="problem-box">
            <p class="problem-box__text">
              <strong>{{ __('ui.about_page.problem_text1') }}</strong> The potter in Gijduvan. The silk weaver in Margilan who learned from her grandmother. They're not just creating beautiful objects—they're living links to a thousand years of history.
            </p>
            <p class="problem-box__text">
              {{ __('ui.about_page.problem_text2') }}
            </p>
            <p class="problem-box__stat">{{ __('ui.about_page.problem_stat') }}</p>
            <p class="problem-box__text" style="margin-bottom: 0;">
              {{ __('ui.about_page.problem_text3') }}
            </p>
          </div>
          <div class="problem-image">
            <img src="{{ asset('images/traditional-embroidery-display.webp') }}" alt="Traditional Uzbek embroidery textiles" loading="lazy">
            <div class="problem-image__caption">
              <i class="fas fa-palette" aria-hidden="true"></i> {{ __('ui.about_page.problem_image_caption') }}
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- Our Solution Section --}}
    <section class="section section--gray">
      <div class="container">
        <span class="eyebrow text-center mx-auto" style="display: block; font-size: 0.875rem; font-weight: 600; color: #2563eb; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 1rem;">{{ __('ui.about_page.solution_eyebrow') }}</span>
        <h2 class="section-heading text-center">{{ __('ui.about_page.solution_title') }}</h2>
        <p class="section-tagline text-center mx-auto">
          {{ __('ui.about_page.solution_subtitle') }}
        </p>

        <div class="solution-grid">
          <div class="solution-card">
            <div class="solution-card__icon">
              <i class="fas fa-users" aria-hidden="true"></i>
            </div>
            <h3 class="solution-card__title">{{ __('ui.about_page.solution1_title') }}</h3>
            <p class="solution-card__text">{{ __('ui.about_page.solution1_text') }}</p>
          </div>

          <div class="solution-card">
            <div class="solution-card__icon">
              <i class="fas fa-hand-holding-usd" aria-hidden="true"></i>
            </div>
            <h3 class="solution-card__title">{{ __('ui.about_page.solution2_title') }}</h3>
            <p class="solution-card__text">{{ __('ui.about_page.solution2_text') }}</p>
          </div>

          <div class="solution-card">
            <div class="solution-card__icon">
              <i class="fas fa-clock" aria-hidden="true"></i>
            </div>
            <h3 class="solution-card__title">{{ __('ui.about_page.solution3_title') }}</h3>
            <p class="solution-card__text">{{ __('ui.about_page.solution3_text') }}</p>
          </div>

          <div class="solution-card">
            <div class="solution-card__icon">
              <i class="fas fa-heart" aria-hidden="true"></i>
            </div>
            <h3 class="solution-card__title">{{ __('ui.about_page.solution4_title') }}</h3>
            <p class="solution-card__text">{{ __('ui.about_page.solution4_text') }}</p>
          </div>

          <div class="solution-card">
            <div class="solution-card__icon">
              <i class="fas fa-language" aria-hidden="true"></i>
            </div>
            <h3 class="solution-card__title">{{ __('ui.about_page.solution5_title') }}</h3>
            <p class="solution-card__text">{{ __('ui.about_page.solution5_text') }}</p>
          </div>

          <div class="solution-card">
            <div class="solution-card__icon">
              <i class="fas fa-home" aria-hidden="true"></i>
            </div>
            <h3 class="solution-card__title">{{ __('ui.about_page.solution6_title') }}</h3>
            <p class="solution-card__text">{{ __('ui.about_page.solution6_text') }}</p>
          </div>
        </div>
      </div>
    </section>

    {{-- Our Impact Section --}}
    <section class="section section--first">
      <div class="container">
        <span class="eyebrow text-center mx-auto" style="display: block; font-size: 0.875rem; font-weight: 600; color: #2563eb; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 1rem;">{{ __('ui.about_page.impact_eyebrow') }}</span>
        <h2 class="section-heading text-center">{{ __('ui.about_page.impact_title') }}</h2>
        <p class="section-tagline text-center mx-auto">
          {{ __('ui.about_page.impact_subtitle') }}
        </p>

        <div class="impact-stats">
          <div class="impact-stat">
            <div class="impact-stat__icon">
              <i class="fas fa-user-friends" aria-hidden="true"></i>
            </div>
            <div class="impact-stat__number">45+</div>
            <div class="impact-stat__label">{{ __('ui.about_page.impact_stat1_label') }}</div>
          </div>

          <div class="impact-stat">
            <div class="impact-stat__icon">
              <i class="fas fa-palette" aria-hidden="true"></i>
            </div>
            <div class="impact-stat__number">12</div>
            <div class="impact-stat__label">{{ __('ui.about_page.impact_stat2_label') }}</div>
          </div>

          <div class="impact-stat">
            <div class="impact-stat__icon">
              <i class="fas fa-hand-holding-usd" aria-hidden="true"></i>
            </div>
            <div class="impact-stat__number">$85K+</div>
            <div class="impact-stat__label">{{ __('ui.about_page.impact_stat3_label') }}</div>
            <div class="impact-stat__source">{{ __('ui.about_page.impact_stat3_source') }}</div>
          </div>

          <div class="impact-stat">
            <div class="impact-stat__icon">
              <i class="fas fa-chart-line" aria-hidden="true"></i>
            </div>
            <div class="impact-stat__number">13</div>
            <div class="impact-stat__label">{{ __('ui.about_page.impact_stat4_label') }}</div>
            <div class="impact-stat__source">{{ __('ui.about_page.impact_stat4_source') }}</div>
          </div>
        </div>
      </div>
    </section>

    {{-- Partnerships Section --}}
    <section class="section section--gray">
      <div class="container">
        <span class="eyebrow text-center mx-auto" style="display: block; font-size: 0.875rem; font-weight: 600; color: #2563eb; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 1rem;">{{ __('ui.about_page.partnerships_eyebrow') }}</span>
        <h2 class="section-heading text-center">{{ __('ui.about_page.partnerships_title') }}</h2>
        <p class="section-tagline text-center mx-auto">
          {{ __('ui.about_page.partnerships_subtitle') }}
        </p>

        <div class="partnership-grid">
          <div class="partnership-card">
            <div class="partnership-card__icon">
              <i class="fas fa-landmark" aria-hidden="true"></i>
            </div>
            <h3 class="partnership-card__name">{{ __('ui.about_page.partner1_name') }}</h3>
            <p class="partnership-card__role">{{ __('ui.about_page.partner1_role') }}</p>
          </div>

          <div class="partnership-card">
            <div class="partnership-card__icon">
              <i class="fas fa-hands" aria-hidden="true"></i>
            </div>
            <h3 class="partnership-card__name">{{ __('ui.about_page.partner2_name') }}</h3>
            <p class="partnership-card__role">{{ __('ui.about_page.partner2_role') }}</p>
          </div>

          <div class="partnership-card">
            <div class="partnership-card__icon">
              <i class="fas fa-industry" aria-hidden="true"></i>
            </div>
            <h3 class="partnership-card__name">{{ __('ui.about_page.partner3_name') }}</h3>
            <p class="partnership-card__role">{{ __('ui.about_page.partner3_role') }}</p>
          </div>

          <div class="partnership-card">
            <div class="partnership-card__icon">
              <i class="fas fa-scroll" aria-hidden="true"></i>
            </div>
            <h3 class="partnership-card__name">{{ __('ui.about_page.partner4_name') }}</h3>
            <p class="partnership-card__role">{{ __('ui.about_page.partner4_role') }}</p>
          </div>
        </div>

        <div class="ethical-commitment">
          <p>
            <strong><i class="fas fa-leaf" aria-hidden="true"></i> {{ __('ui.about_page.ethical_commitment_title') }}</strong>
            {{ __('ui.about_page.ethical_commitment_text') }}
          </p>
        </div>
      </div>
    </section>

    {{-- Artisan Gallery Section --}}
    <section class="section section--first">
      <div class="container">
        <span class="eyebrow text-center mx-auto" style="color: #2563eb;">{{ __('ui.about_page.artisans_eyebrow') }}</span>
        <h2 class="section-heading text-center">{{ __('ui.about_page.artisans_title') }}</h2>
        <p class="section-tagline text-center mx-auto">
          {{ __('ui.about_page.artisans_subtitle') }}
        </p>

        <div class="artisan-gallery">
          <div class="artisan-gallery__item">
            <img src="{{ asset('images/pottery-artisan.webp') }}" alt="Uzbek pottery craftsman at work" loading="lazy">
            <div class="artisan-gallery__caption">
              <strong>Gijduvan Pottery</strong><br>
              Traditional ceramics workshop
            </div>
          </div>
          <div class="artisan-gallery__item">
            <img src="{{ asset('images/embroidery-artisan.webp') }}" alt="Suzani embroidery artisan creating traditional patterns" loading="lazy">
            <div class="artisan-gallery__caption">
              <strong>Suzani Embroidery</strong><br>
              Hand-stitched textiles
            </div>
          </div>
          <div class="artisan-gallery__item">
            <img src="{{ asset('images/silk-weaving-artisan.webp') }}" alt="Silk weaving artisans at traditional loom" loading="lazy">
            <div class="artisan-gallery__caption">
              <strong>Silk Weaving</strong><br>
              Margilan silk tradition
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- Testimonials Section --}}
    <section class="section section--gray">
      <div class="container">
        <span class="eyebrow text-center mx-auto" style="color: #2563eb;">{{ __('ui.about_page.testimonials_eyebrow') }}</span>
        <h2 class="section-heading text-center">{{ __('ui.about_page.testimonials_title') }}</h2>
        <p class="section-tagline text-center mx-auto">
          {{ __('ui.about_page.testimonials_subtitle') }}
        </p>

        <div class="testimonials">
          <div class="testimonial-card">
            <p class="testimonial-card__text">
              {{ __('ui.about_page.testimonial1_text') }}
            </p>
            <div class="testimonial-card__author">
              <div class="testimonial-card__avatar">SC</div>
              <div class="testimonial-card__info">
                <div class="testimonial-card__name">{{ __('ui.about_page.testimonial1_name') }}</div>
                <div class="testimonial-card__meta">{{ __('ui.about_page.testimonial1_meta') }}</div>
                <div class="testimonial-card__rating">
                  <i class="fas fa-star" aria-hidden="true"></i>
                  <i class="fas fa-star" aria-hidden="true"></i>
                  <i class="fas fa-star" aria-hidden="true"></i>
                  <i class="fas fa-star" aria-hidden="true"></i>
                  <i class="fas fa-star" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="testimonial-card">
            <p class="testimonial-card__text">
              {{ __('ui.about_page.testimonial2_text') }}
            </p>
            <div class="testimonial-card__author">
              <div class="testimonial-card__avatar">MH</div>
              <div class="testimonial-card__info">
                <div class="testimonial-card__name">{{ __('ui.about_page.testimonial2_name') }}</div>
                <div class="testimonial-card__meta">{{ __('ui.about_page.testimonial2_meta') }}</div>
                <div class="testimonial-card__rating">
                  <i class="fas fa-star" aria-hidden="true"></i>
                  <i class="fas fa-star" aria-hidden="true"></i>
                  <i class="fas fa-star" aria-hidden="true"></i>
                  <i class="fas fa-star" aria-hidden="true"></i>
                  <i class="fas fa-star" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="testimonial-card">
            <p class="testimonial-card__text">
              {{ __('ui.about_page.testimonial3_text') }}
            </p>
            <div class="testimonial-card__author">
              <div class="testimonial-card__avatar">AL</div>
              <div class="testimonial-card__info">
                <div class="testimonial-card__name">{{ __('ui.about_page.testimonial3_name') }}</div>
                <div class="testimonial-card__meta">{{ __('ui.about_page.testimonial3_meta') }}</div>
                <div class="testimonial-card__rating">
                  <i class="fas fa-star" aria-hidden="true"></i>
                  <i class="fas fa-star" aria-hidden="true"></i>
                  <i class="fas fa-star" aria-hidden="true"></i>
                  <i class="fas fa-star" aria-hidden="true"></i>
                  <i class="fas fa-star" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- CTA Section --}}
    <section class="cta-section">
      <div class="container">
        <h2 class="cta-section__heading">{{ __('ui.about_page.cta_title') }}</h2>
        <p class="cta-section__text">
          {{ __('ui.about_page.cta_text') }}
        </p>
        <a href="{{ url('/tours') }}" class="btn btn--white">{{ __('ui.about_page.cta_button') }}</a>
      </div>
    </section>
@endsection
