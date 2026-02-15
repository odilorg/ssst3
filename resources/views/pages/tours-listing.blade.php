@extends('layouts.main')

@php
    $initialTours = $tours ?? collect();
@endphp

@section('title', 'Uzbekistan Tours | Jahongir Travel')
@section('meta_description', 'Explore all available tours in Uzbekistan. From cultural heritage tours to mountain adventures, find your perfect Silk Road journey with Jahongir Travel.')
@section('canonical', url('/tours'))

@section('structured_data')
{!! $structuredData !!}
@endsection

@push('head')
<!-- Preload hero image for faster LCP -->
<link rel="preload" as="image" href="{{ asset('images/hero-registan.webp') }}" type="image/webp">
@endpush

@push('styles')<link rel="stylesheet" href="{{ asset('css/tours-listing.css') }}">@endpush

@section('content')

    <!-- =====================================================
         HERO SECTION
         ===================================================== -->
    <section class="tours-hero">
        <div class="tours-hero__overlay"></div>
        <div class="container">
            <div class="tours-hero__content">
                <h1 class="tours-hero__title">Discover Amazing Tours</h1>
                <p class="tours-hero__subtitle">Handcrafted journeys through the heart of the Silk Road - cultural experiences, historical tours, and authentic adventures</p>
                <a href="#main-content" class="hero-cta-btn">
                    Browse All Tours
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
                        <div class="trust-badge__number">500+</div>
                        <div class="trust-badge__label">Happy Travelers</div>
                    </div>
                </div>
                <div class="trust-badge">
                    <i class="fas fa-star"></i>
                    <div class="trust-badge__content">
                        <div class="trust-badge__number">4.7/5</div>
                        <div class="trust-badge__label">Average Rating</div>
                    </div>
                </div>
                <div class="trust-badge">
                    <i class="fas fa-certificate"></i>
                    <div class="trust-badge__content">
                        <div class="trust-badge__number">Licensed</div>
                        <div class="trust-badge__label">Tour Operator</div>
                    </div>
                </div>
                <div class="trust-badge">
                    <i class="fas fa-headset"></i>
                    <div class="trust-badge__content">
                        <div class="trust-badge__number">24/7</div>
                        <div class="trust-badge__label">Customer Support</div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- =====================================================
         FILTERS SECTION
         ===================================================== -->
    <section class="tours-filters">
        <div class="container">
            <!-- Category Filter Pills -->
            <div class="tours-categories-wrapper">
                <div class="tours-categories">
                    <a href="{{ route('tours.index') }}"
                       class="tours-category-btn {{ !request('category') ? 'active' : '' }}">
                        <span class="category-icon"><i class="fas fa-globe-asia"></i></span>
                        <span class="category-label">All Tours</span>
                        <span class="category-count">{{ $tours->total() }}</span>
                    </a>
                    @foreach($categories as $category)
                        @php
                            $icons = [
                                'city-tours' => 'fa-city',
                                'cultural-tours' => 'fa-landmark',
                                'day-trips' => 'fa-sun',
                                'multi-day-tours' => 'fa-route',
                                'adventure' => 'fa-mountain',
                                'food-tours' => 'fa-utensils',
                                'photography' => 'fa-camera',
                                'private-tours' => 'fa-user-shield',
                            ];
                            $icon = $icons[$category->slug] ?? 'fa-map-marked-alt';
                        @endphp
                        <a href="{{ route('tours.index', ['category' => $category->slug]) }}"
                           class="tours-category-btn {{ request('category') === $category->slug ? 'active' : '' }}">
                            <span class="category-icon"><i class="fas {{ $icon }}"></i></span>
                            <span class="category-label">{{ $category->translated_name }}</span>
                            <span class="category-count">{{ $category->tours_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <!-- =====================================================
         TOURS GRID
         ===================================================== -->
    <section class="tours-grid" id="main-content">
        <div class="container">
            <div class="tours-grid__header">
                <h2 class="tours-grid__title">All Tours</h2>
                <div class="tours-grid__count" id="tour-count">{{ $tours->total() }} tours available</div>
            </div>


            <div id="tours-container" class="tours-grid__container">
                @forelse($initialTours as $tour)
                    @php $tr = $tour->translationOrDefault(); @endphp
                    <article class="tour-card-o" data-tour-id="{{ $tour->id }}">
                        {{-- Background Image --}}
                        <img
                            src="{{ $tour->featured_image_url ?? asset('images/default-tour.webp') }}"
                            alt="{{ $tr->title ?? $tour->title }}"
                            class="tour-card-o__bg"
                            width="400"
                            height="500"
                            loading="lazy"
                        >
                        
                        <div class="tour-card-o__overlay"></div>

                        <div class="tour-card-o__content">
                            <div class="tour-card-o__top">
                                <span class="tour-card-o__badge">
                                    <i class="far fa-clock"></i>
                                    @if($tour->duration_days === 1)
                                        1 day
                                    @elseif($tour->duration_days)
                                        {{ $tour->duration_days }} days
                                    @else
                                        Flexible
                                    @endif
                                </span>
                                @if($tour->city)
                                <span class="tour-card-o__badge">
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $tour->city->name }}
                                </span>
                                @endif
                            </div>

                            <div class="tour-card-o__bottom">
                                <h3 class="tour-card-o__title">
                                    <a href="/{{ app()->getLocale() }}/tours/{{ $tr->slug ?? $tour->slug }}">
                                        {{ $tr->title ?? $tour->title }}
                                    </a>
                                </h3>

                                <p class="tour-card-o__description">
                                    {{ Str::limit($tr->excerpt ?? $tour->short_description ?? strip_tags($tour->long_description ?? ''), 90) }}
                                </p>

                                <div class="tour-card-o__footer">
                                    <div class="tour-card-o__price">
                                        <span class="tour-card-o__price-amount">
                                            @if($tour->price_per_person)
                                                ${{ number_format($tour->price_per_person, 0) }}
                                            @else
                                                Contact
                                            @endif
                                        </span>
                                    </div>
                                    <a href="/{{ app()->getLocale() }}/tours/{{ $tr->slug ?? $tour->slug }}" class="tour-card-o__btn">
                                        View Tour
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-map fa-3x"></i>
                        <h3>No tours found</h3>
                        <p>We couldn't find any tours at the moment. Please check back soon!</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination Links -->
            @if($tours->hasPages())
                <div class="pagination-wrapper">
                    {{ $tours->links('partials.pagination') }}
                </div>
            @endif
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
