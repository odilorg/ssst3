{{-- Related Tours Partial --}}
@if($tours->isNotEmpty())
<section class="related-tours">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Explore Related Tours</h2>
            <p class="section-subtitle">Experience what you just read about</p>
        </div>

        <div class="tour-grid">
            @foreach($tours as $tour)
            <article class="tour-card">
                {{-- Tour Image --}}
                <a href="{{ route('tours.show', $tour->slug) }}" class="tour-card__image-link">
                    @if($tour->featured_image_url)
                        <img
                            src="{{ $tour->featured_image_url }}"
                            alt="{{ $tour->title }}"
                            class="tour-card__image"
                            loading="lazy"
                        >
                    @else
                        <div class="tour-card__image-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                    @endif

                    {{-- Duration Badge --}}
                    @if($tour->duration_text || $tour->duration_days)
                        <span class="tour-card__badge">
                            {{ $tour->duration_text ?? $tour->duration_days . ' days' }}
                        </span>
                    @endif
                </a>

                {{-- Tour Content --}}
                <div class="tour-card__content">
                    <h3 class="tour-card__title">
                        <a href="{{ route('tours.show', $tour->slug) }}">
                            {{ $tour->title }}
                        </a>
                    </h3>

                    @if($tour->short_description)
                        <p class="tour-card__description">
                            {{ Str::limit($tour->short_description, 100) }}
                        </p>
                    @endif

                    {{-- Tour Meta --}}
                    <div class="tour-card__meta">
                        {{-- Rating --}}
                        @if($tour->rating > 0)
                            <div class="tour-card__rating">
                                <i class="fas fa-star"></i>
                                <span>{{ number_format($tour->rating, 1) }}</span>
                                @if($tour->review_count > 0)
                                    <span class="tour-card__reviews">({{ $tour->review_count }})</span>
                                @endif
                            </div>
                        @endif

                        {{-- Price --}}
                        @if($tour->price_per_person)
                            <div class="tour-card__price">
                                <span class="price-label">From</span>
                                <span class="price-amount">${{ number_format($tour->price_per_person) }}</span>
                                <span class="price-unit">/person</span>
                            </div>
                        @endif
                    </div>

                    {{-- CTA Button --}}
                    <a href="{{ route('tours.show', $tour->slug) }}" class="tour-card__cta btn btn--primary">
                        View Tour Details
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
            @endforeach
        </div>

        {{-- View All Tours Link --}}
        <div class="related-tours__footer">
            <a href="{{ url('/tours') }}" class="btn btn--ghost">
                Browse All Tours
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
@endif
