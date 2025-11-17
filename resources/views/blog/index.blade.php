@extends('layouts.main')

@section('title', 'Travel Blog & Guides | Jahongir Travel')
@section('meta_description', 'Explore expert travel tips, destination guides, and cultural insights for your Uzbekistan adventure. Latest articles from our travel experts.')
@section('meta_keywords', 'Uzbekistan travel blog, travel tips, destination guides, Silk Road culture')

@push('styles')
<link rel="stylesheet" href="{{ asset('blog-listing.css') }}?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('blog-pagination-fix.css') }}?v={{ time() }}">
<style>
    /* Blog Hero Section */
    .blog-hero {
        position: relative;
        height: 400px;
        background-image: url('images/hero-registan.webp');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .blog-hero__overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0.60) 100%);
        z-index: 1;
    }

    .blog-hero__content {
        position: relative;
        z-index: 2;
        text-align: center;
        color: #FFFFFF;
    }

    .blog-hero__title {
        font-family: 'Playfair Display', serif;
        font-size: 56px;
        font-weight: 700;
        line-height: 1.2;
        margin: 0 0 16px 0;
        letter-spacing: -0.5px;
        color: #FFFFFF;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.45);
    }

    .blog-hero__subtitle {
        font-family: 'Inter', sans-serif;
        font-size: 18px;
        font-weight: 400;
        line-height: 1.6;
        margin: 0;
        color: #FFFFFF;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.45);
    }

    @media (max-width: 768px) {
        .blog-hero {
            height: 300px;
            margin-top: 100px;
        }

        .blog-hero__title {
            font-size: 36px;
        }

        .blog-hero__subtitle {
            font-size: 16px;
        }
    }
</style>
@endpush

@section('content')

<!-- =====================================================
     BLOG HERO SECTION
     ===================================================== -->
<section class="blog-hero">
    <div class="blog-hero__overlay"></div>
    <div class="container">
        <div class="blog-hero__content">
            <h1 class="blog-hero__title">Travel Insights & Tips</h1>
            <p class="blog-hero__subtitle">Expert travel advice and destination guides</p>
        </div>
    </div>
</section>

<!-- =====================================================
     FILTERS & SEARCH
     ===================================================== -->
<section class="blog-filters">
    <div class="container">

        <!-- Search Form -->
        <form method="GET" action="{{ route('blog.index') }}" class="blog-search">
            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search articles..."
                aria-label="Search blog articles">
            <button type="submit" aria-label="Search">
                <i class="fas fa-search"></i>
            </button>
        </form>

        <!-- Category Filter Pills -->
        <div class="blog-categories-wrapper">
            <div class="blog-categories">
                <a href="{{ route('blog.index') }}"
                   class="blog-category-btn {{ !request('category') ? 'active' : '' }}">
                    <span class="category-icon">📚</span>
                    <span class="category-label">All Articles</span>
                    <span class="category-count">{{ $posts->total() }}</span>
                </a>
                @foreach($categories as $category)
                    @php
                        $icons = [
                            'culture-heritage' => '🎭',
                            'religion-spirituality' => '🕌',
                            'travel-tips' => '✈️',
                            'destinations' => '🗺️',
                            'food-cuisine' => '🍽️',
                            'history' => '📜',
                            'adventure' => '🏔️',
                            'photography' => '📸',
                        ];
                        $icon = $icons[$category->slug] ?? '📝';
                    @endphp
                    <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                       class="blog-category-btn {{ request('category') === $category->slug ? 'active' : '' }}"
                       data-category="{{ $category->slug }}">
                        <span class="category-icon">{{ $icon }}</span>
                        <span class="category-label">{{ $category->name }}</span>
                        <span class="category-count">{{ $category->posts_count }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Sort Dropdown -->
        <form method="GET" action="{{ route('blog.index') }}" class="blog-sort">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            <label for="sortBy">Sort by:</label>
            <select id="sortBy" name="sort" onchange="this.form.submit()">
                <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Latest</option>
                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
            </select>
        </form>

    </div>
</section>

<!-- =====================================================
     BLOG GRID
     ===================================================== -->
<section class="blog-listing">
    <div class="container">

        @if($posts->isEmpty())
            <!-- Empty State -->
            <div class="blog-empty">
                <i class="fas fa-search"></i>
                <h2>No articles found</h2>
                <p>Try adjusting your filters or search query.</p>
                <a href="{{ route('blog.index') }}" class="btn btn--primary">View All Articles</a>
            </div>
        @else
            <!-- Blog Grid -->
            <div class="blog-grid" id="blogGrid">
                @foreach($posts as $post)
                    @include('partials.blog.card', ['post' => $post])
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="blog-pagination">
                {{ $posts->links('pagination::default') }}
            </div>
        @endif

    </div>
</section>

@endsection

@push('scripts')
<script src="{{ asset('js/htmx.min.js') }}"></script>
<script src="{{ asset('js/blog-listing.js') }}" defer></script>
@endpush
