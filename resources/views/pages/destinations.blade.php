@extends('layouts.main')

@section('title', 'Destinations in Uzbekistan - Explore Ancient Cities | Jahongir Travel')
@section('meta_description', 'Discover the historic cities and destinations of Uzbekistan. From Samarkand to Bukhara, explore the jewels of the Silk Road with Jahongir Travel.')
@section('meta_keywords', 'Uzbekistan destinations, Samarkand, Bukhara, Khiva, Tashkent, Silk Road cities')
@section('canonical', 'https://jahongirtravel.com/destinations')

@push('styles')
<style>
    .destinations-hero {
        background: linear-gradient(135deg, #1a5490 0%, #2c7abf 100%);
        padding: 120px 0 80px;
        color: white;
        text-align: center;
    }

    .destinations-hero__title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .destinations-hero__subtitle {
        font-size: 1.25rem;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }

    .destinations-grid {
        padding: 80px 0;
    }

    .destinations-grid__title {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 3rem;
        color: #1a1a1a;
    }

    .destinations-grid__container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .destination-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: white;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .destination-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .destination-card__image {
        width: 100%;
        height: 250px;
        object-fit: cover;
    }

    .destination-card__content {
        padding: 1.5rem;
    }

    .destination-card__name {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #1a5490;
    }

    .destination-card__tagline {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 1rem;
    }

    .destination-card__description {
        font-size: 0.95rem;
        color: #444;
        margin-bottom: 1rem;
        line-height: 1.6;
    }

    .destination-card__meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }

    .destination-card__tours {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #666;
        font-size: 0.9rem;
    }

    .destination-card__cta {
        margin-left: auto;
        color: #1a5490;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .loading-skeleton {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
    }

    .skeleton-card {
        height: 400px;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 12px;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    @media (max-width: 768px) {
        .destinations-hero {
            padding: 80px 0 60px;
        }

        .destinations-hero__title {
            font-size: 2rem;
        }

        .destinations-hero__subtitle {
            font-size: 1rem;
        }

        .destinations-grid {
            padding: 60px 0;
        }

        .destinations-grid__title {
            font-size: 2rem;
        }

        .destinations-grid__container {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
    }
</style>
@endpush

@section('content')

    <!-- =====================================================
         HERO SECTION
         ===================================================== -->
    <section class="destinations-hero">
        <div class="container">
            <h1 class="destinations-hero__title">Explore Uzbekistan</h1>
            <p class="destinations-hero__subtitle">Discover ancient Silk Road cities, stunning architecture, and rich cultural heritage</p>
        </div>
    </section>

    <!-- =====================================================
         DESTINATIONS GRID
         ===================================================== -->
    <section class="destinations-grid" id="main-content">
        <div class="container">
            <h2 class="destinations-grid__title">Our Destinations</h2>

            <div id="destinations-container" class="destinations-grid__container">
                <!-- Loading Skeleton -->
                <div class="loading-skeleton">
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

        console.log('[Destinations] Initializing...');

        // Fetch cities
        fetch('{{ url('/api/cities') }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(cities => {
                console.log('[Destinations] Cities loaded:', cities.length);
                renderDestinations(cities);
            })
            .catch(error => {
                console.error('[Destinations] Error loading cities:', error);
                showErrorState();
            });

        function renderDestinations(cities) {
            const container = document.getElementById('destinations-container');

            if (!cities || cities.length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 3rem; grid-column: 1/-1;">
                        <i class="fas fa-map-marked-alt fa-3x" style="color: #ccc; margin-bottom: 1rem;"></i>
                        <h3>No destinations available</h3>
                        <p style="color: #666;">Check back soon for amazing destinations!</p>
                    </div>
                `;
                return;
            }

            const html = cities.map(city => `
                <a href="/destinations/${city.slug}/" class="destination-card">
                    <img src="${city.featured_image || city.hero_image || '/images/default-city.jpg'}"
                         alt="${city.name}"
                         class="destination-card__image"
                         loading="lazy">
                    <div class="destination-card__content">
                        <h3 class="destination-card__name">${city.name}</h3>
                        ${city.tagline ? `<p class="destination-card__tagline">${city.tagline}</p>` : ''}
                        <p class="destination-card__description">
                            ${city.short_description || city.description || 'Explore this beautiful destination'}
                        </p>
                        <div class="destination-card__meta">
                            <div class="destination-card__tours">
                                <i class="fas fa-map-marked-alt"></i>
                                <span>${city.tour_count || 0} ${(city.tour_count || 0) === 1 ? 'tour' : 'tours'}</span>
                            </div>
                            <div class="destination-card__cta">
                                Explore <i class="fas fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </a>
            `).join('');

            container.innerHTML = html;
        }

        function showErrorState() {
            const container = document.getElementById('destinations-container');
            container.innerHTML = `
                <div style="text-align: center; padding: 3rem; grid-column: 1/-1;">
                    <i class="fas fa-exclamation-triangle fa-3x" style="color: #e74c3c; margin-bottom: 1rem;"></i>
                    <h3>Error Loading Destinations</h3>
                    <p style="color: #666;">We couldn't load the destinations. Please try again later.</p>
                    <a href="/" class="btn btn--primary" style="margin-top: 1rem;">Go to Homepage</a>
                </div>
            `;
        }
    })();
</script>
@endpush
