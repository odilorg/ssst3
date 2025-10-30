{{-- Blog Post Hero Partial - Breadcrumbs, Title, Author, Date, Featured Image --}}
<div class="container">

    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <ol class="breadcrumbs__list">
            <li class="breadcrumbs__item">
                <a href="/" class="breadcrumbs__link">Home</a>
                <span class="breadcrumbs__separator" aria-hidden="true">/</span>
            </li>
            <li class="breadcrumbs__item">
                <a href="/blog" class="breadcrumbs__link">Blog</a>
                <span class="breadcrumbs__separator" aria-hidden="true">/</span>
            </li>
            @if($post->category)
                <li class="breadcrumbs__item">
                    <a href="/blog?category={{ $post->category->slug }}" class="breadcrumbs__link">{{ $post->category->name }}</a>
                    <span class="breadcrumbs__separator" aria-hidden="true">/</span>
                </li>
            @endif
            <li class="breadcrumbs__item">
                <span class="breadcrumbs__current" aria-current="page">{{ Str::limit($post->title, 50) }}</span>
            </li>
        </ol>
    </nav>

    <!-- Post Title with Actions -->
    <div class="blog-header__title-row">
        <h1 class="blog-title">{{ $post->title }}</h1>
        <div class="blog-header__actions">
            <button class="btn-icon" aria-label="Share this post" title="Share">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path d="M15 7a2 2 0 100-4 2 2 0 000 4zM5 13a2 2 0 100-4 2 2 0 000 4zM15 17a2 2 0 100-4 2 2 0 000 4zM6.5 11.5l7-3M6.5 11.5l7 3"/>
                </svg>
            </button>
            <button class="btn-icon" aria-label="Add to favorites" title="Save to reading list">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path d="M5 0a2 2 0 00-2 2v16l7-4 7 4V2a2 2 0 00-2-2H5z"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Post Meta Information -->
    <div class="blog-header__meta">
        <div class="blog-author">
            @if($post->author_image)
                <img src="{{ $post->author_image }}" alt="{{ $post->author_name }}" class="blog-author__avatar">
            @else
                <div class="blog-author__avatar blog-author__avatar--placeholder">
                    {{ substr($post->author_name, 0, 1) }}
                </div>
            @endif
            <span class="blog-author__name">{{ $post->author_name }}</span>
        </div>
        <span class="blog-meta__separator">•</span>
        <time class="blog-date" datetime="{{ $post->published_at->format('Y-m-d') }}">
            {{ $post->published_at->format('M d, Y') }}
        </time>
        <span class="blog-meta__separator">•</span>
        <span class="blog-reading-time">
            <svg width="16" height="16" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <circle cx="9" cy="9" r="8"/>
                <path d="M9 4.5v4.5l3 2"/>
            </svg>
            {{ $post->reading_time }} min read
        </span>
        <span class="blog-meta__separator">•</span>
        <span class="blog-view-count">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path d="M10 3C5 3 1.73 7.11 1 10c.73 2.89 4 7 9 7s8.27-4.11 9-7c-.73-2.89-4-7-9-7z"/>
                <circle cx="10" cy="10" r="3"/>
            </svg>
            {{ number_format($post->view_count) }} {{ Str::plural('view', $post->view_count) }}
        </span>
    </div>

    <!-- Featured Image -->
    @if($post->featured_image)
        <div class="blog-featured-image">
            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" loading="eager">
        </div>
    @endif

    <!-- Tags -->
    @if($post->tags->isNotEmpty())
        <div class="blog-tags">
            @foreach($post->tags as $tag)
                <a href="/blog?tag={{ $tag->slug }}" class="blog-tag">
                    <svg width="14" height="14" viewBox="0 0 18 18" fill="currentColor" aria-hidden="true">
                        <path d="M2 0a2 2 0 00-2 2v5.586a2 2 0 00.586 1.414l8 8a2 2 0 002.828 0l5.586-5.586a2 2 0 000-2.828l-8-8A2 2 0 008.414 0H2zm2.5 5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                    </svg>
                    {{ $tag->name }}
                </a>
            @endforeach
        </div>
    @endif

</div>
