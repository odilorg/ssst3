{{-- Blog Related Posts Partial --}}
@if($relatedPosts->isNotEmpty())
    <section class="related-articles">
        <div class="container">
            <h2 class="section-title">Related Articles</h2>

            <div class="related-articles-grid">
                @foreach($relatedPosts as $related)
                    <article class="related-article-card">
                        @if($related->featured_image)
                            <a href="/blog/{{ $related->slug }}" class="card-image-link">
                                @if($related->has_webp && $related->featured_image_webp_srcset)
                                    {{-- Serve WebP with responsive sizes --}}
                                    <picture>
                                        <source
                                            type="image/webp"
                                            srcset="{{ $related->featured_image_webp_srcset }}"
                                            sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 400px">
                                        <img
                                            src="{{ $related->featured_image_url }}"
                                            alt="{{ $related->title }}"
                                            loading="lazy">
                                    </picture>
                                @else
                                    {{-- Fallback to original image --}}
                                    <img
                                        src="{{ $related->featured_image_url }}"
                                        alt="{{ $related->title }}"
                                        loading="lazy">
                                @endif
                            </a>
                        @endif

                        <div class="card-content">
                            <h3 class="card-title">
                                <a href="/blog/{{ $related->slug }}">{{ $related->title }}</a>
                            </h3>

                            @if($related->excerpt)
                                <p class="card-excerpt">
                                    {{ Str::limit($related->excerpt, 150) }}
                                </p>
                            @endif

                            <a href="/blog/{{ $related->slug }}" class="card-read-more">
                                Read More →
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
