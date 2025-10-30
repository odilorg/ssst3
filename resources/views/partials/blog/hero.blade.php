{{-- Blog Post Hero Partial - Breadcrumbs, Title, Meta, Featured Image --}}
<div class="container">
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span class="breadcrumb-separator" aria-hidden="true">›</span>
        <a href="/blog/">Blog</a>
        <span class="breadcrumb-separator" aria-hidden="true">›</span>
        @if($post->category)
            <a href="/blog?category={{ $post->category->slug }}">{{ $post->category->name }}</a>
            <span class="breadcrumb-separator" aria-hidden="true">›</span>
        @endif
        <span class="breadcrumb-current" aria-current="page">{{ $post->title }}</span>
    </nav>

    <!-- Article Meta Information -->
    <div class="article-meta">
        @if($post->category)
            <span class="article-category">{{ $post->category->name }}</span>
        @endif
        <span class="article-author">By {{ $post->author_name }}</span>
        <time class="article-date" datetime="{{ $post->published_at->format('Y-m-d') }}">{{ $post->published_at->format('M d, Y') }}</time>
        <span class="article-reading-time">{{ $post->reading_time }} min read</span>
    </div>

    <!-- Article Title -->
    <h1 class="article-title">{{ $post->title }}</h1>

    <!-- Featured Image -->
    @if($post->featured_image)
        <div class="article-featured-image">
            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" loading="eager">
        </div>
    @endif
</div>
