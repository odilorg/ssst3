{{-- Tour Reviews Partial --}}
@if($tour->reviews && $tour->reviews->isNotEmpty())
    <div class="reviews-header">
        <h2 class="section-title">Customer Reviews</h2>
        <div class="reviews-summary">
            <div class="reviews-rating">
                <span class="rating-score">{{ number_format($tour->rating, 1) }}</span>
                <div class="rating-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="icon icon--star" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M8 0l2.163 5.331 5.837.423-4.437 3.798 1.363 5.648L8 12.331 3.074 15.2l1.363-5.648L.563 5.754l5.837-.423L8 0z"/>
                        </svg>
                    @endfor
                </div>
                <span class="rating-count">Based on {{ $tour->review_count }} {{ Str::plural('review', $tour->review_count) }}</span>
            </div>
        </div>
    </div>

    <div class="reviews-list">
        @foreach($tour->reviews as $review)
            <article class="review-card">
                <div class="review-header">
                    <img src="{{ $review->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($review->reviewer_name) . '&size=56&background=1a5490&color=fff' }}"
                         alt=""
                         class="reviewer-avatar"
                         width="56"
                         height="56"
                         loading="lazy">
                    <div class="reviewer-info">
                        <h3 class="reviewer-name">{{ $review->reviewer_name }}</h3>
                        <div class="review-rating">
                            @for($i = 1; $i <= $review->rating; $i++)
                                <svg class="icon icon--star" width="18" height="18" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                    <path d="M8 0l2.163 5.331 5.837.423-4.437 3.798 1.363 5.648L8 12.331 3.074 15.2l1.363-5.648L.563 5.754l5.837-.423L8 0z"/>
                                </svg>
                            @endfor
                            <span class="sr-only">{{ $review->rating }} out of 5 stars</span>
                        </div>
                        <div class="review-meta">
                            <span class="review-location">{{ $review->reviewer_location ?? 'Verified Guest' }}</span>
                            <span class="review-separator">•</span>
                            <time class="review-date" datetime="{{ $review->created_at->format('Y-m-d') }}">
                                {{ $review->created_at->format('F Y') }}
                            </time>
                        </div>
                    </div>
                </div>
                <p class="review-text">
                    {{ $review->content }}
                </p>
            </article>
        @endforeach
    </div>
@else
    {{-- No reviews - return empty/minimal markup so section can be hidden --}}
    <div class="no-reviews" style="display: none;">
        <!-- No reviews available -->
    </div>
@endif
