@extends('layouts.main')

@section('title', 'Travel Blog & Guides | Jahongir Travel')
@section('meta_description', 'Explore expert travel tips, destination guides, and cultural insights for your Uzbekistan adventure. Latest articles from our travel experts.')
@section('canonical', url('/blog'))

{{-- Open Graph --}}
@section('og_type', 'website')
@section('og_url', url('/blog'))
@section('og_title', 'Travel Blog & Guides | Jahongir Travel')
@section('og_description', 'Explore expert travel tips, destination guides, and cultural insights for your Uzbekistan adventure.')
@section('og_image', asset('images/og-blog.jpg'))

{{-- Structured Data for Blog Listing --}}
@section('structured_data')
{
  "@@context": "https://schema.org",
  "@@type": "CollectionPage",
  "name": "Travel Blog & Guides",
  "description": "Explore expert travel tips, destination guides, and cultural insights for your Uzbekistan adventure.",
  "url": "{{ url('/blog') }}",
  "isPartOf": {
    "@@type": "WebSite",
    "name": "Jahongir Travel",
    "url": "{{ url('/') }}"
  },
  "publisher": {
    "@@type": "Organization",
    "name": "Jahongir Travel",
    "logo": {
      "@@type": "ImageObject",
      "url": "{{ asset('images/logo.png') }}"
    }
  }
}
@endsection

{{-- Breadcrumb Structured Data --}}
@push('structured_data_breadcrumb')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "{{ url('/') }}"
    },
    {
      "@@type": "ListItem",
      "position": 2,
      "name": "Blog",
      "item": "{{ url('/blog') }}"
    }
  ]
}
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('blog-listing.css') }}?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('blog-pagination-fix.css') }}?v={{ time() }}">
<style>
    /* Blog Hero Section */
    .blog-hero {
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
            <h1 class="blog-hero__title">{{ __('ui.blog_page.hero_title') }}</h1>
            <p class="blog-hero__subtitle">{{ __('ui.blog_page.hero_subtitle') }}</p>
        </div>
    </div>
</section>

<!-- Breadcrumb Navigation -->
<nav class="breadcrumb" aria-label="Breadcrumb" style="background: #f8f9fa; padding: 1rem 0;">
    <div class="container">
        <ol style="list-style: none; padding: 0; margin: 0; display: flex; align-items: center; flex-wrap: wrap;">
            <li style="display: flex; align-items: center;">
                <a href="{{ url('/') }}" style="color: #1a5490; text-decoration: none;">{{ __("ui.blog_page.breadcrumb_home") }}</a>
                <span style="margin: 0 0.5rem; color: #666;">/</span>
            </li>
            <li style="color: #666; font-weight: 500;" aria-current="page">{{ __('ui.blog_page.breadcrumb_blog') }}</li>
        </ol>
    </div>
</nav>

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
                placeholder="{{ __("ui.blog_page.search_placeholder") }}"
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
                    <span class="category-icon"><i class="fas fa-book-open"></i></span>
                    <span class="category-label">{{ __('ui.blog_page.all_articles') }}</span>
                    <span class="category-count">{{ $posts->total() }}</span>
                </a>
                @foreach($categories as $category)
                    @php
                        $icons = [
                            'culture-heritage' => 'fa-landmark',
                            'religion-spirituality' => 'fa-mosque',
                            'travel-tips' => 'fa-plane',
                            'destinations' => 'fa-map-marked-alt',
                            'food-cuisine' => 'fa-utensils',
                            'history' => 'fa-scroll',
                            'adventure' => 'fa-mountain',
                            'photography' => 'fa-camera',
                        ];
                        $icon = $icons[$category->slug] ?? 'fa-file-alt';
                    @endphp
                    <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                       class="blog-category-btn {{ request('category') === $category->slug ? 'active' : '' }}"
                       data-category="{{ $category->slug }}">
                        <span class="category-icon"><i class="fas {{ $icon }}"></i></span>
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
            @if(request('tag'))
                <input type="hidden" name="tag" value="{{ request('tag') }}">
            @endif
            <label for="sortBy">{{ __("ui.blog_page.sort_by") }}</label>
            <select id="sortBy" name="sort" onchange="this.form.submit()">
                <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>{{ __("ui.blog_page.sort_latest") }}</option>
                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>{{ __("ui.blog_page.sort_popular") }}</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>{{ __("ui.blog_page.sort_oldest") }}</option>
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
                <h2>{{ __('ui.blog_page.no_articles_title') }}</h2>
                <p>{{ __('ui.blog_page.no_articles_text') }}</p>
                <a href="{{ route('blog.index') }}" class="btn btn--primary">{{ __('ui.blog_page.view_all_articles') }}</a>
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
</section>@endsection

@push('scripts')
<script src="{{ asset('js/htmx.min.js') }}?v={{ filemtime(public_path('js/htmx.min.js')) }}"></script>
<script src="{{ asset('js/blog-listing.js') }}?v={{ filemtime(public_path('js/blog-listing.js')) }}" defer></script>
@endpush
