@extends('layouts.main')

@section('title', 'About Us - Preserving Uzbekistan\'s Craft Heritage | Jahongir Travel')
@section('meta_description', 'We support master artisans and preserve traditional crafts through small-group immersive journeys. Supporting artisans and traditional crafts since 2012.')
@section('canonical', url('/about'))

{{-- Open Graph --}}
@section('og_type', 'website')
@section('og_url', url('/about'))
@section('og_title', 'About Us - Preserving Uzbekistan\'s Craft Heritage')
@section('og_description', 'Supporting master artisans and preserving traditional crafts through small-group craft immersion journeys.')
@section('og_image', asset('images/og-about-crafts.jpg'))

{{-- Structured Data - Organization --}}
@section('structured_data')
{
  "@@context": "https://schema.org",
  "@@type": "TravelAgency",
  "name": "Jahongir Travel",
  "description": "Craft tourism specialist supporting master artisans and preserving traditional Uzbek crafts since 2012.",
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
    /* Hero Section */
    .about-hero {
        background: linear-gradient(135deg, #1a5490 0%, #2c7abf 100%);
        color: white;
        padding: 120px 0 80px;
        text-align: center;
    }

    .about-hero__title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        font-family: 'Playfair Display', serif;
    }

    .about-hero__subtitle {
        font-size: 1.25rem;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.8;
        opacity: 0.95;
    }

    /* Section Styles */
    .section {
        padding: 80px 0;
    }

    .section--gray {
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
        max-width: 700px;
    }

    .text-center {
        text-align: center;
    }

    .mx-auto {
        margin-left: auto;
        margin-right: auto;
    }

    /* Problem Section */
    .problem-box {
        background: #fff3f3;
        border-left: 4px solid #e74c3c;
        padding: 2rem;
        border-radius: 8px;
        margin: 2rem 0;
    }

    .problem-box__title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #c0392b;
        margin-bottom: 1rem;
    }

    .problem-box__text {
        font-size: 1.0625rem;
        line-height: 1.8;
        color: #444;
        margin-bottom: 1rem;
    }

    .problem-box__stat {
        font-size: 2rem;
        font-weight: 700;
        color: #c0392b;
        margin: 1rem 0 0.5rem;
    }

    /* Solution Grid */
    .solution-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }

    .solution-card {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .solution-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .solution-card__icon {
        width: 64px;
        height: 64px;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.75rem;
    }

    .solution-card__title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.75rem;
    }

    .solution-card__text {
        font-size: 0.9375rem;
        color: #666;
        line-height: 1.6;
    }

    /* Impact Stats */
    .impact-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }

    .impact-stat {
        text-align: center;
        padding: 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .impact-stat__number {
        font-size: 3rem;
        font-weight: 700;
        color: #27ae60;
        margin-bottom: 0.5rem;
        font-family: 'Playfair Display', serif;
    }

    .impact-stat__label {
        font-size: 1rem;
        color: #666;
        line-height: 1.4;
    }

    /* Partnerships */
    .partnership-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 3rem;
    }

    .partnership-card {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        text-align: center;
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
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, #1a5490 0%, #2c7abf 100%);
        color: white;
        padding: 80px 0;
        text-align: center;
    }

    .cta-section__heading {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        font-family: 'Playfair Display', serif;
    }

    .cta-section__text {
        font-size: 1.125rem;
        margin-bottom: 2rem;
        opacity: 0.95;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .btn {
        display: inline-block;
        padding: 1rem 2rem;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn--white {
        background: white;
        color: #1a5490;
    }

    .btn--white:hover {
        background: #f8f9fa;
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .about-hero__title {
            font-size: 2rem;
        }

        .section-heading {
            font-size: 1.75rem;
        }

        .solution-grid,
        .impact-stats,
        .partnership-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    {{-- Hero Section --}}
    <section class="about-hero">
      <div class="container">
        <h1 class="about-hero__title">Preserving Heritage, One Craft at a Time</h1>
        <p class="about-hero__subtitle">
          We're not a typical tour operator. We're a craft preservation initiative disguised as a travel company—supporting master artisans, keeping traditional skills alive, and connecting travelers with the soul of Uzbekistan.
        </p>
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

        <div class="problem-box" style="max-width: 800px; margin-left: auto; margin-right: auto;">
          <p class="problem-box__text">
            <strong>The master suzani embroiderer in Bukhara.</strong> The fourth-generation potter in Gijduvan. The silk weaver in Margilan who learned from her grandmother. They're not just creating beautiful objects—they're living links to a thousand years of history.
          </p>
          <p class="problem-box__text">
            But tourism has become transactional. Travelers speed through workshops in 20 minutes, snap photos, and leave. Artisans earn pennies while tour operators take the profit. The crafts survive as museum pieces, not living traditions.
          </p>
          <p class="problem-box__stat">80% of traditional craft skills could disappear within one generation.</p>
          <p class="problem-box__text" style="margin-bottom: 0;">
            We believe there's a better way—one that respects artisans, preserves heritage, and creates meaningful connections.
          </p>
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
            <p class="solution-card__text">70% of workshop fees go directly to master craftspeople—triple the industry average—ensuring sustainable income for their families.</p>
          </div>

          <div class="solution-card">
            <div class="solution-card__icon">
              <i class="fas fa-clock" aria-hidden="true"></i>
            </div>
            <h3 class="solution-card__title">Multi-Day Immersion</h3>
            <p class="solution-card__text">Spend days, not minutes, with artisans. Learn their stories, master their techniques, and understand the cultural context behind each craft.</p>
          </div>

          <div class="solution-card">
            <div class="solution-card__icon">
              <i class="fas fa-heart" aria-hidden="true"></i>
            </div>
            <h3 class="solution-card__title">Craft-First Itineraries</h3>
            <p class="solution-card__text">Every tour is built around workshops, not landmarks. You'll visit Registan Square—but only after you've learned to create suzani embroidery.</p>
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
            <div class="impact-stat__number">45+</div>
            <div class="impact-stat__label">Master artisans in our network</div>
          </div>

          <div class="impact-stat">
            <div class="impact-stat__number">12</div>
            <div class="impact-stat__label">Traditional craft forms preserved</div>
          </div>

          <div class="impact-stat">
            <div class="impact-stat__number">$85K+</div>
            <div class="impact-stat__label">Paid directly to artisans (2024)</div>
          </div>

          <div class="impact-stat">
            <div class="impact-stat__number">100%</div>
            <div class="impact-stat__label">Of artisans report stable income</div>
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
            <h3 class="partnership-card__name">UNESCO Samarkand Office</h3>
            <p class="partnership-card__role">Cultural heritage consultation</p>
          </div>

          <div class="partnership-card">
            <h3 class="partnership-card__name">Uzbekistan Craft Guild</h3>
            <p class="partnership-card__role">Artisan network & certification</p>
          </div>

          <div class="partnership-card">
            <h3 class="partnership-card__name">Margilan Silk Factory</h3>
            <p class="partnership-card__role">Traditional silk weaving programs</p>
          </div>

          <div class="partnership-card">
            <h3 class="partnership-card__name">Konigil Paper Workshop</h3>
            <p class="partnership-card__role">Ancient papermaking preservation</p>
          </div>
        </div>

        <div style="text-align: center; margin-top: 2rem; padding: 1.5rem; background: #e8f5e9; border-radius: 8px; max-width: 700px; margin-left: auto; margin-right: auto;">
          <p style="font-size: 1rem; color: #2e7d32; margin: 0; line-height: 1.6;">
            <strong>Ethical Tourism Certified</strong><br>
            All our tours meet fair trade standards for artisan pay, cultural respect, and environmental sustainability.
          </p>
        </div>
      </div>
    </section>

    {{-- CTA Section --}}
    <section class="cta-section">
      <div class="container">
        <h2 class="cta-section__heading">Ready to Meet the Masters?</h2>
        <p class="cta-section__text">
          Explore our craft immersion journeys and support the artisans keeping Uzbekistan's traditions alive.
        </p>
        <a href="{{ url('/tours') }}" class="btn btn--white">Explore Craft Journeys</a>
      </div>
    </section>
@endsection
