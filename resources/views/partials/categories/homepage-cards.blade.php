@foreach($categories as $category)
<a href="/{{ app()->getLocale() }}/tours/category/{{ $category->slug }}" class="activity-card">
    <div class="activity-card__image" @if($category->image_path) style="background-image: url('{{ asset('storage/' . $category->image_path) }}');" @endif>
        <div class="activity-card__overlay"></div>
        <div class="activity-card__content">
            @if($category->icon)
            <div class="activity-card__icon">
                @if(str_starts_with($category->icon, 'fa'))
                <i class="{{ $category->icon }}"></i>
                @else
                <span>{{ $category->icon }}</span>
                @endif
            </div>
            @endif
            <h3 class="activity-card__title">{{ $category->translated_name }}</h3>
            @if($category->translated_description)
            <p class="activity-card__description">{{ $category->translated_description }}</p>
            @endif
            <div class="activity-card__count">
                {{ $category->cached_tour_count }} {{ $category->cached_tour_count === 1 ? 'tour' : 'tours' }}
            </div>
        </div>
    </div>
</a>
@endforeach
