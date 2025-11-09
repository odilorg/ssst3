@extends('layouts.main')

@section('title', 'Uzbekistan Tours - Browse All Tours | Jahongir Travel')
@section('meta_description', 'Explore all available tours in Uzbekistan. From cultural heritage tours to mountain adventures, find your perfect Silk Road journey with Jahongir Travel.')
@section('meta_keywords', 'Uzbekistan tours, all tours, tour packages, Silk Road tours, Central Asia travel')
@section('canonical', 'https://jahongirtravel.com/tours')

@section('content')

    <!-- =====================================================
         HERO SECTION
         ===================================================== -->
    <section class="tours-hero">
        <div class="container">
            <h1 class="tours-hero__title">Discover Amazing Tours</h1>
            <p class="tours-hero__subtitle">Handcrafted journeys through the heart of the Silk Road - cultural experiences, historical tours, and authentic adventures</p>
        </div>
    </section>

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

            <div id="tours-container" class="tours-grid__container">
                <!-- Loading Skeleton -->
                <div class="loading-skeleton">
                    <div class="skeleton-card"></div>
                    <div class="skeleton-card"></div>
                    <div class="skeleton-card"></div>
                    <div class="skeleton-card"></div>
                    <div class="skeleton-card"></div>
                    <div class="skeleton-card"></div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script>
    (function() {
        'use strict';

        console.log('[Tours Listing] Initializing...');

        let allTours = [];
        let currentCategory = '';

        // Fetch all tours
        fetch('{{ url('/api/tours') }}')
            .then(r => r.json())
            .then(tours => {
                console.log('[Tours Listing] Loaded:', tours.length, 'tours');
                allTours = tours;
                renderTours(tours);
            })
        .catch(error => {
            console.error('[Tours Listing] Error loading data:', error);
            showErrorState();
        });

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
