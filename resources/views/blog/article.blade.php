@extends('layouts.main')

{{-- SEO Meta Tags - use translation data when available --}}
@section('title', (isset($translation) ? ($translation->seo_title ?? $translation->title) : ($post->meta_title ?? $post->title)) . ' | Jahongir Travel Blog')
@section('meta_description', isset($translation) ? ($translation->seo_description ?? $translation->excerpt ?? $post->meta_description ?? Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160)) : ($post->meta_description ?? $post->excerpt ?? Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160)))
@section('meta_keywords', implode(', ', array_merge(['Uzbekistan travel blog', 'travel tips', $post->category->name ?? 'travel'], $post->tags->pluck('name')->toArray())))
@section('canonical', isset($canonicalUrl) ? $canonicalUrl : url('/blog/' . $post->slug))

{{-- Open Graph Tags --}}
@section('og_type', 'article')
@section('og_title', isset($translation) ? ($translation->seo_title ?? $translation->title) : ($post->meta_title ?? $post->title))
@section('og_description', isset($translation) ? ($translation->seo_description ?? $translation->excerpt ?? $post->meta_description ?? Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160)) : ($post->meta_description ?? $post->excerpt ?? Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160)))
@section('og_image', $post->featured_image_url ?? asset('images/og-default.jpg'))
@section('og_url', isset($canonicalUrl) ? $canonicalUrl : url('/blog/' . $post->slug))

{{-- Twitter Card Tags --}}
@section('twitter_title', isset($translation) ? ($translation->seo_title ?? $translation->title) : ($post->meta_title ?? $post->title))
@section('twitter_description', isset($translation) ? ($translation->seo_description ?? $translation->excerpt ?? Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160)) : ($post->meta_description ?? $post->excerpt ?? Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160)))
@section('twitter_image', $post->featured_image_url ?? asset('images/og-default.jpg'))

{{-- Schema.org Structured Data --}}
@section('structured_data')
{
  "@@context": "https://schema.org",
  "@@type": "BlogPosting",
  "headline": "{{ isset($translation) ? $translation->title : $post->title }}",
  "image": "{{ $post->featured_image_url ?? asset('images/og-default.jpg') }}",
  "author": {
    "@@type": "Person",
    "name": "{{ $post->author_name ?? 'Jahongir Travel Team' }}"
  },
  "publisher": {
    "@@type": "Organization",
    "name": "Jahongir Travel",
    "logo": {
      "@@type": "ImageObject",
      "url": "{{ asset('images/logo.png') }}"
    }
  },
  "datePublished": "{{ $post->published_at ? $post->published_at->toIso8601String() : '' }}",
  "dateModified": "{{ $post->updated_at ? $post->updated_at->toIso8601String() : '' }}",
  "description": "{{ isset($translation) ? ($translation->seo_description ?? $translation->excerpt ?? Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160)) : ($post->meta_description ?? $post->excerpt ?? Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160)) }}",
  @if($post->tags->isNotEmpty())
  "keywords": "{{ $post->tags->pluck('name')->implode(', ') }}",
  @endif
  "articleSection": "{{ $post->category->name ?? 'Travel' }}"
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
    },
    {
      "@@type": "ListItem",
      "position": 3,
      "name": "{{ isset($translation) ? $translation->title : $post->title }}",
      "item": "{{ isset($canonicalUrl) ? $canonicalUrl : url('/blog/' . $post->slug) }}"
    }
  ]
}
</script>
@endpush

{{-- Page-specific CSS --}}
@push('styles')
<link rel="stylesheet" href="{{ asset('blog-article.css') }}">
@endpush

{{-- Main Content --}}
@section('content')

  <!-- =====================================================
       ARTICLE HERO SECTION
       ===================================================== -->
  <section class="article-hero">
    @if(isset($translation))
      {{-- SSR: Render hero directly for crawler visibility --}}
      <div class="container" data-blog-section="hero">
        @include('partials.blog.hero', ['post' => $post])
      </div>
    @else
      <div class="container"
           hx-get="{{ url('/partials/blog/' . $post->slug . '/hero') }}"
           hx-trigger="load once"
           hx-swap="innerHTML"
           data-blog-section="hero">
        <!-- Loading Skeleton -->
        <div class="skeleton-loader">
          <div class="skeleton skeleton--breadcrumb"></div>
          <div class="skeleton skeleton--meta"></div>
          <div class="skeleton skeleton--title"></div>
          <div class="skeleton skeleton--image"></div>
        </div>
      </div>
    @endif
  </section>

  <!-- =====================================================
       MAIN CONTENT AREA (Two-Column Layout)
       ===================================================== -->
  <div class="article-layout">
    <div class="container">
      <div class="article-layout-grid">

        @if(isset($translation))
          {{-- SSR: Render article content directly --}}
          @include('partials.blog.content', ['post' => $post])
        @else
          <!-- Main Article Content (Dynamic with HTMX) -->
          <main class="article-main"
                hx-get="{{ url('/partials/blog/' . $post->slug . '/content') }}"
                hx-trigger="load once"
                hx-swap="outerHTML"
                data-blog-section="content">
            <!-- Loading Skeleton -->
            <div class="skeleton-loader">
              <div class="skeleton skeleton--text-large"></div>
              <div class="skeleton skeleton--text"></div>
              <div class="skeleton skeleton--text"></div>
              <div class="skeleton skeleton--text"></div>
            </div>
          </main>
        @endif

        @if(isset($translation) && isset($recentPosts))
          {{-- SSR: Render sidebar directly --}}
          <aside class="article-sidebar" data-blog-section="sidebar">
            @include('partials.blog.sidebar', ['recentPosts' => $recentPosts])
          </aside>
        @else
          <!-- Sidebar (Dynamic with HTMX) -->
          <aside class="article-sidebar"
                 hx-get="{{ url('/partials/blog/' . $post->slug . '/sidebar') }}"
                 hx-trigger="load once"
                 hx-swap="innerHTML"
                 data-blog-section="sidebar">
            <!-- Loading Skeleton -->
            <div class="skeleton-loader">
              <div class="skeleton skeleton--widget"></div>
              <div class="skeleton skeleton--widget"></div>
              <div class="skeleton skeleton--widget"></div>
            </div>
          </aside>
        @endif

      </div>
    </div>
  </div>

  <!-- =====================================================
       RELATED ARTICLES SECTION
       ===================================================== -->
  @if(isset($translation) && isset($relatedPosts))
    {{-- SSR: Render related articles directly --}}
    @include('partials.blog.related', ['relatedPosts' => $relatedPosts])
  @else
    <section class="related-articles"
             hx-get="{{ url('/partials/blog/' . $post->slug . '/related') }}"
             hx-trigger="load once"
             hx-swap="outerHTML"
             data-blog-section="related">
      <!-- Loading Skeleton -->
      <div class="container">
        <h2 class="section-title">Related Articles</h2>
        <div class="related-articles-grid">
          <div class="skeleton skeleton--card"></div>
          <div class="skeleton skeleton--card"></div>
          <div class="skeleton skeleton--card"></div>
        </div>
      </div>
    </section>
  @endif

  <!-- =====================================================
       RELATED TOURS SECTION
       ===================================================== -->
  @if(isset($translation) && isset($relatedTours))
    {{-- SSR: Render related tours directly --}}
    @include('partials.blog.related-tours', ['tours' => $relatedTours])
  @else
    <section class="related-tours-section"
             hx-get="{{ url('/partials/blog/' . $post->slug . '/related-tours') }}"
             hx-trigger="load once"
             hx-swap="outerHTML"
             data-blog-section="related-tours">
      <!-- Loading Skeleton -->
      <div class="container">
        <h2 class="section-title">Experience These Tours</h2>
        <div class="tour-grid">
          <div class="skeleton skeleton--card"></div>
          <div class="skeleton skeleton--card"></div>
          <div class="skeleton skeleton--card"></div>
        </div>
      </div>
    </section>
  @endif

  <!-- =====================================================
       COMMENTS SECTION (Always HTMX - interactive, low SEO value)
       ===================================================== -->
  <div class="article-comments"
       hx-get="{{ url('/partials/blog/' . $post->slug . '/comments') }}"
       hx-trigger="load once"
       hx-swap="outerHTML"
       data-blog-section="comments">
    <!-- Loading Skeleton -->
    <div class="container">
      <div class="skeleton-loader">
        <div class="skeleton skeleton--text"></div>
        <div class="skeleton skeleton--widget"></div>
        <div class="skeleton skeleton--text"></div>
      </div>
    </div>
  </div>
@endsection

{{-- Page-specific Scripts --}}
@push('scripts')
@if(!isset($translation))
<!-- HTMX Library (only needed for non-SSR routes) -->
<script src="{{ asset('js/htmx.min.js') }}?v={{ filemtime(public_path('js/htmx.min.js')) }}"></script>
@else
<!-- HTMX Library (needed for comments section) -->
<script src="{{ asset('js/htmx.min.js') }}?v={{ filemtime(public_path('js/htmx.min.js')) }}"></script>
@endif

<!-- HTMX Event Handlers -->
<script>
  (function() {
    'use strict';

    // Guard to prevent multiple event listener registration
    if (window.HTMX_EVENTS_REGISTERED) return;
    window.HTMX_EVENTS_REGISTERED = true;

    document.body.addEventListener('htmx:afterSwap', function(evt) {
      console.log('[HTMX] Loaded:', evt.detail.pathInfo.requestPath);
    });

    document.body.addEventListener('htmx:responseError', function(evt) {
      console.error('[HTMX] Error:', evt.detail.pathInfo.requestPath);
      evt.detail.target.innerHTML = '<div style="padding:20px;background:#fee;color:#c33;">Failed to load content. Please refresh the page.</div>';
    });
  })();
</script>

<!-- Blog-specific JavaScript -->
<script src="{{ asset('blog-article.js') }}?v={{ filemtime(public_path('blog-article.js')) }}" defer></script>
<script src="{{ asset('js/blog-comments.js') }}?v={{ filemtime(public_path('js/blog-comments.js')) }}" defer></script>
@endpush
