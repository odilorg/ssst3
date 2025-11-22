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
        <span class="article-author"><i class="fas fa-user"></i> By {{ $post->author_name }}</span>
        @if($post->published_at)
            <time class="article-date" datetime="{{ $post->published_at->format('Y-m-d') }}"><i class="fas fa-calendar-alt"></i> {{ $post->published_at->format('M d, Y') }}</time>
        @endif
        <span class="article-reading-time"><i class="fas fa-clock"></i> {{ $post->reading_time }} min read</span>
    </div>

    <!-- Article Title -->
    <h1 class="article-title">{{ $post->title }}</h1>

    <!-- Tags -->
    @if($post->tags->isNotEmpty())
        <div class="article-tags">
            @foreach($post->tags as $tag)
                <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="tag-badge">
                    <i class="fas fa-tag"></i> {{ $tag->name }}
                </a>
            @endforeach
        </div>
    @endif

    <!-- Featured Image -->
    @if($post->featured_image)
        <div class="article-featured-image">
            @if($post->has_webp && $post->featured_image_webp_srcset)
                {{-- Serve WebP with responsive sizes --}}
                <picture>
                    <source
                        type="image/webp"
                        srcset="{{ $post->featured_image_webp_srcset }}"
                        sizes="(max-width: 768px) 100vw, (max-width: 1200px) 90vw, 1200px">
                    <img
                        src="{{ asset('storage/' . $post->featured_image) }}"
                        alt="{{ $post->title }}"
                        loading="eager"
                        fetchpriority="high">
                </picture>
            @else
                {{-- Fallback to original image --}}
                <img
                    src="{{ asset('storage/' . $post->featured_image) }}"
                    alt="{{ $post->title }}"
                    loading="eager"
                    fetchpriority="high">
            @endif
        </div>
    @endif
</div>
