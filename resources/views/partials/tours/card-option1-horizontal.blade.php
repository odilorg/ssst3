{{--
    OPTION 1: Horizontal Card Layout
    Best for desktop, better scannability, more info visible
    Aspect ratio: ~1:0.6 landscape (650px × 280px)
--}}

<article class="tour-card-h" data-tour-id="{{ $tour->id }}">
    
    {{-- Image (40% width on desktop) --}}
    <div class="tour-card-h__media">
        <img
            src="{{ $tour->featured_image_url ?? asset('images/default-tour.webp') }}"
            alt="{{ $tour->title }}"
            width="400"
            height="300"
            loading="lazy"
            decoding="async"
        >
        {{-- Duration badge --}}
        <div class="tour-card-h__badge">
            <i class="far fa-clock" aria-hidden="true"></i>
            <span>{{ $tour->duration_text ?? $tour->duration_days . ' days' }}</span>
        </div>
    </div>

    {{-- Content (60% width on desktop) --}}
    <div class="tour-card-h__content">
        <div class="tour-card-h__main">
            {{-- Title --}}
            <h3 class="tour-card-h__title">
                <a href="/tours/{{ $tour->slug }}">
                    {{ $tour->title }}
                </a>
            </h3>

            {{-- Description --}}
            <p class="tour-card-h__description">
                {{ Str::limit($tour->meta_description ?? $tour->description, 120) }}
            </p>
        </div>

        {{-- Footer --}}
        <div class="tour-card-h__footer">
            {{-- Location --}}
            <div class="tour-card-h__location">
                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                <span>{{ $tour->city->name ?? 'Uzbekistan' }}</span>
            </div>

            {{-- Price + CTA --}}
            <div class="tour-card-h__actions">
                <div class="tour-card-h__price">
                    <span class="tour-card-h__price-amount">${{ number_format($tour->price_per_person, 0) }}</span>
                </div>
                <a href="/tours/{{ $tour->slug }}" class="tour-card-h__btn">
                    View Tour
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</article>
