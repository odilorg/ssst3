{{-- Related Tours Partial - Updated to Option 2 Design --}}
@if($tours->isNotEmpty())
<section class="related-tours">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Explore Related Tours</h2>
            <p class="section-subtitle">Experience what you just read about</p>
        </div>

        <div class="tour-grid">
            @foreach($tours as $tour)
            {{-- OPTION 2: Compact Vertical Card --}}
            <article class="tour-card-v" data-tour-id="{{ $tour->id }}">
                
                {{-- Image with floating badges --}}
                <div class="tour-card-v__media">
                    <a href="{{ route('tours.show', $tour->slug) }}">
                        @if($tour->hero_image)
                            <img
                                src="{{ asset('storage/' . $tour->hero_image) }}"
                                alt="{{ $tour->title }}"
                                width="400"
                                height="300"
                                loading="lazy"
                                decoding="async"
                            >
                        @elseif($tour->featured_image_url)
                            <img
                                src="{{ $tour->featured_image_url }}"
                                alt="{{ $tour->title }}"
                                width="400"
                                height="300"
                                loading="lazy"
                                decoding="async"
                            >
                        @else
                            <div class="tour-card__image-placeholder">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                    </a>
                    
                    {{-- Floating badges --}}
                    <div class="tour-card-v__badges">
                        @if($tour->duration_text || $tour->duration_days)
                            <span class="tour-card-v__badge">
                                <i class="far fa-clock" aria-hidden="true"></i>
                                {{ $tour->duration_text ?? $tour->duration_days . ' days' }}
                            </span>
                        @endif
                        @if($tour->city)
                            <span class="tour-card-v__badge">
                                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                {{ $tour->city->name }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Content --}}
                <div class="tour-card-v__content">
                    {{-- Title --}}
                    <h3 class="tour-card-v__title">
                        <a href="{{ route('tours.show', $tour->slug) }}">
                            {{ $tour->title }}
                        </a>
                    </h3>

                    {{-- Description --}}
                    <p class="tour-card-v__description">
                        {{ $tour->short_description ? Str::limit($tour->short_description, 100) : Str::limit(strip_tags($tour->long_description ?? ''), 100) }}
                    </p>

                    {{-- Footer (Price + CTA) --}}
                    <div class="tour-card-v__footer">
                        <div class="tour-card-v__price">
                            @if($tour->price_per_person)
                                <span class="tour-card-v__price-amount">${{ number_format($tour->price_per_person, 0) }}</span>
                            @else
                                <span class="tour-card-v__price-amount">Contact us</span>
                            @endif
                        </div>
                        <a href="{{ route('tours.show', $tour->slug) }}" class="tour-card-v__btn">
                            View Details
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
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
