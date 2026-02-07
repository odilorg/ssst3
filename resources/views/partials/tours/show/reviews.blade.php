{{-- Tour Reviews Partial --}}
<section class="tour-reviews" id="reviews">
    @if(isset($reviews) && $reviews->isNotEmpty())
        <div class="reviews-header">
            <h2 class="section-title">{{ __('ui.sections.customer_reviews') }}</h2>
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
                    <span class="rating-count">{{ trans_choice('ui.tour.based_on_reviews', $tour->review_count, ['count' => $tour->review_count]) }}</span>
                </div>
            </div>
        </div>

        <div class="reviews-list">
            @foreach($reviews as $review)
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
                                <span class="review-location">{{ $review->reviewer_location ?? __('ui.reviews.verified_guest') }}</span>
                                <span class="review-separator">•</span>
                                <time class="review-date" datetime="{{ $review->created_at->format('Y-m-d') }}">
                                    {{ $review->created_at->format('F Y') }}
                                </time>
                            </div>
                        </div>
                    </div>
                    @if($review->title)
                        <h4 class="review-title">{{ $review->title }}</h4>
                    @endif
                    <p class="review-text">
                        {{ $review->content }}
                    </p>
                </article>
            @endforeach
        </div>
    @endif

    {{-- Review Submission Form - Matches "Extra Services" section pattern --}}
    <div class="review-form-wrapper">
        <h2 class="section-title">{{ __('ui.reviews.write_review') }}</h2>
        <p class="section-intro">{{ __('ui.reviews.share_experience') }}</p>

        <div class="review-form">
            <form id="reviewForm" data-tour-slug="{{ $tour->slug }}">
                @csrf

                {{-- Honeypot field (hidden from users, trap for bots) --}}
                <input type="text" name="honeypot" style="display:none" tabindex="-1" autocomplete="off">

                {{-- Star Rating Selector --}}
                <div class="form-group">
                    <label for="rating">{{ __('ui.reviews.rate_experience') }} <span class="required">{{ __('ui.reviews.required') }}</span></label>
                    <div class="star-rating-input" id="starRatingInput">
                        <input type="hidden" name="rating" id="ratingValue" value="0" required>
                        <div class="star-buttons">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="star-btn" data-rating="{{ $i }}" aria-label="Rate {{ $i }} stars">
                                    <svg class="review-star" width="24" height="24" viewBox="0 0 16 16">
                                        <path d="M8 0l2.163 5.331 5.837.423-4.437 3.798 1.363 5.648L8 12.331 3.074 15.2l1.363-5.648L.563 5.754l5.837-.423L8 0z"/>
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        <span class="rating-label" id="ratingLabel">{{ __('ui.reviews.select_rating') }}</span>
                    </div>
                    <span class="error-message" id="error-rating"></span>
                </div>

                {{-- Review Title --}}
                <div class="form-group">
                    <label for="reviewTitle">{{ __('ui.reviews.summarize_experience') }} <span class="required">{{ __('ui.reviews.required') }}</span></label>
                    <input type="text" id="reviewTitle" name="title" required minlength="5" maxlength="150"
                           placeholder="{{ __('ui.reviews.summarize_placeholder') }}">
                    <span class="error-message" id="error-title"></span>
                </div>

                {{-- Review Content --}}
                <div class="form-group">
                    <label for="reviewContent">{{ __('ui.reviews.share_feedback') }} <span class="required">{{ __('ui.reviews.required') }}</span></label>
                    <textarea id="reviewContent" name="content" required minlength="20" maxlength="2000" rows="6"
                              placeholder="{{ __('ui.reviews.share_placeholder') }}"></textarea>
                    <span class="error-message" id="error-content"></span>
                    <span class="char-count"><span id="charCount">0</span>/2000</span>
                </div>

                {{-- Reviewer Information --}}
                <div class="form-row">
                    <div class="form-group">
                        <label for="reviewerName">{{ __('ui.reviews.your_name') }} <span class="required">{{ __('ui.reviews.required') }}</span></label>
                        <input type="text" id="reviewerName" name="reviewer_name" required maxlength="100" placeholder="{{ __('ui.reviews.your_name') }}">
                        <span class="error-message" id="error-reviewer_name"></span>
                    </div>

                    <div class="form-group">
                        <label for="reviewerEmail">{{ __('ui.reviews.your_email') }} <span class="required">{{ __('ui.reviews.required') }}</span></label>
                        <input type="email" id="reviewerEmail" name="reviewer_email" required maxlength="150" placeholder="your@email.com">
                        <span class="error-message" id="error-reviewer_email"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="reviewerLocation">{{ __('ui.reviews.location') }}</label>
                        <input type="text" id="reviewerLocation" name="reviewer_location" maxlength="100" placeholder="{{ __('ui.reviews.location_placeholder') }}">
                        <span class="error-message" id="error-reviewer_location"></span>
                    </div>

                    <div class="form-group">
                        <label for="bookingReference">{{ __('ui.reviews.booking_reference') }}</label>
                        <input type="text" id="bookingReference" name="booking_reference" maxlength="50"
                               placeholder="Optional">
                        <span class="error-message" id="error-booking_reference"></span>
                        <span class="field-hint">{{ __('ui.reviews.booking_reference_hint') }}</span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn--primary" id="submitReview">
                        <span class="btn-text">{{ __('ui.reviews.submit_review') }}</span>
                        <span class="btn-loader" style="display:none">
                            <i class="fas fa-spinner fa-spin"></i> {{ __('ui.reviews.submitting') }}
                        </span>
                    </button>
                </div>

                <div class="form-message" id="formMessage" style="display:none"></div>
            </form>
        </div>
    </div>
</section>
