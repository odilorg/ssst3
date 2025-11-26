@extends('layouts.main')

@php
    $initialTours = $tours ?? collect();
    $toursJson = $initialTours->map(function ($tour) {
        return [
            'id' => $tour->id,
            'slug' => $tour->slug,
            'title' => $tour->title,
            'description' => \Illuminate\Support\Str::limit(strip_tags($tour->long_description ?? ''), 120),
            'short_description' => $tour->short_description,
            'featured_image' => $tour->featured_image_url ?? asset('images/default-tour.jpg'),
            'price_per_person' => $tour->price_per_person,
            'duration' => $tour->duration_days,
            'city_name' => optional($tour->city)->name,
            'city_slug' => optional($tour->city)->slug,
        ];
    })->values();
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


            <div id="tours-container" class="tours-grid__container" data-server-rendered="true">
                @forelse($initialTours as $tour)
                    <a href="/tours/{{ $tour->slug }}" class="tour-card">
                        <img src="{{ $tour->featured_image_url ?? asset('images/default-tour.jpg') }}"
                             alt="{{ $tour->title }}"
                             class="tour-card__image"
                             width="400"
                             height="300"
                             loading="lazy">
                        <div class="tour-card__content">
                            <h3 class="tour-card__title">{{ $tour->title }}</h3>
                            <p class="tour-card__description">
                                {{ !empty($tour->short_description) ? $tour->short_description : \Illuminate\Support\Str::limit(strip_tags($tour->long_description ?? ''), 120) }}
                            </p>
                            <div class="tour-card__meta">
                                <div class="tour-card__meta-item">
                                    <i class="far fa-clock"></i>
                                    <span>
                                        @if($tour->duration_days === 1)
                                            1 day
                                        @elseif($tour->duration_days)
                                            {{ $tour->duration_days }} days
                                        @else
                                            Flexible
                                        @endif
                                    </span>
                                </div>
                                @if($tour->city)
                                    <div class="tour-card__meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ $tour->city->name }}</span>
                                    </div>
                                @endif
                                <div class="tour-card__price">
                                    @if($tour->price_per_person)
                                        ${{ number_format($tour->price_per_person, 0) }}
                                    @else
                                        Contact us
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div style="text-align: center; padding: 3rem; grid-column: 1/-1;">
                        <i class="fas fa-map fa-3x" style="color: #ccc; margin-bottom: 1rem;"></i>
                        <h3>No tours found</h3>
                        <p style="color: #666;">We couldn&apos;t find any tours at the moment. Please check back soon!</p>
                    </div>
                @endforelse
            </div>

<!-- Loading Indicator for Infinite Scroll -->            <div id="loading-indicator" style="visibility: hidden; text-align: center; padding: 2rem;">                <i class="fas fa-spinner fa-spin fa-2x" style="color: #1a5490;"></i>                <p style="margin-top: 1rem; color: #666;">Loading more tours...</p>            </div>            <div id="end-message" style="display: none; text-align: center; padding: 2rem; color: #666;">                <i class="fas fa-check-circle" style="color: #10b981; margin-right: 0.5rem;"></i>                All tours loaded            </div>
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
    </section>

    <!-- =====================================================
         FLOATING WhatsApp CTA
         ===================================================== -->
    <a href="https://wa.me/{{ env('WHATSAPP_NUMBER', '998915550808') }}" target="_blank" rel="noopener" class="floating-whatsapp" aria-label="Contact us on WhatsApp">
        <i class="fab fa-whatsapp"></i>
        <span class="floating-whatsapp__text">WhatsApp</span>
    </a>

@endsection

@push('scripts')
<script>
    window.__INITIAL_TOURS__ = @json($toursJson);
</script>
<script>
    (function() {
        'use strict';


        let allTours = window.__INITIAL_TOURS__ || [];
        let currentCategory = '';
        const shouldFetch = allTours.length === 0;

        if (shouldFetch) {
            fetch('{{ url('/api/tours') }}')
                .then(r => r.json())
                .then(tours => {
                    allTours = tours;
                    renderTours(tours);
                })
                .catch(error => {
                    console.error('[Tours Listing] Error loading data:', error);
                    showErrorState();
                });
        } else {
            renderTours(allTours);
        }

        function renderCategoryFilters(categories) {
            const container = document.getElementById('category-filters');
            const allButton = container.querySelector('[data-category=""]');

            categories.forEach(category => {
                const button = document.createElement('button');
                button.className = 'filter-tab';
                button.setAttribute('data-category', category.slug);
                button.textContent = category.name;
                button.addEventListener('click', () => filterByCategory(category.slug));
                container.appendChild(button);
            });

            // All button handler
            allButton.addEventListener('click', () => filterByCategory(''));
        }

        function filterByCategory(categorySlug) {
            currentCategory = categorySlug;

            // Update active tab
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.classList.toggle('active', tab.getAttribute('data-category') === categorySlug);
            });

            // Filter tours
            const filteredTours = categorySlug
                ? allTours.filter(tour => tour.category_slug === categorySlug)
                : allTours;

            renderTours(filteredTours);
        }

        @verbatim
        function renderTours(tours) {
            const container = document.getElementById('tours-container');
            const countElement = document.getElementById('tour-count');

            // Update count
            countElement.textContent = `${tours.length} ${tours.length === 1 ? 'tour' : 'tours'}`;

            if (!tours || tours.length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 3rem; grid-column: 1/-1;">
                        <i class="fas fa-map fa-3x" style="color: #ccc; margin-bottom: 1rem;"></i>
                        <h3>No tours found</h3>
                        <p style="color: #666;">Try selecting a different category or check back soon for new tours!</p>
                    </div>
                `;
                return;
            }

            const html = tours.map(tour => {
                const price = tour.price_per_person ? `$${tour.price_per_person}` : 'Contact us';
                const duration = tour.duration ? `${tour.duration} ${tour.duration === 1 ? 'day' : 'days'}` : 'Flexible';

                return `
                    <a href="/tours/${tour.slug}" class="tour-card">
                        <img src="${tour.featured_image || '/images/default-tour.jpg'}"
                             alt="${tour.title}"
                             class="tour-card__image"
                             width="400"
                             height="300"
                             loading="lazy">
                        <div class="tour-card__content">

                            <h3 class="tour-card__title">${tour.title}</h3>
                            <p class="tour-card__description">
                                ${tour.short_description || tour.description || 'Explore this amazing tour in Uzbekistan'}
                            </p>
                            <div class="tour-card__meta">
                                <div class="tour-card__meta-item">
                                    <i class="far fa-clock"></i>
                                    <span>${duration}</span>
                                </div>
                                ${tour.city_name ? `
                                <div class="tour-card__meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>${tour.city_name}</span>
                                </div>
                                ` : ''}
                                <div class="tour-card__price">${price}</div>
                            </div>
                        </div>
                    </a>
                `;
            }).join('');

            container.innerHTML = html;
        }
        @endverbatim

        function showErrorState() {
            const container = document.getElementById('tours-container');
            container.innerHTML = `
                <div style="text-align: center; padding: 3rem; grid-column: 1/-1;">
                    <i class="fas fa-exclamation-triangle fa-3x" style="color: #e74c3c; margin-bottom: 1rem;"></i>
                    <h3>Error Loading Tours</h3>
                    <p style="color: #666;">We couldn't load the tours. Please try again later.</p>
                    <a href="/" class="btn btn--primary" style="margin-top: 1rem;">Go to Homepage</a>
                </div>
            `;
        }

        // ============================================
        // INFINITE SCROLL IMPLEMENTATION
        // ============================================
        let currentPage = 1;
        let totalPages = {{ $tours->lastPage() }};
        let isLoading = false;
        const loadingIndicator = document.getElementById('loading-indicator');
        const endMessage = document.getElementById('end-message');
        const toursContainer = document.getElementById('tours-container');
        const tourCountElement = document.getElementById('tour-count');
        const totalTours = {{ $tours->total() }};
        let loadedTours = {{ $tours->count() }};

        // Update initial count
        tourCountElement.textContent = `Showing ${loadedTours} of ${totalTours} tours`;

        // Intersection Observer for infinite scroll
        const observerOptions = {
            root: null,
            rootMargin: '200px', // Start loading 200px before reaching the bottom
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !isLoading && currentPage < totalPages) {
                    loadMoreTours();
                }
            });
        }, observerOptions);

        // Observe the loading indicator
        observer.observe(loadingIndicator);

        async function loadMoreTours() {
            if (isLoading || currentPage >= totalPages) return;

            isLoading = true;
            loadingIndicator.style.visibility = 'visible';

            try {
                const nextPage = currentPage + 1;
                const url = `{{ route('tours.index') }}?page=${nextPage}`;

                const response = await fetch(url);
                const html = await response.text();

                // Parse the HTML response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Extract tour cards from the response
                const newTours = doc.querySelectorAll('#tours-container .tour-card');

                if (newTours.length > 0) {
                    // Append new tour cards
                    newTours.forEach(tour => {
                        toursContainer.appendChild(tour);
                    });

                    currentPage = nextPage;
                    loadedTours += newTours.length;

                    // Update count
                    if (loadedTours >= totalTours) {
                        tourCountElement.textContent = `${totalTours} tours`;
                    } else {
                        tourCountElement.textContent = `Showing ${loadedTours} of ${totalTours} tours`;
                    }

                    // Check if we've loaded all pages
                    if (currentPage >= totalPages) {
                        observer.disconnect();
                        loadingIndicator.style.visibility = 'hidden';
                        endMessage.style.visibility = 'visible';
                    }
                } else {
                    observer.disconnect();
                    loadingIndicator.style.visibility = 'hidden';
                    endMessage.style.visibility = 'visible';
                }

            } catch (error) {
                console.error('[Infinite Scroll] Error loading more tours:', error);
                loadingIndicator.innerHTML = `
                    <p style="color: #e74c3c;">
                        <i class="fas fa-exclamation-circle"></i>
                        Error loading tours. <a href="#" onclick="location.reload(); return false;">Refresh page</a>
                    </p>
                `;
            } finally {
                isLoading = false;
                if (currentPage < totalPages) {
                    loadingIndicator.style.visibility = 'hidden';
                }
            }
        }

    })();
</script>
@endpush
