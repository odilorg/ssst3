{{--
    OPTION 2: Compact Vertical Card
    Reduced height, better aspect ratio, still familiar vertical pattern
    Aspect ratio: ~1:1.5 (300px × 450px)
--}}

@php $tr = $tour->translationOrDefault(); @endphp
<article class="tour-card-v" data-tour-id="{{ $tour->id }}">

    {{-- Image with floating badges --}}
    <div class="tour-card-v__media">
        <img
            src="{{ $tour->featured_image_url ?? asset('images/default-tour.webp') }}"
            alt="{{ $tr->title ?? $tour->title }}"
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
            <a href="/{{ app()->getLocale() }}/tours/{{ $tr->slug ?? $tour->slug }}">
                {{ $tr->title ?? $tour->title }}
            </a>
        </h3>

        {{-- Description --}}
        <p class="tour-card-v__description">
            {{ $tr->excerpt ?? $tour->short_description ?? Str::limit($tour->meta_description, 150) }}
        </p>

        {{-- Footer (Price + CTA) --}}
        <div class="tour-card-v__footer">
            <div class="tour-card-v__price">
                <span class="tour-card-v__price-amount">${{ number_format($tour->price_per_person, 0) }}</span>
            </div>
            <a href="/{{ app()->getLocale() }}/tours/{{ $tr->slug ?? $tour->slug }}" class="tour-card-v__btn">
                View Details
                <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</article>
