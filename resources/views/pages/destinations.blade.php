@extends('layouts.main')

@section('title', 'Destinations in Uzbekistan - Explore Ancient Cities | Jahongir Travel')
@section('meta_description', 'Discover the historic cities and destinations of Uzbekistan. From Samarkand to Bukhara, explore the jewels of the Silk Road with Jahongir Travel.')
@section('meta_keywords', 'Uzbekistan destinations, Samarkand, Bukhara, Khiva, Tashkent, Silk Road cities')
@section('canonical', url('/destinations'))

@push('styles')
<style>

    .destinations-hero {
        position: relative;
        height: 400px;
        background-image: url('/images/hero-registan.webp');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .destinations-hero__overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0.60) 100%);
        z-index: 1;
    }

    .destinations-hero__content {
        position: relative;
        z-index: 2;
        text-align: center;
        color: #FFFFFF;
    }

    .destinations-hero__title {
        font-family: 'Playfair Display', serif;
        font-size: 56px;
        font-weight: 700;
        line-height: 1.2;
        margin: 0 0 16px 0;
        letter-spacing: -0.5px;
        color: #FFFFFF;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.45);
    }

    .destinations-hero__subtitle {
        font-family: 'Inter', sans-serif;
        font-size: 18px;
        font-weight: 400;
        line-height: 1.6;
        margin: 0;
        color: #FFFFFF;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.45);
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
        color: #1a5490;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-left: auto;
    }

    .destinations-empty {
        text-align: center;
        padding: 3rem;
        grid-column: 1 / -1;
    }

    .destinations-empty i {
        color: #ccc;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .destinations-hero {
            height: 300px;
            margin-top: 100px;
        }

        .destinations-hero__title {
            font-size: 36px;
        }

        .destinations-hero__subtitle {
            font-size: 16px;
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
        <div class="destinations-hero__overlay"></div>
        <div class="container">
            <div class="destinations-hero__content">
                <h1 class="destinations-hero__title">Explore Uzbekistan</h1>
                <p class="destinations-hero__subtitle">Discover ancient Silk Road cities, stunning architecture, and rich cultural heritage</p>
            </div>
        </div>
    </section>

    <!-- =====================================================
         DESTINATIONS GRID (SSR)
         ===================================================== -->
    <section class="destinations-grid" id="main-content">
        <div class="container">
            <h2 class="destinations-grid__title">Our Destinations</h2>

            <div class="destinations-grid__container">
                @forelse($cities as $city)
                    <a href="/destinations/{{ $city->slug }}" class="destination-card">
                        <img src="{{ $city->featured_image_url ?? $city->hero_image_url ?? asset('images/default-city.jpg') }}"
                             alt="{{ $city->name }}"
                             class="destination-card__image"
                             loading="lazy">
                        <div class="destination-card__content">
                            <h3 class="destination-card__name">{{ $city->name }}</h3>
                            @if($city->tagline)
                                <p class="destination-card__tagline">{{ $city->tagline }}</p>
                            @endif
                            <p class="destination-card__description">
                                {{ Str::limit($city->short_description ?? 'Explore this beautiful destination', 150) }}
                            </p>
                            <div class="destination-card__meta">
                                <div class="destination-card__tours">
                                    <i class="fas fa-map-marked-alt"></i>
                                    <span>{{ $city->tour_count }} {{ $city->tour_count === 1 ? 'tour' : 'tours' }}</span>
                                </div>
                                <div class="destination-card__cta">
                                    Explore <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="destinations-empty">
                        <i class="fas fa-map-marked-alt fa-3x"></i>
                        <h3>No destinations available</h3>
                        <p style="color: #666;">Check back soon for amazing destinations!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
