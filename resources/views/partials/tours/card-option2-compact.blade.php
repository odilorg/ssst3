{{--
    OPTION 2: Compact Vertical Card
    Reduced height, better aspect ratio, still familiar vertical pattern
    Aspect ratio: ~1:1.5 (300px × 450px)
--}}

<article class="tour-card-v" data-tour-id="{{ $tour->id }}">
    
    {{-- Image with floating badges --}}
    <div class="tour-card-v__media">
        <img
            src="{{ $tour->featured_image_url ?? asset('images/default-tour.webp') }}"
            alt="{{ $tour->title }}"
            width="400"
            height="300"
            loading="lazy"
            decoding="async"
        >
        
        {{-- Floating badges --}}
        <div class="tour-card-v__badges">
            <span class="tour-card-v__badge">
                <i class="far fa-clock" aria-hidden="true"></i>
                {{ $tour->duration_text ?? $tour->duration_days . ' days' }}
            </span>
            <span class="tour-card-v__badge">
                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                {{ $tour->city->name ?? 'Uzbekistan' }}
            </span>
        </div>
    </div>

    {{-- Content --}}
    <div class="tour-card-v__content">
        {{-- Title --}}
        <h3 class="tour-card-v__title">
            <a href="/tours/{{ $tour->slug }}">
                {{ $tour->title }}
            </a>
        </h3>

        {{-- Description --}}
        <p class="tour-card-v__description">
            {{ Str::limit($tour->meta_description ?? $tour->description, 100) }}
        </p>

        {{-- Footer (Price + CTA) --}}
        <div class="tour-card-v__footer">
            <div class="tour-card-v__price">
                <span class="tour-card-v__price-amount">${{ number_format($tour->price_per_person, 0) }}</span>
            </div>
            <a href="/tours/{{ $tour->slug }}" class="tour-card-v__btn">
                View Details
                <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</article>
