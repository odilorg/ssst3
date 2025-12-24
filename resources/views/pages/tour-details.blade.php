@extends('layouts.main')

@section('title', $pageTitle)
@section('meta_description', $metaDescription)
@section('canonical', $canonicalUrl)

{{-- Open Graph / Facebook --}}
@section('og_type', 'website')
@section('og_url', $canonicalUrl)
@section('og_title', $pageTitle)
@section('og_description', $metaDescription)
@section('og_image', $ogImage)

{{-- Twitter Card --}}
@section('twitter_url', $canonicalUrl)
@section('twitter_title', $pageTitle)
@section('twitter_description', $metaDescription)
@section('twitter_image', $ogImage)

{{-- Structured Data for Tour --}}
@section('structured_data')
{!! $structuredData ?? '{}' !!}
@endsection
@section('content')
  <!-- =====================================================
       SECTION 2: TOUR HEADER INFO (Title, Rating, Meta, Tabs)
       ===================================================== -->
  <section class="tour-header"
           hx-get="{{ url('/partials/tours/' . $tour->slug . '/hero') }}"
           hx-trigger="load"
           hx-swap="innerHTML"
           data-tour-slug="{{ $tour->slug }}">

    <!-- Loading Skeleton -->
    <div class="container">
      <div class="skeleton skeleton--text" style="width: 40%; height: 16px; margin-bottom: 1rem;"></div>
      <div class="skeleton skeleton--title" style="height: 40px; width: 80%; margin-bottom: 1rem;"></div>
      <div class="skeleton skeleton--text" style="width: 30%; height: 20px;"></div>
    </div>

  </section>

  <!-- =====================================================
       SECTION 3: TOUR HERO GALLERY
       ===================================================== -->
  <section class="tour-hero">
    <div class="container">

      <!-- Skeleton Loader (hidden once loaded) -->
      <div class="tour-hero__skeleton" aria-hidden="true">
        <div class="skeleton skeleton--hero"></div>
        <div class="skeleton skeleton--thumbnails">
          <div class="skeleton skeleton--thumb"></div>
          <div class="skeleton skeleton--thumb"></div>
          <div class="skeleton skeleton--thumb"></div>
          <div class="skeleton skeleton--thumb"></div>
        </div>
      </div>

      <!-- Actual Gallery -->
      <!-- Actual Gallery - Loaded via HTMX -->
      <div class="tour-hero__gallery is-hidden"
           hx-get="{{ url('/partials/tours/' . $tour->slug . '/gallery') }}"
           hx-trigger="load"
           hx-swap="innerHTML"
           data-tour-slug="{{ $tour->slug }}">
      </div>
    </div>
  </section>

  <!-- =====================================================
       TWO-COLUMN LAYOUT: MAIN CONTENT + BOOKING SIDEBAR
       ===================================================== -->
  <div class="tour-content-wrapper">
    <div class="container">
      <div class="tour-layout">

        <!-- LEFT COLUMN: Main Tour Content -->
        <main class="tour-main-content" id="main-content">

          <!-- Overview Section -->
          <section class="tour-overview" id="overview"
                   hx-get="{{ url('/partials/tours/' . $tour->slug . '/overview') }}"
                   hx-trigger="load"
                   hx-swap="innerHTML"
                   data-tour-slug="{{ $tour->slug }}">

            <!-- Loading Skeleton -->
            <h2 class="section-title">Overview</h2>
            <div class="skeleton skeleton--text" style="width: 90%; height: 16px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton--text" style="width: 85%; height: 16px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton--text" style="width: 92%; height: 16px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton--text" style="width: 88%; height: 16px; margin-bottom: 0.5rem;"></div>

          </section>

          <!-- Highlights Section -->
          <section class="tour-highlights" id="highlights"
                   hx-get="{{ url('/partials/tours/' . $tour->slug . '/highlights') }}"
                   hx-trigger="load"
                   hx-swap="innerHTML"
                   data-tour-slug="{{ $tour->slug }}">

            <!-- Loading Skeleton -->
            <h2 class="section-title">Highlights</h2>
            <div class="skeleton skeleton--text" style="width: 95%; height: 16px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton--text" style="width: 92%; height: 16px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton--text" style="width: 88%; height: 16px; margin-bottom: 0.5rem;"></div>

          </section>

          <!-- Includes/Excludes Section -->
          <section class="tour-includes-excludes" id="includes"
                   hx-get="{{ url('/partials/tours/' . $tour->slug . '/included-excluded') }}"
                   hx-trigger="load"
                   hx-swap="innerHTML"
                   data-tour-slug="{{ $tour->slug }}">

            <!-- Loading Skeleton -->
            <h2 class="section-title">What's Included & Excluded</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
              <div>
                <div class="skeleton skeleton--text" style="width: 90%; height: 16px; margin-bottom: 0.5rem;"></div>
                <div class="skeleton skeleton--text" style="width: 85%; height: 16px; margin-bottom: 0.5rem;"></div>
                <div class="skeleton skeleton--text" style="width: 92%; height: 16px; margin-bottom: 0.5rem;"></div>
              </div>
              <div>
                <div class="skeleton skeleton--text" style="width: 88%; height: 16px; margin-bottom: 0.5rem;"></div>
                <div class="skeleton skeleton--text" style="width: 93%; height: 16px; margin-bottom: 0.5rem;"></div>
                <div class="skeleton skeleton--text" style="width: 87%; height: 16px; margin-bottom: 0.5rem;"></div>
              </div>
            </div>

          </section>

          <!-- Cancellation Policy Section -->
          <section class="tour-cancellation" id="cancellation"
                   data-tour-slug="{{ $tour->slug }}"
                   hx-get="{{ url('/partials/tours/' . $tour->slug . '/cancellation') }}"
                   hx-trigger="load"
                   hx-swap="innerHTML">
            <div class="loading-spinner">Loading cancellation policy...</div>
          </section>

          <!-- Itinerary Section -->
          <section class="tour-itinerary" id="itinerary"
                   hx-get="{{ url('/partials/tours/' . $tour->slug . '/itinerary') }}"
                   hx-trigger="load"
                   hx-swap="innerHTML"
                   aria-label="Tour itinerary"
                   data-tour-slug="{{ $tour->slug }}">

            <!-- Loading Skeleton -->
            <div class="itinerary-header">
              <h2 class="section-title">Tour Itinerary</h2>
            </div>
            <div class="skeleton skeleton--text" style="width: 92%; height: 16px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton--text" style="width: 88%; height: 16px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton--text" style="width: 90%; height: 16px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton--text" style="width: 85%; height: 16px; margin-bottom: 0.5rem;"></div>

          </section>

          <!-- Meeting Point & Pickup Section -->
          <section class="tour-meeting" id="meeting-point"
                   hx-get="{{ url('/partials/tours/' . $tour->slug . '/meeting-point') }}"
                   hx-trigger="load"
                   hx-swap="innerHTML"
                   data-tour-slug="{{ $tour->slug }}">

            <!-- Loading Skeleton -->
            <h2 class="section-title">Meeting Point & Pickup</h2>
            <div class="skeleton skeleton--text" style="width: 95%; height: 16px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 92%; height: 16px; margin-bottom: 1rem;"></div>
            {{-- Map skeleton removed --}}
          </section>

          <!-- Know Before You Go Section -->
          <section class="tour-know-before" id="know-before"
                   hx-get="{{ url('/partials/tours/' . $tour->slug . '/requirements') }}"
                   hx-trigger="load"
                   hx-swap="innerHTML"
                   data-tour-slug="{{ $tour->slug }}">

            <!-- Loading Skeleton -->
            <h2 class="section-title">Know Before You Go</h2>
            <div class="skeleton skeleton--text" style="width: 95%; height: 16px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 92%; height: 16px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 90%; height: 16px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 88%; height: 16px; margin-bottom: 1rem;"></div>

          </section>

          <!-- FAQ Section -->
          <section class="tour-faq" id="faq"
                   hx-get="{{ url('/partials/tours/' . $tour->slug . '/faqs') }}"
                   hx-trigger="load"
                   hx-swap="innerHTML"
                   data-tour-slug="{{ $tour->slug }}">

            <!-- Loading Skeleton -->
            <h2 class="section-title">Frequently Asked Questions</h2>
            <div class="skeleton skeleton--text" style="width: 95%; height: 16px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 92%; height: 16px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 90%; height: 16px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 88%; height: 16px; margin-bottom: 1rem;"></div>

          </section>

          {{-- Extra Services Section - DISABLED --}}
          {{--
          <section class="tour-extras" id="extras"
                   hx-get="{{ url('/partials/tours/' . $tour->slug . '/extras') }}"
                   hx-trigger="load"
                   hx-swap="innerHTML"
                   data-tour-slug="{{ $tour->slug }}">

            <!-- Loading Skeleton -->
            <h2 class="section-title">Extra Services</h2>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
              <div class="skeleton skeleton--thumb"></div>
              <div class="skeleton skeleton--thumb"></div>
              <div class="skeleton skeleton--thumb"></div>
              <div class="skeleton skeleton--thumb"></div>
            </div>

          </section>
          --}}

          <!-- Customer Reviews Section -->
          @if($tour->review_count > 0)
          <section class="tour-reviews" id="reviews"
                   hx-get="{{ url('/partials/tours/' . $tour->slug . '/reviews') }}"
                   hx-trigger="revealed"
                   hx-swap="innerHTML"
                   data-tour-slug="{{ $tour->slug }}">

            <!-- Loading Skeleton -->
            <div class="reviews-header">
              <h2 class="section-title">Customer Reviews</h2>
            </div>
            <div class="skeleton skeleton--text" style="width: 100%; height: 80px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 100%; height: 80px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 100%; height: 80px; margin-bottom: 1rem;"></div>

          </section>
          @endif

        </main>

        <!-- RIGHT COLUMN: Booking Sidebar -->
        <aside class="booking-sidebar" data-sticky="true">

          <!-- Tour Data for JavaScript -->
          <script type=application/json id=tour-data>
{!! json_encode([
  'id' => $tour->slug,
  'name' => html_entity_decode($tour->title, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
  'pricePerPerson' => floatval($tour->price_per_person ?? 0),
  'showPrice' => boolval($tour->show_price ?? true),
  'currency' => $tour->currency ?? 'USD',
  'maxGuests' => intval($tour->max_guests ?? 15),
  'minGuests' => intval($tour->min_guests ?? 1),
  'duration' => $tour->duration_text ?? ($tour->duration_days . ' days')
], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
          </script>

          <!-- Screen Reader Live Region for Dynamic Updates -->
          <div aria-live="polite" aria-atomic="true" class="sr-only" id="booking-status"></div>

          <!-- Skeleton Loader -->
          <div class="booking-sidebar__skeleton" aria-hidden="true">
            <div class="skeleton skeleton--sidebar-top"></div>
            <div class="skeleton skeleton--sidebar-inputs"></div>
            <div class="skeleton skeleton--sidebar-buttons"></div>
          </div>

          <!-- Booking Card -->
          <div class="booking-card">

            <!-- Price Header -->
            <div class="booking-card__header">
              @if($tour->shouldShowPrice())
                @php
                  // Get tier pricing for default 2 guests, or fallback to base price
                  $defaultGuestCount = 2;
                  $defaultTier = $tour->getPricingTierForGuests($defaultGuestCount);

                  if ($defaultTier) {
                    $displayPricePerPerson = $defaultTier->price_per_person;
                    $displayTotal = $defaultTier->price_total;
                  } else {
                    $displayPricePerPerson = $tour->price_per_person;
                    $displayTotal = $tour->price_per_person * $defaultGuestCount;
                  }
                @endphp
                <div class="booking-price">
                  <span class="price-label">from</span>
                  <span class="price-amount" data-base-price="{{ $tour->price_per_person }}">${{ number_format($displayPricePerPerson, 2) }}</span>
                  <span class="price-unit">/person</span>
                </div>
              @else
                <div class="booking-price-hidden">
                  <span class="price-contact-label">Price on request</span>
                  <p class="price-contact-text">Contact us for a personalized quote</p>
                </div>
              @endif
            </div>

            <!-- Price Breakdown -->
            @if($tour->shouldShowPrice())
            <div class="price-breakdown" data-breakdown-visible="true">
              <h3 class="breakdown-title">Price Breakdown</h3>
              <div class="breakdown-items">
                <div class="breakdown-item">
                  <span class="breakdown-label">
                    <span class="breakdown-guests" data-guests="{{ $defaultGuestCount }}">{{ $defaultGuestCount }} guests</span> ×
                    <span class="breakdown-unit-price" data-unit-price="{{ $displayPricePerPerson }}">${{ number_format($displayPricePerPerson, 2) }}</span>
                  </span>
                  <span class="breakdown-value" data-subtotal="{{ $displayTotal }}">${{ number_format($displayTotal, 2) }}</span>
                </div>
                <div class="breakdown-item breakdown-item--total">
                  <span class="breakdown-label">Total</span>
                  <span class="breakdown-value breakdown-total" data-total="{{ $displayTotal }}">${{ number_format($displayTotal, 2) }}</span>
                </div>
              </div>
              <p class="breakdown-note">
                <strong>Cancellation Policy:</strong><br>
                • 60+ days before: Full refund<br>
                • 30-59 days: 75% refund<br>
                • 7-29 days: 50% refund<br>
                • Less than 7 days: No refund
              </p>
            @else
            <div class=price-breakdown data-breakdown-visible=true>
              <h3 class=breakdown-title>Price Breakdown</h3>
              <div class=breakdown-items>
                <div class=breakdown-item>
                  <span class=breakdown-label>
                    <span class=breakdown-guests data-guests=2>2 guests</span> ×
                    <span class=breakdown-unit-price data-unit-price="0">Contact us</span>
                  </span>
                  <span class="breakdown-value" data-subtotal="0">Please contact us</span>
                </div>
                <div class="breakdown-item breakdown-item--total">
                  <span class="breakdown-label">Total</span>
                  <span class="breakdown-value breakdown-total" data-total="0">Please contact us</span>
                </div>
              </div>
              <p class="breakdown-note">Contact us for pricing information</p>
            </div>
            </div>
            @endif

            <!-- Booking Form -->
            <form class="booking-form" id="booking-form" data-form-type="booking" action="/partials/bookings" method="POST">
              @csrf
              <!-- Hidden fields -->
              <input type="hidden" name="tour_id" id="tour-id" value="">

              <!-- Date Picker -->
              <div class="form-group">
                <label for="tour-date" class="form-label">
                  Date
                </label>
                <input
                  type="date"
                  id="tour-date"
                  name="tour-date"
                  class="form-input"
                  required
                  aria-required="true"
                  aria-describedby="date-hint date-error"
                  autocomplete="off"
                  min=""
                  data-min-date-offset="1">
                <span id="date-hint" class="form-hint">Select your preferred tour date (at least 24 hours in advance)</span>
                <span id="date-error" class="form-error" role="alert"></span>
              </div>

              <!-- Guest Selector -->
              <div class="form-group">
                <label for="tour-guests" class="form-label">
                  Guest
                </label>
                <select
                  id="tour-guests"
                  name="tour-guests"
                  class="form-input"
                  required
                  aria-required="true"
                  aria-describedby="guests-hint guests-error"
                  autocomplete="off"
                  data-price-calculator="true">
                  <option value="1">1 guest</option>
                  <option value="2" selected>2 guests</option>
                  <option value="3">3 guests</option>
                  <option value="4">4 guests</option>
                  <option value="5">5 guests</option>
                  <option value="6">6 guests</option>
                  <option value="7">7 guests</option>
                  <option value="8">8 guests</option>
                  <option value="9">9 guests</option>
                  <option value="10">10 guests</option>
                </select>
                <span id="guests-hint" class="form-hint">Maximum 10 guests per booking</span>
                <span id="guests-error" class="form-error" role="alert"></span>
<!-- Price Preview (Dynamic) -->                <div id="price-preview" class="price-preview-container" style="margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; display: none;">                  <div class="price-preview">                    <div class="price-usd" style="font-size: 1.5rem; font-weight: bold; color: #2c3e50;">Loading...</div>                    <div class="price-uzs" style="font-size: 1rem; color: #7f8c8d; margin-top: 0.25rem;"></div>                    <div class="price-label" style="font-size: 0.875rem; color: #95a5a6; margin-top: 0.5rem;"></div>                  </div>                </div>
              </div>

              <!-- Action Buttons: Book or Inquire -->
              <div class="booking-actions">
                <button type="button" class="action-btn action-btn--booking" data-action="booking">
                  <i class="fas fa-calendar-check"></i>
                  <div class="action-btn__content">
                    <span class="action-btn__title">Book This Tour</span>
                    <span class="action-btn__subtitle">Confirm your dates & guests</span>
                  </div>
                </button>

                <button type="button" class="action-btn action-btn--inquiry" data-action="inquiry">
                  <i class="fas fa-question-circle"></i>
                  <div class="action-btn__content">
                    <span class="action-btn__title">Ask a Question</span>
                    <span class="action-btn__subtitle">Get personalized information</span>
                  </div>
                </button>
              </div>

              <!-- STEP 2: Full Booking Form (Hidden Initially) -->
              <div id="step-2-full-form" style="display: none;">

                <!-- Customer Information -->
                <div class="form-section">
                  <h3 class="form-section__title">Your Information</h3>

                  <div class="form-group">
                    <label for="customer-name" class="form-label">
                      Full Name <span class="required">*</span>
                    </label>
                    <input type="text"
                           id="customer-name"
                           name="customer_name"
                           class="form-input"
                           placeholder="John Doe"
                           required>
                  </div>

                  <div class="form-group">
                    <label for="customer-email" class="form-label">
                      Email <span class="required">*</span>
                    </label>
                    <input type="email"
                           id="customer-email"
                           name="customer_email"
                           class="form-input"
                           placeholder="john@example.com"
                           required>
                  </div>

                  <div class="form-group">
                    <label for="customer-phone" class="form-label">
                      Phone <span class="required">*</span>
                    </label>
                    <input type="tel"
                           id="customer-phone"
                           name="customer_phone"
                           class="form-input"
                           placeholder="+998 91 123 45 67"
                           required>
                  </div>

                  <div class="form-group">
                    <label for="customer-country" class="form-label">
                      Country (Optional)
                    </label>
                    <input type="text"
                           id="customer-country"
                           name="customer_country"
                           class="form-input"
                           placeholder="United States">
                  </div>
                </div>

                <!-- Hidden field to track action type -->
                <input type="hidden" name="action_type" id="action-type" value="booking">

                <!-- Message Field (Required for Inquiry, Optional for Booking) -->
                <div class="form-section" id="message-section" style="display: none;">
                  <div class="form-group">
                    <label for="inquiry-message" class="form-label">
                      Your Message <span class="required" id="message-required">*</span>
                    </label>
                    <textarea id="inquiry-message"
                              name="message"
                              class="form-input"
                              rows="4"
                              placeholder="Please tell us about your questions or any specific requirements..."></textarea>
                    <span class="form-hint">Let us know what information you need about this tour</span>
                  </div>
                </div>

                <!-- Special Requests -->
                <div class="form-section">
                  <div class="form-group">
                    <label for="special-requests" class="form-label">
                      Special Requests (Optional)
                    </label>
                    <textarea id="special-requests"
                              name="special_requests"
                              class="form-input"
                              rows="3"
                              placeholder="Any special requirements or preferences?"></textarea>
                  </div>
                </div>

                <!-- Terms & Conditions -->
                <div class="form-section">
                  <label class="terms-checkbox__label">
                    <input type="checkbox"
                           name="agree_terms"
                           id="agree-terms"
                           class="terms-checkbox__input"
                           required>
                    <span class="terms-checkbox__text">
                      I agree to the <a href="/terms" target="_blank">Terms & Conditions</a>
                      and <a href="/privacy" target="_blank">Privacy Policy</a>
                    </span>
                  </label>
                </div>

                <!-- Submit Button -->
                <div class="form-actions">
                  <button type="submit" class="btn--submit btn--block" id="submit-button">
                    <span class="btn__text" id="submit-text">Continue to Payment</span>
                    <span class="spinner"></span>
                  </button>
                </div>

              </div><!-- End Step 2 -->

            </form>

            <!-- ================================================================ -->
            <!-- SIMPLE INQUIRY FORM (Separate from booking form)                -->
            <!-- ================================================================ -->
            <div id="simple-inquiry-form" class="simple-inquiry-form" style="display: none;">
              <div class="inquiry-header">
                <button type="button" class="inquiry-back-btn" id="inquiry-back-btn" aria-label="Go back">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                  </svg>
                </button>
                <h3 class="inquiry-title">Ask Us About This Tour</h3>
              </div>

              <p class="inquiry-subtitle">
                Have questions? We're here to help! We'll respond to your email within 24 hours.
              </p>

              <form id="inquiry-form" class="inquiry-form-fields">
                <input type="hidden" name="tour_id" id="inquiry-tour-id" value="">
                <input type="hidden" name="action_type" value="inquiry">

                <div class="form-group">
                  <label for="inquiry-name" class="form-label">
                    Your Name <span class="required">*</span>
                  </label>
                  <input type="text"
                         id="inquiry-name"
                         name="customer_name"
                         class="form-input"
                         placeholder="John Doe"
                         required
                         autocomplete="name">
                </div>

                <div class="form-group">
                  <label for="inquiry-email" class="form-label">
                    Email Address <span class="required">*</span>
                  </label>
                  <input type="email"
                         id="inquiry-email"
                         name="customer_email"
                         class="form-input"
                         placeholder="john@example.com"
                         required
                         autocomplete="email">
                  <span class="form-hint">We'll send our response to this email</span>
                </div>

                <div class="form-group">
                  <label for="inquiry-message" class="form-label">
                    Your Question <span class="required">*</span>
                  </label>
                  <textarea id="inquiry-message"
                            name="message"
                            class="form-input"
                            rows="6"
                            placeholder="What would you like to know about this tour?"
                            required></textarea>
                  <span class="form-hint">Ask about itinerary, pricing, availability, group sizes, or anything else!</span>
                </div>

                <div class="form-actions">
                  <button type="submit" class="btn btn--primary btn--large btn--block" id="submit-inquiry-btn">
                    <span class="btn__text">Send Question</span>
                    <span class="spinner" style="display: none;"></span>
                  </button>
                </div>
              </form>

              <div class="inquiry-note">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                <span>No dates or guest counts needed - just ask your question and we'll help!</span>
              </div>
            </div>

            <!-- Trust Badges -->
            <div class="trust-badges">
              <div class="badge-item">
                <svg class="icon icon--shield" width="18" height="20" viewBox="0 0 18 20" fill="currentColor" aria-hidden="true"><path d="M9 0L0 3v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V3L9 0zm0 2.18l7 2.09v5.23c0 4.65-3.19 8.98-7 10.05-3.81-1.07-7-5.4-7-10.05V4.27l7-2.09z"/></svg>
                <span>Secure payments</span>
              </div>
              <div class="badge-item">
                <svg class="icon icon--headset" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 0C4.5 0 0 4.5 0 10v5a3 3 0 003 3h2v-8H2v-2a8 8 0 1116 0v2h-3v8h2a3 3 0 003-3v-5c0-5.5-4.5-10-10-10z"/></svg>
                <span>24/7 support</span>
              </div>
              <div class="badge-item">
                <svg class="icon icon--undo" width="18" height="18" viewBox="0 0 18 18" fill="currentColor" aria-hidden="true"><path d="M2 8a6 6 0 1110.89 3.5.5.5 0 10.78.63A7 7 0 103 8h3l-4-4-4 4h3z"/></svg>
                <span>Flexible cancellation</span>
              </div>
            </div>

            <!-- Payment Security Badges -->
            <div class="payment-security-badges">
              <div class="security-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="2" y="5" width="20" height="14" rx="2" stroke="#94A3B8" stroke-width="1.5"/>
                  <path d="M2 10h20" stroke="#94A3B8" stroke-width="1.5"/>
                  <path d="M7 15h3" stroke="#94A3B8" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <span>Card payments</span>
              </div>
              <div class="security-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 2l2.4 7.4h7.6l-6 4.6 2.3 7.2L12 16.5 5.7 21.2 8 14l-6-4.6h7.6L12 2z" stroke="#94A3B8" stroke-width="1.5" fill="none"/>
                </svg>
                <span>Verified merchant</span>
              </div>
              <div class="security-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="#94A3B8"/>
                </svg>
                <span>PCI compliant</span>
              </div>
            </div>

            <!-- SSL Certificate Notice -->
            <div class="ssl-notice">
              <svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 5h1a1 1 0 011 1v7a1 1 0 01-1 1H1a1 1 0 01-1-1V6a1 1 0 011-1h1V4a4 4 0 118 0v1zm-2 0V4a2 2 0 10-4 0v1h4z" fill="#10B981"/>
              </svg>
              <span>SSL encrypted checkout</span>
            </div>

            <!-- Payment Methods -->
            <div class="payment-methods">
              <span class="payment-methods__label">We accept:</span>
              <div class="payment-methods__logos">
                <!-- Visa -->
                <svg width="32" height="20" viewBox="0 0 32 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect width="32" height="20" rx="2" fill="#F3F4F6"/>
                  <path d="M13.5 13h-2l1.2-7h2l-1.2 7zm5.5-7l-1.8 4.8L16.6 6h-2.1l2.8 7h2l2.7-7h-2zm4 0c-.4 0-.7.1-.9.3-.2.2-.3.4-.4.8l-1.4 5.9h2l.2-.9h2.4l.1.9h1.8L24.2 6H23zm-.2 4.5l.7-2 .4 2h-1.1zM11 6l-2 7H7l-1-5.4L5.5 13H3.6L5.8 6h2.7l.8 5 .4-5H11z" fill="#1A56DB"/>
                </svg>
                <!-- Mastercard -->
                <svg width="32" height="20" viewBox="0 0 32 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect width="32" height="20" rx="2" fill="#F3F4F6"/>
                  <circle cx="12" cy="10" r="5" fill="#EB001B"/>
                  <circle cx="20" cy="10" r="5" fill="#F79E1B"/>
                  <path d="M16 6.5c1.2.9 2 2.6 2 3.5s-.8 2.6-2 3.5c-1.2-.9-2-2.6-2-3.5s.8-2.6 2-3.5z" fill="#FF5F00"/>
                </svg>
                <!-- Octobank -->
                <svg width="32" height="20" viewBox="0 0 32 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect width="32" height="20" rx="2" fill="#F3F4F6"/>
                  <circle cx="16" cy="10" r="4" fill="#8B5CF6"/>
                  <path d="M16 6v8M12 10h8" stroke="#ffffff" stroke-width="1"/>
                </svg>
                <!-- Bank Transfer -->
                <svg width="32" height="20" viewBox="0 0 32 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect width="32" height="20" rx="2" fill="#F3F4F6"/>
                  <path d="M8 8h16v7H8V8zm1-2l7-3 7 3H9zm1 5h2v4h-2v-4zm7 0h2v4h-2v-4zm5 0h2v4h-2v-4z" fill="#64748B"/>
                </svg>
              </div>
            </div>

            <!-- Booking Clarification -->
            <div class="booking-clarification">
              <p class="clarification-text">
                <svg class="icon icon--clock" width="18" height="18" viewBox="0 0 18 18" fill="currentColor" aria-hidden="true"><path d="M9 0a9 9 0 100 18A9 9 0 009 0zm4 10H9a1 1 0 01-1-1V4a1 1 0 112 0v4h3a1 1 0 010 2z"/></svg>
                <strong>Instant booking confirmation</strong> — secure your spot with a deposit or save 3% by paying in full.
              </p>
              <p class="clarification-note">
                Choose 30% deposit now or pay in full and save 3%.
              </p>
            </div>


            <!-- Why Book Section -->
            <div class="booking-benefits">
              <h3 class="benefits-title">Why book with Jahongir Travel?</h3>
              <ul class="benefits-list">
                <li class="benefit-item">
                  <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/></svg>
                  <span>Best price guarantee</span>
                </li>
                <li class="benefit-item">
                  <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/></svg>
                  <span>Flexible cancellation policy</span>
                </li>
                <li class="benefit-item">
                  <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/></svg>
                  <span>Expert local guides</span>
                </li>
                <li class="benefit-item">
                  <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/></svg>
                  <span>24/7 customer support</span>
                </li>
              </ul>
            </div>

          </div>

        </aside>

      </div>
    </div>
  </div>

  <!-- Mobile Floating CTA (Task 13) -->
  <!-- Hidden on desktop, visible on mobile/tablet only via CSS -->
  <div class="mobile-booking-cta" data-mobile-only="true">
    <div class="mobile-cta__container">
      @if($tour->shouldShowPrice())
        @php
          // Use same tier pricing calculation as desktop
          $mobileTier = $tour->getPricingTierForGuests(2);
          $mobilePricePerPerson = $mobileTier ? $mobileTier->price_per_person : $tour->price_per_person;
        @endphp
        <div class="mobile-cta__price">
          <span class="mobile-cta__amount" data-mobile-price="{{ $tour->price_per_person }}">${{ number_format($mobilePricePerPerson, 2) }}</span>
          <span class="mobile-cta__unit">per person</span>
        </div>
      @else
        <div class="mobile-cta__contact">
          <span>Request Quote</span>
        </div>
      @endif
      <!-- Action Buttons Group -->
      <div class="mobile-cta__actions">
        <button type="button" class="btn btn--accent mobile-cta__button" data-scroll-to="booking-form" aria-label="Scroll to booking form">
          <svg class="icon icon--calendar-check" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6 2a2 2 0 00-2 2v1H2a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2h-2V4a2 2 0 00-2-2H6zm1 2h4v2H7V4zM2 9h14v8H2V9zm11.707 1.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
          Book Now
        </button>
        <a href="https://wa.me/998915550808?text=Hi!%20I'm%20interested%20in%20the%20{{ urlencode($tour->title) }}%20tour."
           class="mobile-cta__whatsapp"
           target="_blank"
           rel="noopener noreferrer"
           aria-label="Contact us on WhatsApp">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.149-.67.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.123-.272-.198-.57-.347z" fill="currentColor"/>
            <path d="M20.52 3.449C18.24 1.245 15.24 0 11.997 0 5.433 0 .104 5.334.101 11.906c0 2.096.546 4.142 1.587 5.945L0 24l6.304-1.654a11.882 11.882 0 005.684 1.448h.005c6.561 0 11.892-5.335 11.895-11.906 0-3.176-1.237-6.16-3.469-8.439zM11.997 21.709h-.004a9.859 9.859 0 01-5.031-1.378l-.361-.214-3.741.981.997-3.646-.235-.374a9.863 9.863 0 01-1.511-5.26c.002-5.45 4.436-9.883 9.889-9.883 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.892 6.993c-.002 5.45-4.436 9.883-9.884 9.883z" fill="currentColor"/>
          </svg>
        </a>
      </div>

      <!-- Mobile Trust Indicators -->
      <div class="mobile-cta__trust">
        <svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M10 5h1a1 1 0 011 1v7a1 1 0 01-1 1H1a1 1 0 01-1-1V6a1 1 0 011-1h1V4a4 4 0 118 0v1zm-2 0V4a2 2 0 10-4 0v1h4z" fill="#10B981"/>
        </svg>
        <span>Secure • SSL encrypted</span>
      </div>
    </div>
  </div>

  <!-- Scroll to Top Button -->
  <button id="scroll-to-top" class="scroll-to-top" aria-label="Scroll to top" title="Back to top">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M7 14l5-5 5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </button>

  <!-- BOOKING CONFIRMATION MODAL - Modern Minimalist Design -->
  <div id="booking-confirmation-modal" class="modal-overlay modal-overlay--glassmorphic" style="display: none;">
    <div class="modal-container modal-container--minimal">
      <!-- Minimal Header -->
      <div class="modal-header-minimal">
        <button class="modal-close-minimal" aria-label="Close">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 5L5 15M5 5l10 10"/>
          </svg>
        </button>
        <div class="success-animation">
          <svg class="checkmark-svg" width="56" height="56" viewBox="0 0 56 56">
            <circle class="checkmark-circle" cx="28" cy="28" r="26" fill="none" stroke="#059669" stroke-width="2"/>
            <path class="checkmark-check" d="M16 28l8 8 16-16" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h2 class="modal-title-minimal">Booking Confirmed</h2>
        <p class="modal-reference-minimal">
          <span id="modal-reference">BK-2025-XXX</span>
        </p>
      </div>

      <!-- Essential Info Only -->
      <div class="modal-body-minimal">
        <div class="booking-info-card">
          <div class="info-row">
            <span class="info-label">Tour</span>
            <span class="info-value" id="modal-tour-name">...</span>
          </div>
          <div class="info-row">
            <span class="info-label">Date</span>
            <span class="info-value" id="modal-date">...</span>
          </div>
          <div class="info-row">
            <span class="info-label">Guests</span>
            <span class="info-value" id="modal-guests">...</span>
          </div>
          <div class="info-row info-row--total">
            <span class="info-label">Total</span>
            <span class="info-value info-value--price" id="modal-total">$200.00</span>
          </div>
        </div>

        <!-- Minimal Next Steps -->
        <div class="next-steps-minimal">
          <p class="next-step-text">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" opacity="0.5">
              <path d="M2 4v8c0 .6.4 1 1 1h10c.6 0 1-.4 1-1V4c0-.6-.4-1-1-1H3c-.6 0-1 .4-1 1zm12 0L8 8.5 2 4h12zM3 12V5.3l4.7 3.5c.2.1.4.2.6.2s.4-.1.6-.2L14 5.3V12H3z"/>
            </svg>
            Confirmation sent to <span id="modal-customer-email">your email</span>
          </p>
        </div>
      </div>

      <!-- Payment Options -->
      <div class="modal-payment-options" id="payment-options-section">
        <h4 class="payment-options-title">Choose Payment Option</h4>

        <!-- Deposit Option -->
        <label class="payment-option-minimal recommended">
          <input type="radio" name="payment_type" value="deposit" checked>
          <div class="option-content-minimal">
            <div class="option-header-minimal">
              <span class="recommended-badge-minimal">RECOMMENDED</span>
              <strong>Pay 30% Deposit</strong>
            </div>
            <div class="option-details-minimal">
              <span class="deposit-amount" id="deposit-amount">$60</span>
              <span class="deposit-text">now, balance later</span>
            </div>
          </div>
        </label>

        <!-- Full Payment Option -->
        <label class="payment-option-minimal">
          <input type="radio" name="payment_type" value="full">
          <div class="option-content-minimal">
            <div class="option-header-minimal">
              <span class="discount-badge-minimal">SAVE 3%</span>
              <strong>Pay in Full</strong>
            </div>
            <div class="option-details-minimal">
              <span class="full-amount" id="full-amount">$194</span>
              <span class="full-text">with 3% discount</span>
            </div>
          </div>
        </label>
      </div>

      <!-- Single CTA -->
      <div class="modal-footer-minimal">
        <button class="btn-minimal-primary" id="proceed-to-payment-btn">
          <span id="payment-btn-text">Pay $60 Now</span>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
            <path d="M9.3 3.3a1 1 0 011.4 0l4 4a1 1 0 010 1.4l-4 4a1 1 0 01-1.4-1.4L11.6 9H2a1 1 0 110-2h9.6L9.3 4.7a1 1 0 010-1.4z"/>
          </svg>
        </button>

        <!-- Subtle trust indicators -->
        <div class="trust-minimal">
          <div class="trust-icons">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" opacity="0.4">
              <path d="M8 1C6.3 1 5 2.3 5 4v2H3c-.6 0-1 .4-1 1v6c0 .6.4 1 1 1h10c.6 0 1-.4 1-1V7c0-.6-.4-1-1-1h-2V4c0-1.7-1.3-3-3-3zm0 1.5c.8 0 1.5.7 1.5 1.5v2h-3V4c0-.8.7-1.5 1.5-1.5z"/>
            </svg>
            <span class="trust-text">Secure payment</span>
            <div class="payment-icons-minimal">
              <img src="/images/payments/visa.svg" alt="Visa" class="payment-icon-minimal">
              <img src="/images/payments/mastercard.svg" alt="Mastercard" class="payment-icon-minimal">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ================================================================ -->
  <!-- SIMPLE INQUIRY CONFIRMATION MODAL                               -->
  <!-- ================================================================ -->
  <div id="inquiry-confirmation-modal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
      <div class="modal-header">
        <div class="success-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
            <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
        </div>
        <h2 class="modal-title">Question Received!</h2>
        <p class="modal-subtitle">We'll respond to your email within 24 hours</p>
        <button class="modal-close" aria-label="Close">&times;</button>
      </div>

      <div class="modal-body">
        <!-- Reference Number -->
        <div class="confirmation-reference">
          <span class="label">Reference Number</span>
          <span class="reference-number" id="inquiry-modal-reference">INQ-2025-XXX</span>
          <p class="reference-note">Save this for your records</p>
        </div>

        <!-- Tour Info -->
        <div class="booking-summary">
          <h3 class="section-title">Your Question About</h3>
          <div class="summary-grid">
            <div class="summary-item" style="grid-column: 1 / -1;">
              <span class="label">Tour</span>
              <span class="value" id="inquiry-modal-tour" style="font-size: 1.125rem;">Tour Name</span>
            </div>
          </div>
        </div>

        <!-- Email Confirmation -->
        <div class="customer-info">
          <h3 class="section-title">We'll Reply To</h3>
          <p class="email-address" id="inquiry-modal-email">customer@example.com</p>
        </div>

        <!-- Next Steps -->
        <div class="next-steps">
          <h3 class="section-title">What Happens Next?</h3>
          <ol class="steps-list">
            <li>Our travel experts will review your question</li>
            <li>You'll receive a detailed response within <strong>24 hours</strong></li>
            <li>Check your email for our personalized reply</li>
            <li>We're here to help you plan the perfect trip!</li>
          </ol>
        </div>

        <!-- Important Note -->
        <div class="important-note">
          <svg class="warning-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
          </svg>
          <p><strong>Check your spam folder</strong> if you don't receive our email within an hour.</p>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn--primary btn--large" id="close-inquiry-modal">
          Got It, Thanks!
        </button>
      </div>
    </div>
  </div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('tour-details.css') }}">
<link rel="stylesheet" href="{{ asset('css/gallery-lightbox.css') }}">
<link rel="stylesheet" href="{{ asset('tour-details-gallery-addon.css') }}">
<link rel="stylesheet" href="{{ asset('css/tour-reviews.css') }}">
<style>
/* =====================================================
   MODERN MINIMALISTIC MOBILE BUTTON REDESIGN
   ===================================================== */

/* Modern Book Now Button - Mobile CTA */
.mobile-cta__button {
  /* Remove old styles */
  background: none !important;
  border: none !important;

  /* Modern gradient background */
  background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%) !important;
  color: #FFFFFF !important;

  /* Optimized typography */
  font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", sans-serif !important;
  font-size: 15px !important;
  font-weight: 600 !important;
  letter-spacing: 0.02em !important;
  text-transform: none !important;
  line-height: 1 !important;

  /* Balanced padding */
  padding: 13px 24px !important;

  /* Subtle rounded corners */
  border-radius: 10px !important;

  /* Modern shadow */
  box-shadow: 0 3px 12px rgba(102, 126, 234, 0.3) !important;

  /* Smooth transitions */
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;

  /* Perfect center alignment */
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 7px !important;

  /* Clean edges */
  outline: none !important;
  position: relative !important;
  overflow: hidden !important;
  white-space: nowrap !important;
  min-width: 130px !important;
  height: 44px !important;
}

/* Hover effect */
.mobile-cta__button:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 18px rgba(102, 126, 234, 0.4) !important;
  background: linear-gradient(135deg, #764BA2 0%, #667EEA 100%) !important;
}

/* Active state */
.mobile-cta__button:active {
  transform: translateY(0) !important;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
}

/* Icon alignment */
.mobile-cta__button svg {
  width: 17px !important;
  height: 17px !important;
  fill: currentColor !important;
  opacity: 0.95 !important;
  margin-top: -1px !important;
  flex-shrink: 0 !important;
}

/* Modern price styling */
.mobile-cta__price {
  display: flex !important;
  flex-direction: column !important;
  align-items: flex-start !important;
  line-height: 1 !important;
  gap: 3px !important;
}

.mobile-cta__amount {
  font-size: 20px !important;
  font-weight: 700 !important;
  color: #1A202C !important;
  line-height: 1 !important;
  letter-spacing: -0.03em !important;
  font-family: -apple-system, BlinkMacSystemFont, "Inter", sans-serif !important;
}

.mobile-cta__unit {
  font-size: 12px !important;
  color: #64748B !important;
  font-weight: 500 !important;
  line-height: 1.2 !important;
  letter-spacing: 0.01em !important;
}

/* Container improvements - Better layout */
.mobile-cta__container {
  display: grid !important;
  grid-template-columns: 1fr auto auto !important;
  align-items: center !important;
  gap: 12px !important;
  padding: 12px 16px !important;
  background: rgba(255, 255, 255, 0.98) !important;
  backdrop-filter: blur(20px) !important;
  -webkit-backdrop-filter: blur(20px) !important;
  border-top: 1px solid rgba(0, 0, 0, 0.06) !important;
}

/* WhatsApp button styling */
.mobile-cta__whatsapp {
  /* Remove default styles */
  background: none !important;
  border: none !important;

  /* WhatsApp brand green */
  background: linear-gradient(135deg, #25D366 0%, #128C7E 100%) !important;
  color: #FFFFFF !important;

  /* Size and shape */
  width: 44px !important;
  height: 44px !important;
  border-radius: 50% !important;
  padding: 0 !important;

  /* Perfect center */
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;

  /* Shadow and effects */
  box-shadow: 0 2px 8px rgba(37, 211, 102, 0.3) !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  cursor: pointer !important;
  flex-shrink: 0 !important;
}

.mobile-cta__whatsapp:hover {
  transform: scale(1.1) !important;
  box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4) !important;
}

.mobile-cta__whatsapp:active {
  transform: scale(0.95) !important;
}

.mobile-cta__whatsapp svg {
  width: 24px !important;
  height: 24px !important;
  fill: #FFFFFF !important;
}

/* Hide the main floating WhatsApp when mobile CTA is visible */
@media (max-width: 767px) {
  .mobile-booking-cta {
    box-shadow: 0 -2px 20px rgba(0, 0, 0, 0.1) !important;
    z-index: 999 !important;
  }

  /* Hide floating WhatsApp button when mobile CTA bar is visible */
  .whatsapp-float {
    display: none !important;
  }

  /* Optional: Create button group for Book + WhatsApp */
  .mobile-cta__actions {
    display: flex !important;
    gap: 10px !important;
    align-items: center !important;
  }
}

/* =====================================================
   FIX: HAMBURGER MENU VISIBILITY ON MOBILE
   ===================================================== */
@media (max-width: 768px) {
  /* Show hamburger menu on mobile */
  .nav__toggle {
    display: block !important;
    background: none !important;
    border: none !important;
    padding: 8px !important;
    cursor: pointer !important;
    position: relative !important;
    z-index: 1001 !important;
    min-width: 44px !important;
    min-height: 44px !important;
  }

  /* Style the hamburger icon bars */
  .nav__toggle-icon-bars {
    display: block !important;
    font-size: 20px !important;
    color: var(--color-text, #1E1E1E) !important;
  }

  .nav__toggle-icon-close {
    display: none !important;
  }

  /* When menu is open */
  .nav__toggle[aria-expanded="true"] .nav__toggle-icon-bars {
    display: none !important;
  }

  .nav__toggle[aria-expanded="true"] .nav__toggle-icon-close {
    display: block !important;
    font-size: 22px !important;
    color: var(--color-text, #1E1E1E) !important;
  }

  /* Hide desktop menu on mobile */
  .nav__menu {
    display: none !important;
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    right: 0 !important;
    background: white !important;
    flex-direction: column !important;
    padding: 1rem !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    z-index: 1000 !important;
  }

  /* Show menu when toggled */
  .nav__menu.active,
  .nav__menu.show {
    display: flex !important;
  }

  .nav__menu li {
    margin: 0.5rem 0 !important;
    list-style: none !important;
  }

  .nav__menu a {
    display: block !important;
    padding: 0.75rem 1rem !important;
    color: var(--color-text, #1E1E1E) !important;
    text-decoration: none !important;
    border-radius: 6px !important;
    transition: background 0.2s !important;
  }

  .nav__menu a:hover {
    background: rgba(13, 76, 146, 0.08) !important;
  }

  .nav__menu a.active {
    background: rgba(13, 76, 146, 0.12) !important;
    color: var(--color-primary, #0D4C92) !important;
    font-weight: 600 !important;
  }

  /* Ensure nav container is positioned correctly */
  .nav {
    position: relative !important;
  }

  /* Adjust nav layout on mobile */
  .nav .container {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    position: relative !important;
  }

  /* Logo should be on the left */
  .nav__logo {
    flex: 1 !important;
  }

  /* Toggle button should be on the right */
  .nav__toggle {
    margin-left: auto !important;
  }
}

/* Booking Actions - Improved Hierarchy */
.booking-actions{display:flex;flex-direction:column;gap:1rem;margin-bottom:1.5rem}

/* Primary Booking Button - Dominant */
.action-btn--booking{
  display:flex;align-items:center;justify-content:center;gap:0.75rem;
  padding:1.25rem 2rem;
  background:#0D4C92;
  color:#fff;
  border:none;
  border-radius:8px;
  cursor:pointer;
  transition:all 0.2s;
  text-align:center;
  width:100%;
  font-size:1.0625rem;
  font-weight:600;
  box-shadow:0 2px 8px rgba(13,76,146,0.2)
}
.action-btn--booking:hover{
  background:#0B3D75;
  transform:translateY(-2px);
  box-shadow:0 4px 12px rgba(13,76,146,0.3)
}
.action-btn--booking:active{transform:translateY(0)}
.action-btn--booking i{font-size:1.25rem;color:#fff}

/* Secondary Inquiry Link - Subtle */
.action-btn--inquiry{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:0.5rem;
  padding:0.75rem;
  background:transparent;
  color:#0D4C92;
  border:none;
  cursor:pointer;
  transition:all 0.2s;
  text-align:center;
  width:100%;
  font-size:0.9375rem;
  font-weight:500;
  text-decoration:underline;
  text-underline-offset:3px
}
.action-btn--inquiry:hover{
  color:#0B3D75;
  text-decoration-thickness:2px
}
.action-btn--inquiry i{font-size:1rem;color:#0D4C92}

.action-btn__content{display:flex;flex-direction:column;gap:0.25rem}
.action-btn__title{font-size:inherit;font-weight:inherit;color:inherit}
.action-btn__subtitle{font-size:0.8125rem;opacity:0.9;line-height:1.3}

    .modal-overlay {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0, 0, 0, 0.75);
      z-index: 99999;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      animation: fadeIn 0.2s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .modal-container {
      background: #ffffff;
      max-width: 600px;
      width: 100%;
      max-height: 90vh;
      overflow-y: auto;
      border-radius: 16px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
      animation: slideUp 0.3s ease-out;
      position: relative;
    }

    @keyframes slideUp {
      from { transform: translateY(30px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
      text-align: center;
      padding: 2rem 2rem 1.5rem;
      border-bottom: 1px solid #e5e7eb;
      position: sticky;
      top: 0;
      background: white;
      z-index: 10;
    }

    .success-icon {
      width: 72px; height: 72px;
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .modal-title {
      font-size: 1.75rem;
      font-weight: 700;
      color: #111827;
      margin-bottom: 0.5rem;
    }

    .modal-subtitle {
      font-size: 1rem;
      color: #6b7280;
      margin: 0;
    }

    .modal-close {
      position: absolute;
      top: 1rem; right: 1rem;
      width: 36px; height: 36px;
      border: none;
      background: rgba(243, 244, 246, 0.95);
      color: #6b7280;
      font-size: 24px;
      line-height: 1;
      border-radius: 50%;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 20;
      backdrop-filter: blur(4px);
    }

    .modal-close:hover {
      background: #e5e7eb;
      color: #111827;
    }

    .modal-body {
      padding: 1.5rem;
    }

    .confirmation-reference {
      background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
      padding: 1rem 1.25rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
      border-left: 4px solid #0ea5e9;
    }

    .reference-content {
      display: flex;
      align-items: baseline;
      gap: 0.75rem;
      flex-wrap: wrap;
    }

    .confirmation-reference .label {
      font-size: 0.875rem;
      color: #0369a1;
      font-weight: 500;
    }

    .reference-number {
      font-size: 1.125rem;
      font-weight: 700;
      color: #075985;
      letter-spacing: 1px;
      font-family: 'Courier New', monospace;
    }

    .booking-summary,
    .next-steps {
      margin-bottom: 1.5rem;
    }

    .section-title {
      font-size: 1rem;
      font-weight: 600;
      color: #111827;
      margin-bottom: 0.875rem;
    }

    /* Steps Grid - Modern Card Layout */
    .steps-grid {
      display: flex;
      flex-direction: column;
      gap: 0.875rem;
    }

    .step-item {
      display: flex;
      gap: 1rem;
      padding: 1rem;
      background: #f9fafb;
      border-radius: 8px;
      border: 1px solid #e5e7eb;
      transition: all 0.2s;
    }

    .step-item:hover {
      background: #f3f4f6;
      border-color: #d1d5db;
    }

    .step-icon {
      font-size: 1.5rem;
      flex-shrink: 0;
      line-height: 1;
    }

    .step-content {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
      flex: 1;
    }

    .step-content strong {
      color: #111827;
      font-size: 0.9375rem;
      font-weight: 600;
    }

    .step-detail {
      color: #6b7280;
      font-size: 0.875rem;
      line-height: 1.4;
    }

    .summary-grid {
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      overflow: hidden;
    }

    .summary-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.875rem 1rem;
      border-bottom: 1px solid #e5e7eb;
      background: #ffffff;
    }

    .summary-item:last-child {
      border-bottom: none;
    }

    .summary-item--total {
      background: #f9fafb;
      font-weight: 600;
    }

    .summary-item .label {
      color: #6b7280;
      font-size: 0.875rem;
      font-weight: 500;
    }

    .summary-item .value {
      color: #111827;
      font-weight: 600;
      text-align: right;
    }

    .summary-item .value.highlight {
      color: #059669;
      font-size: 1.25rem;
    }

    .email-address {
      font-size: 1rem;
      color: #2563eb;
      font-weight: 600;
      background: #eff6ff;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      text-align: center;
    }

    .steps-list {
      margin: 0;
      padding-left: 1.5rem;
      color: #374151;
      line-height: 1.8;
    }

    .steps-list li {
      margin-bottom: 0.75rem;
    }

    .steps-list strong {
      color: #111827;
    }

    .important-note {
      display: flex;
      gap: 0.75rem;
      background: #fef3c7;
      border-left: 4px solid #f59e0b;
      padding: 1rem;
      border-radius: 8px;
      align-items: flex-start;
    }

    .warning-icon {
      flex-shrink: 0;
      color: #d97706;
    }

    .important-note p {
      margin: 0;
      color: #78350f;
      font-size: 0.875rem;
      line-height: 1.6;
    }

    .modal-footer {
      padding: 1.25rem 1.5rem 1.5rem;
      border-top: 1px solid #e5e7eb;
      display: flex;
      flex-direction: column;
      gap: 0.625rem;
      background: #fafbfc;
    }

    .btn--large {
      padding: 0.875rem 1.5rem;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .btn--primary {
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
      color: white;
      box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    }

    .btn--primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
    }

    .btn--secondary {
      background: #f3f4f6;
      color: #374151;
    }

    .btn--secondary:hover {
      background: #e5e7eb;
    }

    /* Mobile Responsive */
    @media (max-width: 640px) {
      .modal-overlay {
        padding: 0;
        align-items: flex-end;
      }

      .modal-container {
        max-width: 100%;
        max-height: 100vh;
        border-radius: 12px 12px 0 0;
        margin: 0;
      }

      .modal-header {
        padding: 1.25rem 1rem 1rem;
      }

      .modal-body {
        padding: 1rem;
      }

      .modal-footer {
        padding: 1rem;
        position: sticky;
        bottom: 0;
      }

      .success-icon {
        width: 56px;
        height: 56px;
        margin-bottom: 0.75rem;
      }

      .success-icon svg {
        width: 36px;
        height: 36px;
      }

      .modal-title {
        font-size: 1.375rem;
        margin-bottom: 0.375rem;
      }

      .modal-subtitle {
        font-size: 0.875rem;
      }

      .modal-close {
        width: 32px;
        height: 32px;
        top: 0.75rem;
        right: 0.75rem;
        font-size: 20px;
      }

      .confirmation-reference {
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        border-left-width: 3px;
      }

      .reference-content {
        font-size: 0.875rem;
        gap: 0.5rem;
      }

      .reference-number {
        font-size: 0.875rem;
        letter-spacing: 0.5px;
      }

      .section-title {
        font-size: 0.9375rem;
        margin-bottom: 0.625rem;
      }

      .summary-grid {
        font-size: 0.875rem;
      }

      .summary-item {
        padding: 0.625rem 0.75rem;
      }

      .summary-item .value.highlight {
        font-size: 1.125rem;
      }

      /* Compact step items for mobile */
      .steps-grid {
        gap: 0;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 0.75rem;
      }

      .step-item {
        padding: 0.375rem 0;
        border: none;
        background: transparent;
        gap: 0.5rem;
      }

      .step-item:hover {
        background: transparent;
        border-color: transparent;
      }

      .step-item:not(:last-child) {
        border-bottom: 1px dashed #e5e7eb;
      }

      .step-icon {
        font-size: 1.125rem;
      }

      .step-content {
        gap: 0.125rem;
      }

      .step-content strong {
        font-size: 0.8125rem;
      }

      .step-detail {
        font-size: 0.75rem;
        line-height: 1.3;
      }

      .btn--large {
        padding: 0.75rem 1.25rem;
        font-size: 0.9375rem;
      }

      .btn--secondary {
        padding: 0.625rem 1rem;
        font-size: 0.875rem;
      }

      .booking-summary,
      .next-steps {
        margin-bottom: 1rem;
      }
    }

    /* Payment Trust Section Styles */
    .payment-trust-section {
      background: linear-gradient(135deg, #f8fafb 0%, #f3f4f6 100%);
      border-radius: 8px;
      padding: 1rem;
      margin: 0 1.5rem 1rem;
      border: 1px solid #e5e7eb;
    }

    .accepted-payments {
      margin-bottom: 1rem;
      text-align: center;
    }

    .payment-label {
      font-size: 0.75rem;
      text-transform: uppercase;
      color: #6b7280;
      letter-spacing: 0.5px;
      margin-bottom: 0.625rem;
      font-weight: 600;
    }

    .payment-logos {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 0.875rem;
      flex-wrap: wrap;
    }

    .payment-logo {
      height: 28px;
      width: auto;
      filter: grayscale(20%);
      opacity: 0.9;
      transition: all 0.2s ease;
    }

    .payment-logo:hover {
      filter: grayscale(0%);
      opacity: 1;
      transform: translateY(-2px);
    }

    .security-badges {
      text-align: center;
      padding-top: 0.875rem;
      border-top: 1px dashed #e5e7eb;
    }

    .badge-row {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-bottom: 0.5rem;
    }

    .security-badge {
      height: 32px;
      width: auto;
    }

    .security-text {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.375rem;
      font-size: 0.75rem;
      color: #059669;
      font-weight: 500;
    }

    .lock-icon {
      color: #059669;
    }

    /* Enhanced Payment Button */
    .btn--payment {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      background: linear-gradient(135deg, #059669 0%, #047857 100%);
      box-shadow: 0 4px 14px rgba(5, 150, 105, 0.3);
      transition: all 0.3s ease;
    }

    .btn--payment:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4);
    }

    .btn-secure-icon {
      display: flex;
      align-items: center;
      padding: 0.125rem 0.375rem;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 4px;
    }

    /* Mobile Optimization for Payment Trust */
    @media (max-width: 640px) {
      .payment-trust-section {
        margin: 0 1rem 0.75rem;
        padding: 0.75rem;
      }

      .payment-logos {
        gap: 0.625rem;
      }

      .payment-logo {
        height: 22px;
      }

      .security-badge {
        height: 24px;
      }

      .badge-row {
        gap: 0.5rem;
      }

      .security-text {
        font-size: 0.6875rem;
      }
    }

/* Fix nav positioning for tour details page - no hero image */
.nav {
  position: relative;
  background: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}
.nav .nav__logo-text {
  color: var(--color-primary) !important;
  text-shadow: none;
}
.nav .nav__menu a {
  color: var(--color-text) !important;
  text-shadow: none;
}

/* ============================================ */
/* MODERN MINIMALIST MODAL DESIGN - 2025       */
/* ============================================ */

/* Glassmorphic Overlay */
.modal-overlay--glassmorphic {
  background: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* Minimal Modal Container */
.modal-container--minimal {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 20px;
  box-shadow:
    0 8px 32px rgba(0, 0, 0, 0.08),
    0 2px 8px rgba(0, 0, 0, 0.04);
  max-width: 420px;
  width: 100%;
  padding: 0;
  animation: slideUp 0.3s ease;
  position: relative;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.97);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* Minimal Header */
.modal-header-minimal {
  padding: 32px 32px 24px;
  text-align: center;
  position: relative;
}

.modal-close-minimal {
  position: absolute;
  top: 20px;
  right: 20px;
  width: 32px;
  height: 32px;
  border: none;
  background: rgba(0, 0, 0, 0.04);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #6B7280;
}

.modal-close-minimal:hover {
  background: rgba(0, 0, 0, 0.08);
  transform: rotate(90deg);
}

/* Success Animation */
.success-animation {
  margin: 0 auto 20px;
  width: 56px;
  height: 56px;
}

.checkmark-svg {
  width: 56px;
  height: 56px;
  animation: scaleIn 0.3s ease 0.1s both;
}

@keyframes scaleIn {
  from { transform: scale(0); }
  to { transform: scale(1); }
}

.checkmark-circle {
  stroke-dasharray: 166;
  stroke-dashoffset: 166;
  animation: strokeCircle 0.6s ease forwards;
}

.checkmark-check {
  stroke-dasharray: 48;
  stroke-dashoffset: 48;
  animation: strokeCheck 0.3s ease 0.5s forwards;
}

@keyframes strokeCircle {
  to { stroke-dashoffset: 0; }
}

@keyframes strokeCheck {
  to { stroke-dashoffset: 0; }
}

/* Minimal Typography */
.modal-title-minimal {
  font-size: 20px;
  font-weight: 600;
  color: #111827;
  margin: 0 0 8px;
  letter-spacing: -0.02em;
}

.modal-reference-minimal {
  font-size: 13px;
  color: #6B7280;
  margin: 0;
  font-family: 'SF Mono', Monaco, monospace;
}

/* Minimal Body */
.modal-body-minimal {
  padding: 0 32px 24px;
}

/* Booking Info Card */
.booking-info-card {
  background: rgba(248, 250, 251, 0.5);
  border: 1px solid rgba(0, 0, 0, 0.04);
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 20px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.04);
}

.info-row:last-child {
  border-bottom: none;
  padding-top: 12px;
  margin-top: 4px;
}

.info-row--total {
  border-top: 1px solid rgba(0, 0, 0, 0.08);
}

.info-label {
  font-size: 13px;
  color: #6B7280;
  font-weight: 400;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.info-value {
  font-size: 14px;
  color: #111827;
  font-weight: 500;
}

.info-value--price {
  font-size: 18px;
  font-weight: 600;
  color: #059669;
}

/* Minimal Next Steps */
.next-steps-minimal {
  text-align: center;
  padding: 0 20px;
}

.next-step-text {
  font-size: 13px;
  color: #6B7280;
  margin: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.next-step-text span {
  color: #111827;
  font-weight: 500;
}

/* Minimal Footer */
.modal-footer-minimal {
  padding: 24px 32px 32px;
  border-top: 1px solid rgba(0, 0, 0, 0.04);
}

/* Minimal Primary Button */
.btn-minimal-primary {
  width: 100%;
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  border: none;
  border-radius: 10px;
  color: white;
  font-weight: 500;
  padding: 14px 24px;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 8px rgba(5, 150, 105, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  letter-spacing: -0.01em;
}

.btn-minimal-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}

.btn-minimal-primary:active {
  transform: translateY(0);
}

/* Payment Options in Modal */
.modal-payment-options {
  padding: 0 32px 20px;
  border-top: 1px solid rgba(0, 0, 0, 0.04);
  margin-top: 20px;
}

.payment-options-title {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 16px;
  text-align: center;
}

.payment-option-minimal {
  display: block;
  margin-bottom: 12px;
  cursor: pointer;
}

.payment-option-minimal input[type="radio"] {
  position: absolute;
  opacity: 0;
}

.option-content-minimal {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  background: #F9FAFB;
  border: 2px solid #E5E7EB;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.payment-option-minimal:hover .option-content-minimal {
  border-color: #D1D5DB;
  background: #F3F4F6;
}

.payment-option-minimal input:checked + .option-content-minimal {
  border-color: #059669;
  background: #F0FDF4;
}

.payment-option-minimal.recommended input:checked + .option-content-minimal {
  border-width: 2px;
  box-shadow: 0 0 0 1px #059669;
}

.option-header-minimal {
  display: flex;
  align-items: center;
  gap: 8px;
}

.option-header-minimal strong {
  font-size: 13px;
  color: #111827;
}

.recommended-badge-minimal {
  background: #059669;
  color: white;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 9px;
  font-weight: 600;
  letter-spacing: 0.3px;
}

.discount-badge-minimal {
  background: #DC2626;
  color: white;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 9px;
  font-weight: 600;
  letter-spacing: 0.3px;
}

.option-details-minimal {
  text-align: right;
}

.deposit-amount, .full-amount {
  font-size: 16px;
  font-weight: 600;
  color: #111827;
  display: block;
}

.deposit-text, .full-text {
  font-size: 11px;
  color: #6B7280;
}

/* Minimal Trust Indicators */
.trust-minimal {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid rgba(0, 0, 0, 0.04);
}

.trust-icons {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.trust-text {
  font-size: 11px;
  color: #9CA3AF;
  letter-spacing: 0.3px;
}

.payment-icons-minimal {
  display: flex;
  gap: 6px;
  margin-left: 8px;
}

.payment-icon-minimal {
  height: 16px;
  width: auto;
  opacity: 0.3;
  filter: grayscale(100%);
}

/* Mobile Optimizations */
@media (max-width: 640px) {
  .modal-container--minimal {
    border-radius: 16px;
    margin: 10px;
    max-width: calc(100vw - 40px);
  }

  .modal-header-minimal {
    padding: 24px 24px 20px;
  }

  .modal-body-minimal {
    padding: 0 24px 20px;
  }

  .modal-footer-minimal {
    padding: 20px 24px 24px;
  }

  .booking-info-card {
    padding: 16px;
  }

  .info-row {
    padding: 6px 0;
  }

  .modal-title-minimal {
    font-size: 18px;
  }

  .info-value--price {
    font-size: 16px;
  }

  .btn-minimal-primary {
    padding: 12px 20px;
    font-size: 14px;
  }
}

/* Removed dark mode - Always use light minimalistic design */

/* Modern Submit Button for Booking Form */
.btn--submit {
  background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%) !important;
  color: #FFFFFF !important;
  font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", sans-serif !important;
  font-size: 16px !important;
  font-weight: 600 !important;
  letter-spacing: 0.02em !important;
  padding: 14px 28px !important;
  border: none !important;
  border-radius: 10px !important;
  cursor: pointer !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  box-shadow: 0 3px 12px rgba(102, 126, 234, 0.3) !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 8px !important;
  position: relative !important;
  overflow: hidden !important;
  text-transform: none !important;
}

.btn--submit:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4) !important;
  background: linear-gradient(135deg, #5a67d8 0%, #6b4299 100%) !important;
}

.btn--submit:active {
  transform: translateY(0) !important;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
}

.btn--submit:disabled {
  opacity: 0.6 !important;
  cursor: not-allowed !important;
  transform: none !important;
}

.btn--submit .spinner {
  display: none;
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.btn--submit.loading .spinner {
  display: inline-block;
}

.btn--submit.loading .btn__text {
  margin-right: 8px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.btn--block {
  width: 100% !important;
  display: flex !important;
}

/* Inquiry Button Modern Style */
.btn--primary.btn--large {
  background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%) !important;
  color: #FFFFFF !important;
  font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", sans-serif !important;
  font-size: 16px !important;
  font-weight: 600 !important;
  padding: 14px 28px !important;
  border-radius: 10px !important;
  box-shadow: 0 3px 12px rgba(102, 126, 234, 0.3) !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.btn--primary.btn--large:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4) !important;
  background: linear-gradient(135deg, #5a67d8 0%, #6b4299 100%) !important;
}

/* Share and Favorite Button Styles */
.btn-icon {
  background: transparent;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
}

.btn-icon:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
  transform: translateY(-1px);
}

.btn-icon:active {
  transform: translateY(0);
}

.btn-icon svg {
  transition: all 0.2s ease;
}

.btn-share:hover svg {
  stroke: #667EEA;
}

.btn-favorite:hover svg {
  stroke: #FF6B6B;
}

.btn-favorite.is-favorited svg {
  fill: #FF6B6B;
  stroke: #FF6B6B;
}

/* Toast notification animations */
@keyframes slideUpNotification {
  from {
    opacity: 0;
    transform: translate(-50%, 20px);
  }
  to {
    opacity: 1;
    transform: translate(-50%, 0);
  }
}

@keyframes slideDownNotification {
  from {
    opacity: 1;
    transform: translate(-50%, 0);
  }
  to {
    opacity: 0;
    transform: translate(-50%, 20px);
  }
}

/* Payment Security Badges */
.payment-security-badges {
  display: flex;
  gap: 12px;
  margin: 16px 0;
  padding: 12px;
  background: linear-gradient(to right, #f8fafc, #f1f5f9);
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.security-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  flex: 1;
}

.security-badge svg {
  flex-shrink: 0;
  opacity: 0.6;
}

.security-badge span {
  font-size: 11px;
  color: #64748b;
  font-weight: 500;
  white-space: nowrap;
}

/* SSL Notice */
.ssl-notice {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 6px;
  margin-bottom: 16px;
}

.ssl-notice svg {
  flex-shrink: 0;
}

.ssl-notice span {
  font-size: 12px;
  color: #059669;
  font-weight: 500;
}

/* Mobile Trust Indicators */
.mobile-cta__trust {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  background: rgba(16, 185, 129, 0.08);
  border-radius: 6px;
  margin-left: auto;
}

.mobile-cta__trust span {
  font-size: 11px;
  color: #059669;
  font-weight: 500;
  white-space: nowrap;
}

/* Update mobile CTA container for trust badge */
@media (max-width: 767px) {
  .mobile-cta__container {
    display: grid !important;
    grid-template-columns: auto 1fr auto !important;
    grid-template-rows: auto auto !important;
    align-items: center !important;
    gap: 8px 12px !important;
    padding: 10px 14px !important;
  }

  .mobile-cta__price {
    grid-column: 1;
    grid-row: 1;
  }

  .mobile-cta__actions {
    grid-column: 2 / 4;
    grid-row: 1;
    justify-self: end;
  }

  .mobile-cta__trust {
    grid-column: 1 / 4;
    grid-row: 2;
    justify-self: center;
    margin: 0;
  }
}

/* Hover effects for trust elements */
.payment-security-badges:hover {
  background: linear-gradient(to right, #ffffff, #f8fafc);
  transition: background 0.3s ease;
}

.ssl-notice:hover {
  background: #dcfce7;
  transition: background 0.3s ease;
}

/* Trust badge animations */
@keyframes trustPulse {
  0%, 100% { opacity: 0.6; }
  50% { opacity: 1; }
}

.security-badge svg,
.ssl-notice svg {
  animation: trustPulse 3s ease-in-out infinite;
}

/* Payment Methods */
.payment-methods {
  margin: 16px 0;
  padding: 12px;
  background: #fafafa;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

.payment-methods__label {
  display: block;
  font-size: 11px;
  color: #6b7280;
  font-weight: 500;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.payment-methods__logos {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  align-items: center;
}

.payment-methods__logos svg {
  opacity: 0.8;
  transition: opacity 0.2s ease;
}

.payment-methods__logos svg:hover {
  opacity: 1;
}

/* Responsive adjustments */
@media (max-width: 480px) {
  .payment-security-badges {
    flex-direction: column;
    gap: 8px;
  }

  .security-badge {
    width: 100%;
  }

  .payment-methods__logos {
    justify-content: center;
  }
}

/* Scroll to Top Button */
.scroll-to-top {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
  color: white;
  border: none;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  opacity: 0;
  visibility: hidden;
  transform: translateY(20px);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
  z-index: 95;
}

.scroll-to-top.visible {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.scroll-to-top:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
  background: linear-gradient(135deg, #5a67d8 0%, #6b4299 100%);
}

.scroll-to-top:active {
  transform: translateY(0);
}

.scroll-to-top svg {
  width: 24px;
  height: 24px;
  stroke-width: 2.5;
}

/* Mobile positioning - above mobile CTA bar */
@media (max-width: 767px) {
  .scroll-to-top {
    bottom: 100px; /* Above mobile CTA bar which is 60px height */
    right: 20px;
    width: 44px;
    height: 44px;
  }

  .scroll-to-top svg {
    width: 20px;
    height: 20px;
  }

  /* When WhatsApp button is present, stack vertically */
  .whatsapp-float ~ .scroll-to-top {
    bottom: 160px; /* Stack above WhatsApp button */
  }
}

/* Desktop positioning - consider WhatsApp button */
@media (min-width: 768px) {
  .scroll-to-top {
    bottom: 30px;
    right: 100px; /* Move left to avoid WhatsApp button at right: 30px */
  }

  /* Alternative: Stack vertically if preferred */
  .whatsapp-float ~ .scroll-to-top {
    bottom: 90px;
    right: 30px; /* Align with WhatsApp button */
  }
}

/* Reduced motion preference */
@media (prefers-reduced-motion: reduce) {
  .scroll-to-top {
    transition: opacity 0.2s ease;
  }

  .scroll-to-top.visible {
    transform: none;
  }

  .scroll-to-top:hover {
    transform: none;
  }
}

/* Pulse animation for attention */
@keyframes scrollTopPulse {
  0% {
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
  }
  50% {
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.5);
  }
  100% {
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
  }
}

.scroll-to-top.pulse {
  animation: scrollTopPulse 2s ease-in-out infinite;
}

/* ========================================
   MOBILE BOOKING FORM OPTIMIZATION
   Compact, Modern, Minimalistic Design
   ======================================== */

@media (max-width: 767px) {

  /* Reduce overall padding and spacing */
  .booking-sidebar {
    padding: 12px !important; /* Reduced from 16-20px */
  }

  .booking-card {
    padding: 14px !important; /* Reduced from 20px */
    border-radius: 12px !important;
  }

  /* Compact Price Header */
  .booking-card__header {
    padding: 10px 12px !important; /* Reduced from 16px */
    margin-bottom: 12px !important; /* Reduced from 20px */
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 8px;
  }

  .booking-price {
    display: flex;
    align-items: baseline;
    gap: 4px !important; /* Tighter spacing */
  }

  .price-label {
    font-size: 11px !important; /* Smaller label */
    opacity: 0.7;
  }

  .price-amount {
    font-size: 24px !important; /* Reduced from 28-32px */
    font-weight: 600 !important;
  }

  .price-unit {
    font-size: 12px !important; /* Smaller unit */
    opacity: 0.8;
  }

  /* Ultra-Compact Form Groups */
  .booking-form .form-group {
    margin-bottom: 10px !important; /* Reduced from 16-20px */
  }

  .form-label {
    font-size: 11px !important; /* Smaller labels */
    font-weight: 600 !important;
    color: #64748b !important;
    margin-bottom: 4px !important; /* Minimal gap */
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  /* Compact Input Fields */
  .booking-form input[type="date"],
  .booking-form input[type="text"],
  .booking-form input[type="email"],
  .booking-form input[type="tel"],
  .booking-form select,
  .booking-form textarea {
    height: 44px !important; /* Minimum touch target */
    padding: 10px 12px !important; /* Reduced padding */
    font-size: 14px !important; /* Slightly smaller text */
    border-radius: 8px !important;
    border: 1px solid #e2e8f0 !important;
    background: #fafafa !important;
  }

  .booking-form textarea {
    min-height: 60px !important; /* Reduced from 80-100px */
    resize: none !important;
  }

  /* Inline Guest Counter (saves vertical space) */
  .guest-counter {
    display: grid !important;
    grid-template-columns: 1fr auto !important;
    align-items: center !important;
    gap: 8px !important;
    padding: 0 !important;
  }

  .guest-counter__controls {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    background: #f3f4f6 !important;
    border-radius: 6px !important;
    padding: 2px !important;
  }

  .guest-counter__btn {
    width: 32px !important;
    height: 32px !important; /* Smaller buttons */
    border-radius: 4px !important;
    font-size: 18px !important;
  }

  .guest-counter__value {
    min-width: 24px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
  }

  /* Compact Price Breakdown */
  .price-breakdown {
    padding: 10px !important; /* Reduced from 16px */
    margin: 12px 0 !important; /* Reduced margins */
    background: #f8fafc !important;
    border-radius: 8px !important;
    border: 1px solid #e2e8f0 !important;
  }

  .breakdown-title {
    font-size: 12px !important; /* Smaller title */
    font-weight: 600 !important;
    margin-bottom: 8px !important;
    color: #475569 !important;
  }

  .breakdown-item {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    padding: 6px 0 !important; /* Tighter spacing */
    font-size: 13px !important; /* Smaller text */
  }

  .breakdown-item--total {
    padding-top: 8px !important;
    margin-top: 8px !important;
    border-top: 1px solid #e2e8f0 !important;
    font-weight: 600 !important;
    font-size: 14px !important;
  }

  /* Compact Section Headers */
  .form-section {
    margin-top: 14px !important; /* Reduced from 24px */
    padding-top: 14px !important;
    border-top: 1px solid #f1f5f9 !important;
  }

  .form-section__title {
    font-size: 13px !important; /* Smaller section titles */
    font-weight: 600 !important;
    margin-bottom: 10px !important;
    color: #334155 !important;
  }

  /* Streamlined Payment Options */
  .payment-options {
    display: flex !important;
    gap: 8px !important; /* Tighter gap */
    margin: 12px 0 !important;
  }

  .payment-option {
    flex: 1 !important;
    padding: 10px 8px !important; /* Compact padding */
    border-radius: 8px !important;
    font-size: 12px !important; /* Smaller text */
    text-align: center !important;
    border: 2px solid #e2e8f0 !important;
    transition: all 0.2s ease !important;
  }

  .payment-option.selected {
    background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%) !important;
    color: white !important;
    border-color: transparent !important;
  }

  .payment-option__price {
    font-size: 16px !important; /* Reduced from 20px */
    font-weight: 600 !important;
    display: block !important;
  }

  .payment-option__label {
    font-size: 11px !important;
    opacity: 0.9 !important;
    margin-top: 2px !important;
  }

  /* Compact Trust Badges */
  .trust-badges {
    display: flex !important;
    justify-content: space-between !important;
    padding: 8px !important;
    margin: 10px 0 !important;
    background: #fafafa !important;
    border-radius: 6px !important;
  }

  .badge-item {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    flex: 1 !important;
  }

  .badge-item svg {
    width: 16px !important;
    height: 16px !important;
    opacity: 0.6 !important;
    margin-bottom: 2px !important;
  }

  .badge-item span {
    font-size: 9px !important; /* Very small text */
    color: #64748b !important;
    text-align: center !important;
    line-height: 1.2 !important;
  }

  /* Compact Payment Security Badges */
  .payment-security-badges {
    display: grid !important;
    grid-template-columns: repeat(3, 1fr) !important;
    gap: 6px !important;
    padding: 8px !important;
    margin: 8px 0 !important;
  }

  .security-badge {
    flex-direction: column !important;
    align-items: center !important;
    text-align: center !important;
  }

  .security-badge svg {
    width: 20px !important;
    height: 20px !important;
    margin-bottom: 2px !important;
  }

  .security-badge span {
    font-size: 9px !important;
    line-height: 1.1 !important;
  }

  /* Inline SSL Notice */
  .ssl-notice {
    display: inline-flex !important;
    padding: 6px 10px !important;
    margin: 8px 0 !important;
    font-size: 11px !important;
  }

  .ssl-notice svg {
    width: 10px !important;
    height: 12px !important;
    margin-right: 4px !important;
  }

  /* Compact Payment Methods */
  .payment-methods {
    padding: 8px !important;
    margin: 8px 0 !important;
  }

  .payment-methods__label {
    font-size: 10px !important;
    margin-bottom: 4px !important;
  }

  .payment-methods__logos svg {
    width: 28px !important;
    height: 18px !important;
  }

  /* Streamlined Booking Button */
  .btn--submit,
  .btn--primary {
    height: 44px !important; /* Minimum touch target */
    padding: 0 20px !important;
    font-size: 15px !important;
    font-weight: 600 !important;
    border-radius: 10px !important;
  }

  /* Compact Booking Clarification */
  .booking-clarification {
    padding: 10px !important;
    margin: 10px 0 !important;
    background: #f0fdf4 !important;
    border-radius: 6px !important;
    border: 1px solid #bbf7d0 !important;
  }

  .clarification-text {
    font-size: 12px !important;
    line-height: 1.4 !important;
    margin-bottom: 4px !important;
  }

  .clarification-note {
    font-size: 11px !important;
    opacity: 0.8 !important;
  }

  /* Compact Benefits List */
  .booking-benefits {
    padding: 10px !important;
    margin: 10px 0 !important;
  }

  .benefits-title {
    font-size: 13px !important;
    margin-bottom: 8px !important;
    font-weight: 600 !important;
  }

  .benefits-list {
    padding: 0 !important;
    margin: 0 !important;
  }

  .benefit-item {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 4px 0 !important;
    font-size: 12px !important;
  }

  .benefit-item svg {
    width: 14px !important;
    height: 14px !important;
    flex-shrink: 0 !important;
    color: #10b981 !important;
  }

  /* Compact WhatsApp Contact */
  .booking-contact {
    padding: 10px !important;
    margin: 10px 0 !important;
  }

  .contact-text {
    font-size: 12px !important;
    margin-bottom: 6px !important;
  }

  .btn--whatsapp {
    height: 40px !important;
    font-size: 13px !important;
    padding: 0 16px !important;
  }

  /* Remove unnecessary margins and paddings */
  .booking-form > *:first-child {
    margin-top: 0 !important;
  }

  .booking-form > *:last-child {
    margin-bottom: 0 !important;
  }

  /* Floating Labels for Ultra-Compact Design (Optional) */
  .form-group--floating {
    position: relative !important;
    margin-bottom: 8px !important;
  }

  .form-group--floating .form-label {
    position: absolute !important;
    top: 12px !important;
    left: 12px !important;
    font-size: 14px !important;
    transition: all 0.2s ease !important;
    pointer-events: none !important;
    background: white !important;
    padding: 0 4px !important;
  }

  .form-group--floating input:focus + .form-label,
  .form-group--floating input:not(:placeholder-shown) + .form-label {
    top: -6px !important;
    left: 8px !important;
    font-size: 10px !important;
    color: #667EEA !important;
  }

  /* Inline Form Hints */
  .form-hint {
    font-size: 10px !important;
    color: #94a3b8 !important;
    margin-top: 2px !important;
    line-height: 1.3 !important;
  }

  /* Compact Error Messages */
  .form-error {
    font-size: 11px !important;
    color: #ef4444 !important;
    margin-top: 2px !important;
  }
}

/* Additional Ultra-Compact Mode for Small Screens < 360px */
@media (max-width: 359px) {
  .booking-card {
    padding: 10px !important;
  }

  .price-amount {
    font-size: 22px !important;
  }

  .booking-form input,
  .booking-form select {
    height: 42px !important;
    font-size: 13px !important;
  }

  .btn--submit {
    height: 42px !important;
    font-size: 14px !important;
  }
}
</style>
@endpush


@push('scripts')
<script src="{{ asset('js/htmx.min.js') }}"></script>
<script src="{{ asset('js/payment-integration.js') }}"></script>
<script src="{{ asset('tour-details.js') }}"></script>
<script src="{{ asset('js/booking-form.js') }}"></script>
<script src="{{ asset('js/gallery-lightbox.js') }}"></script>
<script>
// Hamburger menu toggle functionality
document.addEventListener('DOMContentLoaded', function() {
  const navToggle = document.getElementById('navToggle');
  const navMenu = document.getElementById('navMenu');

  if (navToggle && navMenu) {
    navToggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      const isExpanded = navToggle.getAttribute('aria-expanded') === 'true';

      // Toggle menu visibility
      navToggle.setAttribute('aria-expanded', !isExpanded);
      navMenu.classList.toggle('active');
      navMenu.classList.toggle('show');
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
      if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
        navToggle.setAttribute('aria-expanded', 'false');
        navMenu.classList.remove('active');
        navMenu.classList.remove('show');
      }
    });

    // Close menu when clicking on a menu item
    navMenu.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', function() {
        navToggle.setAttribute('aria-expanded', 'false');
        navMenu.classList.remove('active');
        navMenu.classList.remove('show');
      });
    });
  }
});

// Share button functionality
document.addEventListener('click', function(e) {
  if (e.target.closest('.btn-share')) {
    const button = e.target.closest('.btn-share');
    const tourTitle = button.dataset.tourTitle;
    const tourUrl = button.dataset.tourUrl || window.location.href;

    // Check if Web Share API is available (mobile browsers)
    if (navigator.share) {
      navigator.share({
        title: tourTitle,
        text: `Check out this amazing tour: ${tourTitle}`,
        url: tourUrl
      }).catch(err => {
        // Fallback to copy link if share fails
        copyToClipboard(tourUrl);
      });
    } else {
      // Desktop fallback - copy link to clipboard
      copyToClipboard(tourUrl);
    }
  }
});

// Favorite/Wishlist button functionality
document.addEventListener('click', function(e) {
  if (e.target.closest('.btn-favorite')) {
    const button = e.target.closest('.btn-favorite');
    const tourId = button.dataset.tourId;
    const tourTitle = button.dataset.tourTitle;
    const svg = button.querySelector('svg');

    // Toggle favorite state
    const isFavorited = button.classList.contains('is-favorited');

    if (isFavorited) {
      // Remove from favorites
      button.classList.remove('is-favorited');
      svg.setAttribute('fill', 'none');
      removeFromWishlist(tourId);
      showNotification(`${tourTitle} removed from wishlist`);
    } else {
      // Add to favorites
      button.classList.add('is-favorited');
      svg.setAttribute('fill', '#FF6B6B');
      addToWishlist(tourId, tourTitle);
      showNotification(`${tourTitle} added to wishlist!`);
    }
  }
});

// Helper function to copy to clipboard
function copyToClipboard(text) {
  const textArea = document.createElement('textarea');
  textArea.value = text;
  textArea.style.position = 'fixed';
  textArea.style.left = '-999999px';
  document.body.appendChild(textArea);
  textArea.select();

  try {
    document.execCommand('copy');
    showNotification('Link copied to clipboard!');
  } catch (err) {
    showNotification('Failed to copy link');
  }

  document.body.removeChild(textArea);
}

// Helper function to show notifications
function showNotification(message) {
  // Create notification element
  const notification = document.createElement('div');
  notification.className = 'toast-notification';
  notification.textContent = message;
  notification.style.cssText = `
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    z-index: 10000;
    font-size: 14px;
    animation: slideUpNotification 0.3s ease;
  `;

  document.body.appendChild(notification);

  // Remove after 3 seconds
  setTimeout(() => {
    notification.style.animation = 'slideDownNotification 0.3s ease';
    setTimeout(() => {
      document.body.removeChild(notification);
    }, 300);
  }, 3000);
}

// Wishlist management functions (using localStorage for now)
function addToWishlist(tourId, tourTitle) {
  let wishlist = JSON.parse(localStorage.getItem('tour_wishlist') || '[]');
  if (!wishlist.find(item => item.id === tourId)) {
    wishlist.push({ id: tourId, title: tourTitle, date: new Date().toISOString() });
    localStorage.setItem('tour_wishlist', JSON.stringify(wishlist));
  }
}

function removeFromWishlist(tourId) {
  let wishlist = JSON.parse(localStorage.getItem('tour_wishlist') || '[]');
  wishlist = wishlist.filter(item => item.id !== tourId);
  localStorage.setItem('tour_wishlist', JSON.stringify(wishlist));
}

// Check if current tour is in wishlist on page load
document.addEventListener('DOMContentLoaded', function() {
  const favoriteBtn = document.querySelector('.btn-favorite');
  if (favoriteBtn) {
    const tourId = favoriteBtn.dataset.tourId;
    const wishlist = JSON.parse(localStorage.getItem('tour_wishlist') || '[]');

    if (wishlist.find(item => item.id === tourId)) {
      favoriteBtn.classList.add('is-favorited');
      favoriteBtn.querySelector('svg').setAttribute('fill', '#FF6B6B');
    }
  }
});

// Scroll to Top Button Functionality
(function() {
  const scrollToTopBtn = document.getElementById('scroll-to-top');
  if (!scrollToTopBtn) return;

  let isScrolling = false;
  const SCROLL_THRESHOLD = 800; // Show button after 800px scroll

  // Check scroll position and show/hide button
  function checkScroll() {
    const scrollY = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollY > SCROLL_THRESHOLD) {
      scrollToTopBtn.classList.add('visible');
    } else {
      scrollToTopBtn.classList.remove('visible');
      scrollToTopBtn.classList.remove('pulse');
    }

    // Add pulse animation when near bottom
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;
    const scrolledToBottom = scrollY + windowHeight >= documentHeight - 100;

    if (scrolledToBottom && scrollY > SCROLL_THRESHOLD) {
      scrollToTopBtn.classList.add('pulse');
    } else {
      scrollToTopBtn.classList.remove('pulse');
    }
  }

  // Smooth scroll to top
  function scrollToTop() {
    if (isScrolling) return;
    isScrolling = true;

    const startPosition = window.pageYOffset;
    const duration = 600; // milliseconds
    const startTime = performance.now();

    function easeInOutCubic(t) {
      return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
    }

    function animateScroll(currentTime) {
      const elapsedTime = currentTime - startTime;
      const progress = Math.min(elapsedTime / duration, 1);
      const easing = easeInOutCubic(progress);
      const newPosition = startPosition * (1 - easing);

      window.scrollTo(0, newPosition);

      if (progress < 1) {
        requestAnimationFrame(animateScroll);
      } else {
        isScrolling = false;
        // Focus on header for accessibility
        const header = document.querySelector('.tour-header, .header, h1');
        if (header) header.focus({ preventScroll: true });
      }
    }

    requestAnimationFrame(animateScroll);
  }

  // Event listeners
  scrollToTopBtn.addEventListener('click', scrollToTop);

  // Keyboard support (Enter/Space)
  scrollToTopBtn.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      scrollToTop();
    }
  });

  // Throttled scroll event
  let scrollTimer = null;
  window.addEventListener('scroll', function() {
    if (scrollTimer !== null) {
      clearTimeout(scrollTimer);
    }
    scrollTimer = setTimeout(checkScroll, 150);
  });

  // Initial check
  checkScroll();

  // Alternative smooth scroll for browsers with native support
  if ('scrollBehavior' in document.documentElement.style) {
    scrollToTopBtn.addEventListener('click', function(e) {
      e.preventDefault();
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }
})();
</script>
<script src="{{ asset('js/tour-reviews.js') }}"></script>
@endpush
