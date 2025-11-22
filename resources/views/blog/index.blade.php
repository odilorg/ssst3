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
            <h1 class="blog-hero__title">Travel Insights & Tips</h1>
            <p class="blog-hero__subtitle">Expert travel advice and destination guides</p>
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
            <li style="color: #666; font-weight: 500;" aria-current="page">Blog</li>
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
                    <span class="category-icon"><i class="fas fa-book-open"></i></span>
                    <span class="category-label">All Articles</span>
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

        <!-- Tag Filter Pills -->
        @if($tags->isNotEmpty())
        <div class="blog-tags-wrapper">
            <h3 class="filter-section-title">
                <i class="fas fa-tags"></i> Filter by Tags
            </h3>
            <div class="blog-tags-filter">
                @foreach($tags as $tag)
                    <a href="{{ route('blog.index', array_merge(request()->except('page'), ['tag' => $tag->slug])) }}"
                       class="blog-tag-btn {{ request('tag') === $tag->slug ? 'active' : '' }}"
                       data-tag="{{ $tag->slug }}">
                        <span class="tag-icon"><i class="fas fa-tag"></i></span>
                        <span class="tag-label">{{ $tag->name }}</span>
                        <span class="tag-count">{{ $tag->posts_count }}</span>
                    </a>
                @endforeach
                @if(request('tag'))
                    <a href="{{ route('blog.index', request()->except(['tag', 'page'])) }}"
                       class="blog-tag-btn clear-tag">
                        <span class="tag-icon"><i class="fas fa-times"></i></span>
                        <span class="tag-label">Clear Tag</span>
                    </a>
                @endif
            </div>
        </div>
        @endif

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

<!-- =====================================================
     FLOATING WhatsApp CTA
     ===================================================== -->
<a href="https://wa.me/998901234567" target="_blank" rel="noopener" class="floating-whatsapp" aria-label="Contact us on WhatsApp">
    <i class="fab fa-whatsapp"></i>
    <span class="floating-whatsapp__text">WhatsApp</span>
</a>

<style>
    .floating-whatsapp {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #25D366;
        color: white;
        padding: 12px 20px;
        border-radius: 50px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
        z-index: 1000;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .floating-whatsapp:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37, 211, 102, 0.5);
    }
    .floating-whatsapp i {
        font-size: 1.25rem;
    }
    @media (max-width: 768px) {
        .floating-whatsapp__text {
            display: none;
        }
        .floating-whatsapp {
            padding: 14px;
            border-radius: 50%;
        }
    }
</style>

@endsection

@push('scripts')
<script src="{{ asset('js/htmx.min.js') }}"></script>
<script src="{{ asset('js/blog-listing.js') }}" defer></script>
@endpush
