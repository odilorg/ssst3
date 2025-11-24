{{-- Related Tours Section --}}
@if($relatedTours && $relatedTours->isNotEmpty())
<section class="related-tours-section">
    <div class="related-tours-header">
        <h2 class="related-tours-title">You Might Also Like</h2>
        <p class="related-tours-subtitle">Discover more amazing experiences</p>
    </div>

    <div class="related-tours-grid">
        @foreach($relatedTours as $relatedTour)
            <a href="{{ route('tours.show', $relatedTour->slug) }}" class="related-tour-card">
                {{-- Tour Image --}}
                <div class="related-tour-image-wrapper">
                    @if($relatedTour->hero_image)
                        <img
                            src="{{ asset('storage/' . $relatedTour->hero_image) }}"
                            alt="{{ $relatedTour->title }}"
                            class="related-tour-image"
                            loading="lazy">
                    @else
                        <div class="related-tour-image-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                    @endif

                    {{-- Duration Badge --}}
                    <div class="related-tour-duration-badge">
                        <i class="far fa-clock" aria-hidden="true"></i>
                        {{ $relatedTour->duration_days }} {{ $relatedTour->duration_days > 1 ? 'days' : 'day' }}
                    </div>
                </div>

                {{-- Tour Content --}}
                <div class="related-tour-content">
                    {{-- Category Badges --}}
                    @if($relatedTour->categories && $relatedTour->categories->isNotEmpty())
                        <div class="related-tour-badges">
                            @foreach($relatedTour->categories->take(2) as $category)
                                <span class="related-tour-badge">{{ ucwords(str_replace('-', ' ', $category->slug)) }}</span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Title --}}
                    <h3 class="related-tour-title">{{ Str::limit($relatedTour->title, 60) }}</h3>

                    {{-- Meta Info --}}
                    <div class="related-tour-meta">
                        @if($relatedTour->city)
                            <span class="related-tour-city">
                                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                {{ $relatedTour->city->name }}
                            </span>
                        @endif
                    </div>

                    {{-- Price --}}
                    <div class="related-tour-footer">
                        @if($relatedTour->price_per_person)
                            <div class="related-tour-price">
                                <span class="price-label">From</span>
                                <span class="price-amount">${{ number_format($relatedTour->price_per_person, 0) }}</span>
                                <span class="price-per">/ person</span>
                            </div>
                        @endif

                        <span class="related-tour-cta">
                            View Details
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif
