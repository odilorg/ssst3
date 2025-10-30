{{-- Tour Reviews Partial --}}
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
        @if($tour->reviews && $tour->reviews->isNotEmpty())
            @foreach($tour->reviews as $review)
                <article class="review-card">
                    <div class="review-header">
                        <img src="{{ $review->reviewer_avatar ?? 'images/reviewers/avatar-placeholder.jpg' }}"
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
                                <span class="review-location">{{ $review->reviewer_country ?? 'Verified Guest' }}</span>
                                <span class="review-separator">•</span>
                                <time class="review-date" datetime="{{ $review->created_at->format('Y-m-d') }}">
                                    {{ $review->created_at->format('F Y') }}
                                </time>
                            </div>
                        </div>
                    </div>
                    <p class="review-text">
                        {{ $review->comment }}
                    </p>
                </article>
            @endforeach
        @else
            {{-- Fallback: Default reviews if none in database --}}
            <article class="review-card">
                <div class="review-header">
                    <img src="images/reviewers/avatar-placeholder.jpg" alt="" class="reviewer-avatar" width="56" height="56" loading="lazy">
                    <div class="reviewer-info">
                        <h3 class="reviewer-name">Sarah Mitchell</h3>
                        <div class="review-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="icon icon--star" width="18" height="18" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                    <path d="M8 0l2.163 5.331 5.837.423-4.437 3.798 1.363 5.648L8 12.331 3.074 15.2l1.363-5.648L.563 5.754l5.837-.423L8 0z"/>
                                </svg>
                            @endfor
                            <span class="sr-only">5 out of 5 stars</span>
                        </div>
                        <div class="review-meta">
                            <span class="review-location">United States</span>
                            <span class="review-separator">•</span>
                            <time class="review-date" datetime="2024-10-15">October 2024</time>
                        </div>
                    </div>
                </div>
                <p class="review-text">
                    Absolutely breathtaking! Our guide was incredibly knowledgeable about the history of Samarkand and made every monument come alive with stories. The Registan Square at sunset was magical. Highly recommend this tour to anyone visiting Uzbekistan.
                </p>
            </article>

            <article class="review-card">
                <div class="review-header">
                    <img src="images/reviewers/avatar-placeholder.jpg" alt="" class="reviewer-avatar" width="56" height="56" loading="lazy">
                    <div class="reviewer-info">
                        <h3 class="reviewer-name">James Chen</h3>
                        <div class="review-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="icon icon--star" width="18" height="18" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                    <path d="M8 0l2.163 5.331 5.837.423-4.437 3.798 1.363 5.648L8 12.331 3.074 15.2l1.363-5.648L.563 5.754l5.837-.423L8 0z"/>
                                </svg>
                            @endfor
                            <span class="sr-only">5 out of 5 stars</span>
                        </div>
                        <div class="review-meta">
                            <span class="review-location">Singapore</span>
                            <span class="review-separator">•</span>
                            <time class="review-date" datetime="2024-09-28">September 2024</time>
                        </div>
                    </div>
                </div>
                <p class="review-text">
                    Perfect tour for photography enthusiasts! Our guide knew all the best spots and angles for photos. The Shah-i-Zinda necropolis was stunning with its blue tiles. Great pace, not rushed at all. Worth every penny!
                </p>
            </article>

            <article class="review-card">
                <div class="review-header">
                    <img src="images/reviewers/avatar-placeholder.jpg" alt="" class="reviewer-avatar" width="56" height="56" loading="lazy">
                    <div class="reviewer-info">
                        <h3 class="reviewer-name">Emma Rodriguez</h3>
                        <div class="review-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="icon icon--star" width="18" height="18" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                    <path d="M8 0l2.163 5.331 5.837.423-4.437 3.798 1.363 5.648L8 12.331 3.074 15.2l1.363-5.648L.563 5.754l5.837-.423L8 0z"/>
                                </svg>
                            @endfor
                            <span class="sr-only">5 out of 5 stars</span>
                        </div>
                        <div class="review-meta">
                            <span class="review-location">Spain</span>
                            <span class="review-separator">•</span>
                            <time class="review-date" datetime="2024-09-12">September 2024</time>
                        </div>
                    </div>
                </div>
                <p class="review-text">
                    An unforgettable experience! The architecture is mind-blowing, and the guide's passion for Uzbek history was contagious. Loved the small group size - it felt personal and intimate. The Bibi-Khanym Mosque was my favorite stop.
                </p>
            </article>
        @endif
    </div>
