<article class="blog-card" data-post-id="{{ $post->id }}">
    <a href="{{ route('blog.show', $post->slug) }}" class="blog-card__link">

        <!-- Card Media -->
        <div class="blog-card__media">
            @if($post->featured_image_url)
                @if($post->has_webp && $post->featured_image_webp_srcset)
                    {{-- Serve WebP with responsive sizes --}}
                    <picture>
                        <source
                            type="image/webp"
                            srcset="{{ $post->featured_image_webp_srcset }}"
                            sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 400px">
                        <img
                            src="{{ $post->featured_image_url }}"
                            alt="{{ $post->title }}"
                            width="800"
                            height="450"
                            loading="lazy"
                            fetchpriority="low"
                            decoding="async">
                    </picture>
                @else
                    {{-- Fallback to original image --}}
                    <img
                        src="{{ $post->featured_image_url }}"
                        alt="{{ $post->title }}"
                        width="800"
                        height="450"
                        loading="lazy"
                        fetchpriority="low"
                        decoding="async">
                @endif
            @else
                <img
                    src="{{ asset('images/blog-default.svg') }}"
                    alt="{{ $post->title }}"
                    width="800"
                    height="450"
                    loading="lazy"
                    fetchpriority="low"
                    decoding="async">
            @endif

            @if($post->category)
                <span class="blog-card__category" data-category="{{ $post->category->slug }}">
                    {{ $post->category->name }}
                </span>
            @endif
        </div>

        <!-- Card Content -->
        <div class="blog-card__content">
            <h3 class="blog-card__title">{{ $post->title }}</h3>
            <p class="blog-card__excerpt">
                {{ Str::limit($post->excerpt, 150) }}
            </p>

            <!-- Card Meta -->
            <div class="blog-card__meta">
                <time class="blog-card__date" datetime="{{ $post->published_at->format('Y-m-d') }}">
                    {{ $post->published_at->format('M d, Y') }}
                </time>
                <span class="blog-card__reading-time" aria-label="Reading time">
                    <i class="far fa-clock" aria-hidden="true"></i> {{ $post->reading_time }} min read
                </span>
            </div>
        </div>

    </a>
</article>
