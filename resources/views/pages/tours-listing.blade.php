@extends('layouts.main')

@php
    $initialTours = $tours ?? collect();
@endphp

@section('title', 'Craft Immersion Journeys in Uzbekistan | Hands-On Artisan Workshops')
@section('meta_description', 'Authentic craft tourism in Uzbekistan. Learn pottery, silk weaving, and suzani embroidery from master artisans. Small groups (max 6). Weekend to 2-week immersive journeys from $850.')
@section('canonical', url('/tours'))

@section('structured_data')
{!! $structuredData !!}
@endsection

@push('head')
<!-- Preload hero image for faster LCP -->
<link rel="preload" as="image" href="{{ asset('images/hero-registan.webp') }}" type="image/webp">
@endpush

@push('styles')<link rel="stylesheet" href="{{ asset('css/tours-listing.css') }}">@endpush
<link rel="stylesheet" href="{{ asset('css/tour-card-option2.css') }}">

@section('content')

    <!-- =====================================================
         HERO SECTION
         ===================================================== -->
    <section class="tours-hero">
        <div class="tours-hero__overlay"></div>
        <div class="container">
            <div class="tours-hero__content">
                <h1 class="tours-hero__title">Learn From the Masters</h1>
                <p class="tours-hero__subtitle">Small-group craft immersion journeys (max 6 travelers) with hands-on workshops, artisan homestays, and UNESCO-recognized master craftspeople</p>
                <a href="#craft-journeys" class="hero-cta-btn">
                    Explore Craft Journeys
                    <i class="fas fa-arrow-down"></i>
                </a>
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
                <li style="color: #666; font-weight: 500;" aria-current="page">Tours</li>
            </ol>
        </div>
    </nav>

    <!-- =====================================================
         TRUST BADGES
         ===================================================== -->
    <section class="trust-badges">
        <div class="container">
            <div class="trust-badges__grid">
                <div class="trust-badge">
                    <i class="fas fa-users"></i>
                    <div class="trust-badge__content">
                        <div class="trust-badge__number">Max 6</div>
                        <div class="trust-badge__label">Small Groups</div>
                    </div>
                </div>
                <div class="trust-badge">
                    <i class="fas fa-hands-helping"></i>
                    <div class="trust-badge__content">
                        <div class="trust-badge__number">45+</div>
                        <div class="trust-badge__label">Master Artisans</div>
                    </div>
                </div>
                <div class="trust-badge">
                    <i class="fas fa-home"></i>
                    <div class="trust-badge__content">
                        <div class="trust-badge__number">Homestays</div>
                        <div class="trust-badge__label">With Artisan Families</div>
                    </div>
                </div>
                <div class="trust-badge">
                    <i class="fas fa-hand-holding-usd"></i>
                    <div class="trust-badge__content">
                        <div class="trust-badge__number">70%</div>
                        <div class="trust-badge__label">Direct to Artisans</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =====================================================
         FEATURED CRAFT JOURNEYS
         ===================================================== -->
    <section id="craft-journeys" style="padding: 80px 0; background: #f9fafb;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 3rem;">
                <span style="display: block; font-size: 0.875rem; font-weight: 600; color: #27ae60; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 1rem;">FEATURED CRAFT IMMERSION JOURNEYS</span>
                <h2 style="font-size: 2.5rem; font-weight: 700; color: #1a1a1a; margin-bottom: 1rem; font-family: 'Playfair Display', serif;">Choose Your Journey</h2>
                <p style="font-size: 1.125rem; color: #666; max-width: 700px; margin: 0 auto;">From weekend tasters to comprehensive craft tours—all feature small groups, hands-on workshops, and artisan homestays.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
                @php
                    $craftTours = App\Models\Tour::whereIn('id', [48, 47, 46])->orderBy('duration_days')->get();
                @endphp

                @foreach($craftTours as $craftTour)
                    @include('partials.tours.card-option2-compact', ['tour' => $craftTour])
                @endforeach
            </div>
        </div>
    </section>

    <!-- =====================================================
         FAQ SECTION
         ===================================================== -->
    <section class="tours-faq">
        <div class="container">
            <h2 class="tours-faq__title">Frequently Asked Questions</h2>
            <p class="tours-faq__subtitle">Everything you need to know about booking tours in Uzbekistan</p>

            <div class="faq-grid">
                <div class="faq-item">
                    <h3 class="faq-item__question">
                        <i class="fas fa-question-circle"></i>
                        How do I book a tour?
                    </h3>
                    <p class="faq-item__answer">Click on any tour to view details, then use the "Book Now" button or contact us via WhatsApp. We'll confirm availability and guide you through the booking process.</p>
                </div>

                <div class="faq-item">
                    <h3 class="faq-item__question">
                        <i class="fas fa-question-circle"></i>
                        What's included in the tour price?
                    </h3>
                    <p class="faq-item__answer">Each tour page clearly lists what's included and excluded. Typically includes guide services, transportation, and entrance fees. Meals and accommodation vary by tour.</p>
                </div>

                <div class="faq-item">
                    <h3 class="faq-item__question">
                        <i class="fas fa-question-circle"></i>
                        Can tours be customized?
                    </h3>
                    <p class="faq-item__answer">Yes! We offer private tours that can be fully customized to your interests, schedule, and budget. Contact us to discuss your preferences.</p>
                </div>

                <div class="faq-item">
                    <h3 class="faq-item__question">
                        <i class="fas fa-question-circle"></i>
                        What's your cancellation policy?
                    </h3>
                    <p class="faq-item__answer">Cancellation policies vary by tour. Check the specific tour page for details. Generally, we offer full refunds for cancellations made 7+ days before departure.</p>
                </div>

                <div class="faq-item">
                    <h3 class="faq-item__question">
                        <i class="fas fa-question-circle"></i>
                        Do I need a visa for Uzbekistan?
                    </h3>
                    <p class="faq-item__answer">Many nationalities can enter Uzbekistan visa-free for up to 30 days. We'll provide visa guidance during the booking process based on your nationality.</p>
                </div>

                <div class="faq-item">
                    <h3 class="faq-item__question">
                        <i class="fas fa-question-circle"></i>
                        Are your guides English-speaking?
                    </h3>
                    <p class="faq-item__answer">Yes, all our tours include professional English-speaking guides. We also offer tours in Russian, Japanese, and other languages upon request.</p>
                </div>
            </div>
        </div>
    </section>@endsection

@push('scripts')
<script>
    // Smooth scroll to top when clicking pagination links
    document.addEventListener('DOMContentLoaded', function() {
        const paginationLinks = document.querySelectorAll('.pagination-wrapper a');

        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Let the default navigation happen, but scroll to tours section
                setTimeout(() => {
                    const toursSection = document.getElementById('main-content');
                    if (toursSection) {
                        toursSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 100);
            });
        });
    });
</script>
@endpush
