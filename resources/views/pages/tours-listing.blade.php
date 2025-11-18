@extends('layouts.main')

@php
    $initialTours = $tours ?? collect();
    $toursJson = $initialTours->map(function ($tour) {
        return [
            'id' => $tour->id,
            'slug' => $tour->slug,
            'title' => $tour->title,
            'description' => $tour->long_description,
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
         TOURS GRID
         ===================================================== -->
    <section class="tours-grid" id="main-content">
        <div class="container">
            <div class="tours-grid__header">
                <h2 class="tours-grid__title">All Tours</h2>
                <div class="tours-grid__count" id="tour-count">Loading...</div>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs" id="category-filters" style="display: none;">
                <button class="filter-tab active" data-category="">All Tours</button>
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
                                {{ $tour->short_description ?? \Illuminate\Support\Str::limit(strip_tags($tour->long_description), 140) }}
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

            <!-- Pagination Links -->
            <div class="pagination-wrapper" style="margin-top: 3rem;">
                {{ $tours->links() }}
            </div>
        </div>
    </section>

    <!-- =====================================================
         FLOATING WhatsApp CTA
         ===================================================== -->
    <a href="https://wa.me/998901234567" target="_blank" rel="noopener" class="floating-whatsapp" aria-label="Contact us on WhatsApp">
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
    })();
</script>
@endpush
