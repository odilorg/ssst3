<div class="related-categories-grid">
@forelse($categories as $category)
    <a href="/tours/category/{{ $category->slug }}" class="related-category-card">
        @if($category->icon)
        <div class="related-category-card__icon">
            @if(str_starts_with($category->icon, 'fa'))
            <i class="{{ $category->icon }}"></i>
            @else
            <span>{{ $category->icon }}</span>
            @endif
        </div>
        @endif

        <h3 class="related-category-card__title">{{ $category->translated_name }}</h3>

        <p class="related-category-card__count">
            {{ $category->cached_tour_count }} {{ $category->cached_tour_count === 1 ? 'tour' : 'tours' }}
        </p>

        <div class="related-category-card__arrow">
            <i class="fas fa-arrow-right"></i>
        </div>
    </a>
@empty
    <div class="related-categories-empty">
        <p>No other categories available at the moment.</p>
    </div>
@endforelse
</div>
