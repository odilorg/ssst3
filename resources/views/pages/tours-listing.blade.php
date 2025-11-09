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

    <!-- =====================================================
         FOOTER
         ===================================================== -->
@endsection
