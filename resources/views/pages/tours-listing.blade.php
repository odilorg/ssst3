@extends('layouts.main')

@section('title', 'Uzbekistan Tours - Browse All Tours | Jahongir Travel')
@section('meta_description', 'Explore all available tours in Uzbekistan. From cultural heritage tours to mountain adventures, find your perfect Silk Road journey with Jahongir Travel.')
@section('meta_keywords', 'Uzbekistan tours, all tours, tour packages, Silk Road tours, Central Asia travel')
@section('canonical', 'https://jahongirtravel.com/tours')

@push('styles')
<style>

    .tours-hero {
        background: url("/images/hero-registan.webp") center/cover no-repeat;
        padding: 120px 0 80px;
        color: white;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .tours-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(26,84,144,0.65) 0%, rgba(44,122,191,0.55) 100%),
                    linear-gradient(rgba(0,0,0,0.35) 0%, rgba(0,0,0,0.25) 40%, rgba(0,0,0,0.55) 100%);
        z-index: 1;
        pointer-events: none;
    }

    .tours-hero .container {
        position: relative;
        z-index: 2;
    }

    .tours-hero__title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.30);
    }

    .tours-hero__subtitle {
        font-size: 1.25rem;
        color: rgba(255, 255, 255, 0.85);
        max-width: 700px;
        margin: 0 auto;
        text-shadow: 0 1px 3px rgba(0,0,0,0.25);
    }



















    .tours-grid {
        padding: 80px 0;
    }

    .tours-grid__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 3rem;
    }

    .tours-grid__title {
        font-size: 2.5rem;
        color: #1a1a1a;
    }

    .tours-grid__count {
        font-size: 1.1rem;
        color: #666;
    }

    .tours-grid__container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2rem;
    }

    .tour-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: white;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
    }

    .tour-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .tour-card__image {
        width: 100%;
        height: 220px;
        object-fit: cover;
    }

    .tour-card__content {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .tour-card__category {
        display: inline-block;
        background: #e3f2fd;
        color: #1a5490;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-bottom: 0.75rem;
        width: fit-content;
    }

    .tour-card__title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #1a1a1a;
        line-height: 1.4;
    }

    .tour-card__description {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 1rem;
        line-height: 1.6;
        flex: 1;
    }

    .tour-card__meta {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #eee;
        font-size: 0.9rem;
        color: #666;
    }

    .tour-card__meta-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .tour-card__price {
        margin-left: auto;
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a5490;
    }

    .loading-skeleton {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2rem;
    }

    .skeleton-card {
        height: 420px;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 12px;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .filter-tabs {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .filter-tab {
        padding: 0.75rem 1.5rem;
        border: 2px solid #ddd;
        border-radius: 25px;
        background: white;
        color: #666;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .filter-tab:hover {
        border-color: #1a5490;
        color: #1a5490;
    }

    .filter-tab.active {
        background: #1a5490;
        color: white;
        border-color: #1a5490;
    }

    @media (max-width: 768px) {

        .tours-hero {
            padding: 120px 0 60px;
        }

        .tours-hero::before {
            background: linear-gradient(135deg, rgba(26,84,144,0.7) 0%, rgba(44,122,191,0.6) 100%),
                        linear-gradient(rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.65) 40%, rgba(0,0,0,0.80) 100%);
        }

        .tours-hero__title {
            font-size: clamp(1.6rem, 4.5vw, 2.2rem);
            line-height: 1.3;
        }

        .tours-hero__subtitle {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
        }











        .tours-grid {
            padding: 60px 0;
        }

        .tours-grid .container {
            padding-inline: 0 !important;
        }

        .tours-grid__header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
            padding-inline: 1rem;
        }

        .tours-grid__title {
            font-size: 2rem;
        }

        .tours-grid__container {
            grid-template-columns: 1fr;
            gap: 0.75rem;
            padding-inline: 0.25rem;
        }

        .filter-tabs {
            overflow-x: auto;
            flex-wrap: nowrap;
            padding-bottom: 0.5rem;
        }

        .filter-tab {
            white-space: nowrap;
        }

        .tour-card {
            border-radius: 8px;
        }
    }
</style>
@endpush

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
