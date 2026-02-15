{{--
    OPTION 3: Grid Card with Info Overlay
    Modern, space-efficient, image-heavy
    Aspect ratio: ~1:1.3 (300px × 400px)
--}}

@php $tr = $tour->translationOrDefault(); @endphp
<article class="tour-card-o" data-tour-id="{{ $tour->id }}">

    {{-- Background Image --}}
    <img
        src="{{ $tour->featured_image_url ?? asset('images/default-tour.webp') }}"
        alt="{{ $tr->title ?? $tour->title }}"
        class="tour-card-o__bg"
        width="400"
        height="500"
        loading="lazy"
        decoding="async"
    >
    
    {{-- Gradient Overlay --}}
    <div class="tour-card-o__overlay"></div>

    {{-- Content Overlaid --}}
    <div class="tour-card-o__content">
        
        {{-- Top Badges --}}
        <div class="tour-card-o__top">
            <span class="tour-card-o__badge">
                <i class="far fa-clock" aria-hidden="true"></i>
                {{ $tour->duration_text ?? $tour->duration_days . ' days' }}
            </span>
            <span class="tour-card-o__badge">
                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                {{ $tour->city->name ?? 'Uzbekistan' }}
            </span>
        </div>

        {{-- Bottom Content --}}
        <div class="tour-card-o__bottom">
            <h3 class="tour-card-o__title">
                <a href="/{{ app()->getLocale() }}/tours/{{ $tr->slug ?? $tour->slug }}">
                    {{ $tr->title ?? $tour->title }}
                </a>
            </h3>

            <p class="tour-card-o__description">
                {{ Str::limit($tr->excerpt ?? $tour->short_description ?? $tour->meta_description, 90) }}
            </p>

            <div class="tour-card-o__footer">
                <div class="tour-card-o__price">
                    <span class="tour-card-o__price-amount">${{ number_format($tour->price_per_person, 0) }}</span>
                </div>
                <a href="/{{ app()->getLocale() }}/tours/{{ $tr->slug ?? $tour->slug }}" class="tour-card-o__btn">
                    View Tour
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</article>
