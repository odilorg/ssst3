@extends('layouts.main')

{{-- SEO Meta Tags --}}
@section('title', ($post->meta_title ?? $post->title) . ' | Jahongir Travel Blog')
@section('meta_description', $post->meta_description ?? $post->excerpt ?? Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160))
@section('meta_keywords', 'Uzbekistan travel blog, travel tips, ' . ($post->category->name ?? ''))
@section('canonical', url('/blog/' . $post->slug))

{{-- Open Graph Tags --}}
@section('og_type', 'article')
@section('og_title', $post->meta_title ?? $post->title)
@section('og_description', $post->meta_description ?? $post->excerpt ?? Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160))
@section('og_image', $post->featured_image_url ?? asset('images/og-default.jpg'))
@section('og_url', url('/blog/' . $post->slug))

{{-- Twitter Card Tags --}}
@section('twitter_title', $post->meta_title ?? $post->title)
@section('twitter_description', $post->meta_description ?? $post->excerpt ?? Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160))
@section('twitter_image', $post->featured_image_url ?? asset('images/og-default.jpg'))

{{-- Schema.org Structured Data --}}
@section('structured_data')
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "{{ $post->title }}",
  "image": "{{ $post->featured_image_url ?? asset('images/og-default.jpg') }}",
  "author": {
    "@type": "Person",
    "name": "{{ $post->author_name ?? 'Jahongir Travel Team' }}"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Jahongir Travel",
    "logo": {
      "@type": "ImageObject",
      "url": "{{ asset('images/logo.png') }}"
    }
  },
  "datePublished": "{{ $post->published_at ? $post->published_at->toIso8601String() : '' }}",
  "dateModified": "{{ $post->updated_at ? $post->updated_at->toIso8601String() : '' }}",
  "description": "{{ $post->meta_description ?? $post->excerpt ?? Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160) }}"
}
@endsection

{{-- Page-specific CSS --}}
@push('styles')
<link rel="stylesheet" href="{{ asset('blog-article.css') }}">
@endpush

{{-- Main Content --}}
@section('content')

  <!-- =====================================================
       ARTICLE HERO SECTION (Dynamic with HTMX)
       ===================================================== -->
  <section class="article-hero">
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
  </section>

  <!-- =====================================================
       MAIN CONTENT AREA (Two-Column Layout)
       ===================================================== -->
  <div class="article-layout">
    <div class="container">
      <div class="article-layout-grid">

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

      </div>
    </div>
  </div>

  <!-- =====================================================
       RELATED ARTICLES SECTION (Dynamic with HTMX)
       ===================================================== -->
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

  <!-- =====================================================
       COMMENTS SECTION (Dynamic with HTMX)
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
<!-- HTMX Library -->
<script src="{{ asset('js/htmx.min.js') }}"></script>

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
<script src="{{ asset('blog-article.js') }}" defer></script>
<script src="{{ asset('js/blog-comments.js') }}" defer></script>
@endpush
