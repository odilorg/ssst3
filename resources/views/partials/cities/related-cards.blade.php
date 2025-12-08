<div class="related-categories-grid">
@forelse($cities as $city)
    <a href="/destinations/{{ $city->slug }}" class="related-category-card">
        <div class="related-category-card__icon">
            <i class="fas fa-map-marker-alt"></i>
        </div>

        <h3 class="related-category-card__title">{{ $city->translated_name }}</h3>

        @if($city->tagline)
        <p class="related-category-card__tagline" style="font-size: 12px; color: #666; margin-top: 4px;">
            {{ $city->tagline }}
        </p>
        @endif

        <p class="related-category-card__count">
            {{ $city->tour_count_cache ?? 0 }} {{ ($city->tour_count_cache ?? 0) === 1 ? 'tour' : 'tours' }}
        </p>

        <div class="related-category-card__arrow">
            <i class="fas fa-arrow-right"></i>
        </div>
    </a>
@empty
    <div class="related-categories-empty">
        <p>No other destinations available at the moment.</p>
    </div>
@endforelse
</div>
