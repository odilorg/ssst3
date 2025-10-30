{{-- Blog Related Posts Partial --}}
@if($relatedPosts->isNotEmpty())
    <div class="related-posts">
        <h2 class="related-posts__title">Related Articles</h2>
        <p class="related-posts__subtitle">Continue exploring Uzbekistan and travel insights</p>

        <div class="related-posts__grid">
            @foreach($relatedPosts as $related)
                <article class="related-post-card">
                    <!-- Featured Image -->
                    @if($related->featured_image)
                        <a href="/blog/{{ $related->slug }}" class="related-post-card__image">
                            <img src="{{ $related->featured_image }}" alt="{{ $related->title }}" loading="lazy">
                            @if($related->is_featured)
                                <span class="related-post-card__badge">Featured</span>
                            @endif
                        </a>
                    @endif

                    <div class="related-post-card__content">
                        <!-- Category -->
                        @if($related->category)
                            <a href="/blog?category={{ $related->category->slug }}" class="related-post-card__category">
                                {{ $related->category->name }}
                            </a>
                        @endif

                        <!-- Title -->
                        <h3 class="related-post-card__title">
                            <a href="/blog/{{ $related->slug }}">{{ $related->title }}</a>
                        </h3>

                        <!-- Excerpt -->
                        @if($related->excerpt)
                            <p class="related-post-card__excerpt">{{ Str::limit($related->excerpt, 120) }}</p>
                        @endif

                        <!-- Meta -->
                        <div class="related-post-card__meta">
                            <div class="related-post-card__author">
                                @if($related->author_image)
                                    <img src="{{ $related->author_image }}" alt="{{ $related->author_name }}" class="author-avatar">
                                @else
                                    <div class="author-avatar author-avatar--placeholder">
                                        {{ substr($related->author_name, 0, 1) }}
                                    </div>
                                @endif
                                <span class="author-name">{{ $related->author_name }}</span>
                            </div>
                            <div class="related-post-card__info">
                                <time datetime="{{ $related->published_at->format('Y-m-d') }}">
                                    {{ $related->published_at->format('M d, Y') }}
                                </time>
                                <span class="info-separator">•</span>
                                <span class="reading-time">{{ $related->reading_time }} min read</span>
                            </div>
                        </div>

                        <!-- Tags (show up to 3) -->
                        @if($related->tags->isNotEmpty())
                            <div class="related-post-card__tags">
                                @foreach($related->tags->take(3) as $tag)
                                    <a href="/blog?tag={{ $tag->slug }}" class="post-tag">{{ $tag->name }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <!-- View All Posts Link -->
        <div class="related-posts__footer">
            <a href="/blog" class="btn btn--outline btn--large">
                View All Blog Posts
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M7 3l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
@endif
