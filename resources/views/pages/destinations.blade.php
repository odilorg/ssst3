@extends('layouts.main')

@section('title', 'Destinations in Uzbekistan - Explore Ancient Cities | Jahongir Travel')
@section('meta_description', 'Discover the historic cities and destinations of Uzbekistan. From Samarkand to Bukhara, explore the jewels of the Silk Road with Jahongir Travel.')
@section('meta_keywords', 'Uzbekistan destinations, Samarkand, Bukhara, Khiva, Tashkent, Silk Road cities')
@section('canonical', 'https://jahongirtravel.com/destinations')

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

    <!-- =====================================================
         FOOTER
         ===================================================== -->
@endsection
