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
        background: linear-gradient(135deg, #1a5490 0%, #2c7abf 100%);
        color: white;
        padding: 140px 0 100px;
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
        color: #ffd700;
    }

    .about-hero__title {
        font-size: 3.25rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        font-family: 'Playfair Display', serif;
        line-height: 1.2;
    }

    .about-hero__subtitle {
        font-size: 1.25rem;
        max-width: 720px;
        margin: 0 auto 2rem;
        line-height: 1.8;
        opacity: 0.95;
    }

    .about-hero__trust {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2rem;
        flex-wrap: wrap;
        margin-top: 1.5rem;
    }

    .about-hero__trust-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9375rem;
        opacity: 0.9;
    }

    .about-hero__trust-item i {
        color: #ffd700;
    }

    .about-hero__cta {
        margin-top: 2rem;
    }

    .btn--hero {
        background: rgba(255,255,255,0.95);
        color: #1a5490;
        padding: 1rem 2.5rem;
        font-size: 1.0625rem;
        font-weight: 600;
        border-radius: 10px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .btn--hero:hover {
        background: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.25);
    }

    /* Section Styles - Improved spacing & typography */
    .section {
        padding: 100px 0;
    }

    .section--gray {
        background: #f8f9fa;
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
        font-size: 0.875rem;
        font-weight: 600;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 1rem;
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
        background: linear-gradient(135deg, #fff5f5 0%, #fff0f0 100%);
        border-left: 5px solid #e74c3c;
        padding: 2.5rem;
        border-radius: 12px;
        margin: 2rem 0;
        box-shadow: 0 4px 20px rgba(231, 76, 60, 0.1);
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
        color: #c0392b;
        margin: 1.5rem 0;
        padding: 1.5rem;
        background: white;
        border-radius: 8px;
        position: relative;
        text-align: center;
        box-shadow: 0 2px 12px rgba(192, 57, 43, 0.15);
    }

    .problem-box__stat::before {
        content: '"';
        font-size: 4rem;
        color: rgba(192, 57, 43, 0.15);
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
        background: #1a5490;
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
        background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.875rem;
        box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
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
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #27ae60;
        font-size: 1.25rem;
    }

    .impact-stat__number {
        font-size: 3.5rem;
        font-weight: 800;
        color: #27ae60;
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
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1a5490;
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
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        border-radius: 12px;
        max-width: 750px;
        margin-left: auto;
        margin-right: auto;
        border: 1px solid rgba(39, 174, 96, 0.2);
    }

    .ethical-commitment p {
        font-size: 1.0625rem;
        color: #2e7d32;
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
        border-left: 4px solid #1a5490;
        position: relative;
    }

    .testimonial-card::before {
        content: '"';
        font-size: 4rem;
        color: rgba(26, 84, 144, 0.1);
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
        background: linear-gradient(135deg, #1a5490 0%, #2c7abf 100%);
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
        color: #ffd700;
        font-size: 0.875rem;
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, #1a5490 0%, #2c7abf 100%);
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
        color: #1a5490;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .btn--white:hover {
        background: #f8f9fa;
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
            padding: 120px 0 80px;
        }

        .about-hero__title {
            font-size: 2.25rem;
        }

        .about-hero__subtitle {
            font-size: 1.125rem;
        }

        .about-hero__trust {
            flex-direction: column;
            gap: 0.75rem;
        }

        .section {
            padding: 70px 0;
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
        <h1 class="about-hero__title">Preserving Heritage, One Craft at a Time</h1>
        <p class="about-hero__subtitle">
          We're a craft preservation initiative supporting local artisans and keeping traditional skills alive through meaningful, small-group cultural immersion journeys.
        </p>
        <div class="about-hero__trust">
          <div class="about-hero__trust-item">
            <i class="fas fa-star" aria-hidden="true"></i>
            <span>4.4/5 on GetYourGuide</span>
          </div>
          <div class="about-hero__trust-item">
            <i class="fas fa-users" aria-hidden="true"></i>
            <span>500+ Travelers Hosted</span>
          </div>
          <div class="about-hero__trust-item">
            <i class="fas fa-heart" aria-hidden="true"></i>
            <span>45+ Partner Artisans</span>
          </div>
        </div>
        <div class="about-hero__cta">
          <a href="{{ url('/tours') }}" class="btn--hero">
            <i class="fas fa-route" aria-hidden="true"></i> Explore Craft Tours
          </a>
        </div>
      </div>
    </section>

    {{-- The Problem Section --}}
    <section class="section">
      <div class="container">
        <span class="eyebrow text-center mx-auto" style="display: block; font-size: 0.875rem; font-weight: 600; color: #e74c3c; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 1rem;">THE PROBLEM</span>
        <h2 class="section-heading text-center">Traditional Crafts Are Disappearing</h2>
        <p class="section-tagline text-center mx-auto">
          Across Central Asia, centuries-old craft traditions are vanishing as artisans struggle to earn a living and young people choose modern careers.
        </p>

        <div class="problem-section__content">
          <div class="problem-box">
            <p class="problem-box__text">
              <strong>The suzani embroiderer in Bukhara.</strong> The potter in Gijduvan. The silk weaver in Margilan who learned from her grandmother. They're not just creating beautiful objects—they're living links to a thousand years of history.
            </p>
            <p class="problem-box__text">
              But tourism has become transactional. Travelers speed through workshops in 20 minutes, snap photos, and leave. Artisans earn pennies while tour operators take the profit. The crafts survive as museum pieces, not living traditions.
            </p>
            <p class="problem-box__stat">Many traditional craft skills are at risk of disappearing within a generation.</p>
            <p class="problem-box__text" style="margin-bottom: 0;">
              We believe there's a better way—one that respects artisans, preserves heritage, and creates meaningful connections.
            </p>
          </div>
          <div class="problem-image">
            <img src="{{ asset('images/traditional-embroidery-display.webp') }}" alt="Traditional Uzbek embroidery textiles" loading="lazy">
            <div class="problem-image__caption">
              <i class="fas fa-palette" aria-hidden="true"></i> Traditional Uzbek embroidery
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- Our Solution Section --}}
    <section class="section section--gray">
      <div class="container">
        <span class="eyebrow text-center mx-auto" style="display: block; font-size: 0.875rem; font-weight: 600; color: #27ae60; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 1rem;">OUR SOLUTION</span>
        <h2 class="section-heading text-center">Small Groups, Deep Impact, Fair Pay</h2>
        <p class="section-tagline text-center mx-auto">
          We design craft-focused journeys that support artisans financially, preserve traditional skills, and give travelers authentic, meaningful experiences.
        </p>

        <div class="solution-grid">
          <div class="solution-card">
            <div class="solution-card__icon">
              <i class="fas fa-users" aria-hidden="true"></i>
            </div>
            <h3 class="solution-card__title">Maximum 6 Travelers</h3>
            <p class="solution-card__text">Intimate group sizes mean quality time with artisans, hands-on learning, and genuine cultural exchange—not rushed factory tours.</p>
          </div>

          <div class="solution-card">
            <div class="solution-card__icon">
              <i class="fas fa-hand-holding-usd" aria-hidden="true"></i>
            </div>
            <h3 class="solution-card__title">Fair Pay to Artisans</h3>
            <p class="solution-card__text">A significant portion of workshop fees goes directly to craftspeople—triple the industry average—ensuring sustainable income for their families.</p>
          </div>

          <div class="solution-card">
            <div class="solution-card__icon">
              <i class="fas fa-clock" aria-hidden="true"></i>
            </div>
            <h3 class="solution-card__title">Multi-Day Immersion</h3>
            <p class="solution-card__text">Spend days, not minutes, with artisans. Learn their stories, explore their techniques, and understand the cultural context behind each craft.</p>
          </div>

          <div class="solution-card">
            <div class="solution-card__icon">
              <i class="fas fa-heart" aria-hidden="true"></i>
            </div>
            <h3 class="solution-card__title">Craft-First Itineraries</h3>
            <p class="solution-card__text">Every tour is built around workshops, not landmarks. You'll visit Registan Square—but only after you've learned to create suzani embroidery.</p>
          </div>

          <div class="solution-card">
            <div class="solution-card__icon">
              <i class="fas fa-language" aria-hidden="true"></i>
            </div>
            <h3 class="solution-card__title">Local Expert Guides</h3>
            <p class="solution-card__text">English-speaking guides who personally know the artisans and translate not just words, but cultural context and centuries of tradition.</p>
          </div>

          <div class="solution-card">
            <div class="solution-card__icon">
              <i class="fas fa-home" aria-hidden="true"></i>
            </div>
            <h3 class="solution-card__title">Authentic Workshops</h3>
            <p class="solution-card__text">Real working studios, not tourist shops. You'll see where artisans actually create—the same spaces where their families have worked for generations.</p>
          </div>
        </div>
      </div>
    </section>

    {{-- Our Impact Section --}}
    <section class="section">
      <div class="container">
        <span class="eyebrow text-center mx-auto" style="display: block; font-size: 0.875rem; font-weight: 600; color: #2c7abf; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 1rem;">OUR IMPACT</span>
        <h2 class="section-heading text-center">Making a Real Difference</h2>
        <p class="section-tagline text-center mx-auto">
          Since pivoting to craft-focused tourism, we've supported artisan communities across Uzbekistan and helped preserve endangered skills.
        </p>

        <div class="impact-stats">
          <div class="impact-stat">
            <div class="impact-stat__icon">
              <i class="fas fa-user-friends" aria-hidden="true"></i>
            </div>
            <div class="impact-stat__number">45+</div>
            <div class="impact-stat__label">Artisans in our network</div>
          </div>

          <div class="impact-stat">
            <div class="impact-stat__icon">
              <i class="fas fa-palette" aria-hidden="true"></i>
            </div>
            <div class="impact-stat__number">12</div>
            <div class="impact-stat__label">Traditional craft forms preserved</div>
          </div>

          <div class="impact-stat">
            <div class="impact-stat__icon">
              <i class="fas fa-hand-holding-usd" aria-hidden="true"></i>
            </div>
            <div class="impact-stat__number">$85K+</div>
            <div class="impact-stat__label">Paid directly to artisans</div>
            <div class="impact-stat__source">2024 estimate</div>
          </div>

          <div class="impact-stat">
            <div class="impact-stat__icon">
              <i class="fas fa-chart-line" aria-hidden="true"></i>
            </div>
            <div class="impact-stat__number">13</div>
            <div class="impact-stat__label">Years supporting artisans</div>
            <div class="impact-stat__source">Since 2012</div>
          </div>
        </div>
      </div>
    </section>

    {{-- Partnerships Section --}}
    <section class="section section--gray">
      <div class="container">
        <span class="eyebrow text-center mx-auto" style="display: block; font-size: 0.875rem; font-weight: 600; color: #2c7abf; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 1rem;">PARTNERSHIPS</span>
        <h2 class="section-heading text-center">Working with Heritage Organizations</h2>
        <p class="section-tagline text-center mx-auto">
          We collaborate with cultural institutions, craft guilds, and heritage organizations to ensure authentic, ethical craft tourism.
        </p>

        <div class="partnership-grid">
          <div class="partnership-card">
            <div class="partnership-card__icon">
              <i class="fas fa-landmark" aria-hidden="true"></i>
            </div>
            <h3 class="partnership-card__name">Samarkand Heritage Sites</h3>
            <p class="partnership-card__role">UNESCO World Heritage locations</p>
          </div>

          <div class="partnership-card">
            <div class="partnership-card__icon">
              <i class="fas fa-hands" aria-hidden="true"></i>
            </div>
            <h3 class="partnership-card__name">Local Artisan Network</h3>
            <p class="partnership-card__role">45+ craftspeople across Uzbekistan</p>
          </div>

          <div class="partnership-card">
            <div class="partnership-card__icon">
              <i class="fas fa-industry" aria-hidden="true"></i>
            </div>
            <h3 class="partnership-card__name">Yodgorlik Silk Factory</h3>
            <p class="partnership-card__role">Traditional silk weaving, Margilan</p>
          </div>

          <div class="partnership-card">
            <div class="partnership-card__icon">
              <i class="fas fa-scroll" aria-hidden="true"></i>
            </div>
            <h3 class="partnership-card__name">Konigil Paper Workshop</h3>
            <p class="partnership-card__role">Ancient papermaking, Samarkand</p>
          </div>
        </div>

        <div class="ethical-commitment">
          <p>
            <strong><i class="fas fa-leaf" aria-hidden="true"></i> Our Commitment to Ethical Tourism</strong>
            We strive to ensure fair pay to artisans, cultural respect, and environmental sustainability in all our tours. Every journey supports local communities and preserves traditional craftsmanship.
          </p>
        </div>
      </div>
    </section>

    {{-- Artisan Gallery Section --}}
    <section class="section">
      <div class="container">
        <span class="eyebrow text-center mx-auto" style="color: #1a5490;">MEET THE ARTISANS</span>
        <h2 class="section-heading text-center">The Hands Behind the Crafts</h2>
        <p class="section-tagline text-center mx-auto">
          Real artisans in real workshops. These are the craftspeople you'll meet on our journeys.
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
        <span class="eyebrow text-center mx-auto" style="color: #1a5490;">TESTIMONIALS</span>
        <h2 class="section-heading text-center">What Travelers Say</h2>
        <p class="section-tagline text-center mx-auto">
          Real experiences from travelers who've journeyed with us to meet Uzbekistan's master artisans.
        </p>

        <div class="testimonials">
          <div class="testimonial-card">
            <p class="testimonial-card__text">
              "This wasn't just a tour—it was a masterclass in Uzbek culture. We spent two full days with a suzani embroidery artisan in Bukhara. She taught us ancient stitching techniques her grandmother passed down. I'll never see textiles the same way."
            </p>
            <div class="testimonial-card__author">
              <div class="testimonial-card__avatar">SC</div>
              <div class="testimonial-card__info">
                <div class="testimonial-card__name">Sarah C.</div>
                <div class="testimonial-card__meta">United Kingdom • May 2024</div>
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
              "What impressed me most was the authenticity. No tourist traps—just real workshops where families have worked for generations. The potters in Gijduvan welcomed us like old friends. Jahongir's team truly understands ethical tourism."
            </p>
            <div class="testimonial-card__author">
              <div class="testimonial-card__avatar">MH</div>
              <div class="testimonial-card__info">
                <div class="testimonial-card__name">Michael H.</div>
                <div class="testimonial-card__meta">USA • October 2024</div>
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
              "Small group size made all the difference. Only 5 of us on the tour, so we got real one-on-one time with the silk weavers in Margilan. They showed us techniques you'd never see in a big group tour. Worth every penny."
            </p>
            <div class="testimonial-card__author">
              <div class="testimonial-card__avatar">AL</div>
              <div class="testimonial-card__info">
                <div class="testimonial-card__name">Anna L.</div>
                <div class="testimonial-card__meta">Germany • September 2024</div>
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
        <h2 class="cta-section__heading">Ready to Meet Our Artisans?</h2>
        <p class="cta-section__text">
          Explore our craft immersion journeys and support the artisans keeping Uzbekistan's traditions alive.
        </p>
        <a href="{{ url('/tours') }}" class="btn btn--white">Explore Craft Journeys</a>
      </div>
    </section>
@endsection
