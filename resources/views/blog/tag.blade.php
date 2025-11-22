@extends('layouts.main')

@section('title', $tag->meta_title ?? ($tag->name . ' Articles | Jahongir Travel Blog'))
@section('meta_description', $tag->meta_description ?? ('Explore articles about ' . $tag->name . '. Expert travel tips and guides from Jahongir Travel.'))
@section('canonical', url('/blog/tag/' . $tag->slug))

{{-- Open Graph --}}
@section('og_type', 'website')
@section('og_url', url('/blog/tag/' . $tag->slug))
@section('og_title', $tag->name . ' | Travel Blog')
@section('og_description', $tag->meta_description ?? ('Articles tagged with ' . $tag->name))
@section('og_image', asset('images/og-blog.jpg'))

{{-- Structured Data for Tag Page --}}
@section('structured_data')
{
  "@@context": "https://schema.org",
  "@@type": "CollectionPage",
  "name": "{{ $tag->name }} Articles",
  "description": "{{ $tag->meta_description ?? ('Articles tagged with ' . $tag->name) }}",
  "url": "{{ url('/blog/tag/' . $tag->slug) }}",
  "isPartOf": {
    "@@type": "WebSite",
    "name": "Jahongir Travel",
    "url": "{{ url('/') }}"
  }
}
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('blog-listing.css') }}?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('blog-pagination-fix.css') }}?v={{ time() }}">
@endpush

@section('content')

<!-- =====================================================
     TAG HERO SECTION
     ===================================================== -->
<section class="blog-hero" style="position: relative; height: 350px; background: linear-gradient(135deg, #0D4C92 0%, #1565C0 100%); display: flex; align-items: center; justify-content: center; overflow: hidden;">
    <div class="container">
        <div class="blog-hero__content" style="text-align: center; color: #FFFFFF;">
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.2); padding: 0.5rem 1.25rem; border-radius: 50px; margin-bottom: 1.5rem;">
                <i class="fas fa-tag" style="font-size: 0.875rem;"></i>
                <span style="font-size: 0.875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Tag</span>
            </div>
            <h1 class="blog-hero__title" style="font-family: 'Playfair Display', serif; font-size: 3rem; font-weight: 700; margin: 0 0 1rem 0; color: #FFFFFF;">{{ $tag->name }}</h1>
            @if($tag->description)
                <p class="blog-hero__subtitle" style="font-size: 1.125rem; font-weight: 400; margin: 0; color: rgba(255,255,255,0.95); max-width: 700px; margin: 0 auto;">{{ $tag->description }}</p>
            @endif
            <p style="margin-top: 1rem; font-size: 0.938rem; color: rgba(255,255,255,0.85);">
                <i class="fas fa-file-alt"></i> {{ $tag->posts_count }} {{ Str::plural('article', $tag->posts_count) }}
            </p>
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
            <li style="display: flex; align-items: center;">
                <a href="{{ route('blog.index') }}" style="color: #1a5490; text-decoration: none;">Blog</a>
                <span style="margin: 0 0.5rem; color: #666;">/</span>
            </li>
            <li style="color: #666; font-weight: 500;" aria-current="page">{{ $tag->name }}</li>
        </ol>
    </div>
</nav>

<!-- =====================================================
     FILTERS & NAVIGATION
     ===================================================== -->
<section class="blog-filters">
    <div class="container">

        <!-- Active Filter Badge -->
        <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 0.938rem; color: #666;">Showing articles tagged with:</span>
            <span style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, #0D4C92 0%, #1565C0 100%); color: white; border-radius: 50px; font-weight: 500; font-size: 0.875rem;">
                <i class="fas fa-tag"></i> {{ $tag->name }}
            </span>
            <a href="{{ route('blog.index') }}" style="color: #0D4C92; text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                <i class="fas fa-times"></i> View all articles
            </a>
        </div>

        <!-- Sort Dropdown -->
        <form method="GET" action="{{ route('blog.tag', $tag->slug) }}" class="blog-sort">
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
                <i class="fas fa-tag"></i>
                <h2>No articles found with this tag</h2>
                <p>Check back later for new content.</p>
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

        <!-- Related Tags -->
        @if($tags->count() > 1)
        <div style="margin-top: 4rem; padding-top: 2rem; border-top: 2px solid #E8E8E8;">
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem; color: #1E1E1E;">
                <i class="fas fa-tags"></i> Explore Other Topics
            </h3>
            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                @foreach($tags->where('slug', '!=', $tag->slug)->take(10) as $relatedTag)
                    <a href="{{ route('blog.tag', $relatedTag->slug) }}"
                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.125rem; background: #F8F9FA; border: 2px solid #E8E8E8; border-radius: 50px; color: #1E1E1E; text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease;">
                        <i class="fas fa-tag" style="font-size: 0.75rem; opacity: 0.8;"></i>
                        <span>{{ $relatedTag->name }}</span>
                        <span style="background: rgba(0,0,0,0.1); padding: 0.125rem 0.5rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">{{ $relatedTag->posts_count }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</section>

@endsection

@push('scripts')
<script src="{{ asset('js/blog-listing.js') }}" defer></script>
@endpush
