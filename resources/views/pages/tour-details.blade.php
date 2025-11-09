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
{
  "@@context": "https://schema.org",
  "@@type": "TouristTrip",
  "name": "{{ $tour->name }}",
  "description": "{{ $tour->description }}",
  "image": "{{ $ogImage }}",
  "offers": {
    "@@type": "Offer",
    "price": "{{ $tour->price }}",
    "priceCurrency": "USD",
    "availability": "https://schema.org/InStock"
  },
  "provider": {
    "@@type": "TravelAgency",
    "name": "Jahongir Travel",
    "url": "{{ url('/') }}"
  }
}

  <!-- BOOKING CONFIRMATION MODALS -->
  <div id="booking-confirmation-modal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
      <div class="modal-header">
        <div class="success-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>
        <h2 class="modal-title">Booking Request Submitted!</h2>
        <p class="modal-subtitle">We've received your request and will get back to you soon</p>
        <button class="modal-close" aria-label="Close">&times;</button>
      </div>

      <div class="modal-body">
        <!-- Booking Reference -->
        <div class="confirmation-reference">
          <span class="label">Your Reference Number</span>
          <span class="reference-number" id="modal-reference">BK-2025-XXX</span>
          <p class="reference-note">Save this number for your records</p>
        </div>

        <!-- Booking Summary -->
        <div class="booking-summary">
          <h3 class="section-title">Booking Summary</h3>
          <div class="summary-grid">
            <div class="summary-item">
              <span class="label">Tour</span>
              <span class="value" id="modal-tour-name">...</span>
            </div>
            <div class="summary-item">
              <span class="label">Date</span>
              <span class="value" id="modal-date">...</span>
            </div>
            <div class="summary-item">
              <span class="label">Guests</span>
              <span class="value" id="modal-guests">...</span>
            </div>
            <div class="summary-item summary-item--total">
              <span class="label">Estimated Total</span>
              <span class="value highlight" id="modal-total">$200.00</span>
            </div>
          </div>
        </div>

        <!-- Customer Info -->
        <div class="customer-info">
          <h3 class="section-title">Confirmation Email Sent To</h3>
          <p class="email-address" id="modal-customer-email">customer@example.com</p>
        </div>

        <!-- Next Steps -->
        <div class="next-steps">
          <h3 class="section-title">What Happens Next?</h3>
          <ol class="steps-list">
            <li>We'll check availability for your requested dates</li>
            <li>You'll receive confirmation within <strong>24 hours</strong></li>
            <li>Check your email (<strong id="modal-customer-email-inline">...</strong>) for details</li>
            <li>No payment required until we confirm availability</li>
          </ol>
        </div>

        <!-- Important Note -->
        <div class="important-note">
          <svg class="warning-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
          </svg>
          <p><strong>Please check your spam folder</strong> if you don't receive our email within an hour.</p>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn--primary btn--large" id="continue-browsing">
          Got It, Thanks!
        </button>
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

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/998915550808?text=Hi!%20I'm%20interested%20in%20learning%20more%20about%20your%20tours%20in%20Uzbekistan."

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('tour-details.css') }}">
<link rel="stylesheet" href="{{ asset('css/gallery-lightbox.css') }}">
<link rel="stylesheet" href="{{ asset('tour-details-gallery-addon.css') }}">
<link rel="stylesheet" href="{{ asset('css/tour-reviews.css') }}">
<style>
/* Booking Actions - Two-Button Choice */
.booking-actions{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem}
.action-btn{display:flex;align-items:center;gap:0.75rem;padding:1rem;border:2px solid #E3E3E3;border-radius:8px;background:#fff;cursor:pointer;transition:all 0.2s;min-height:80px;text-align:left;width:100%}
.action-btn:hover{border-color:#0D4C92;transform:translateY(-2px);box-shadow:0 4px 12px rgba(13,76,146,0.15)}
.action-btn:active{transform:translateY(0)}
.action-btn.active{border-color:#0D4C92;background:#F0F7FF;box-shadow:0 0 0 3px rgba(13,76,146,0.1)}
.action-btn i{font-size:1.5rem;color:#0D4C92;flex-shrink:0}
.action-btn--booking i{color:#4CAF50}
.action-btn--inquiry i{color:#FF9800}
.action-btn__content{display:flex;flex-direction:column;gap:0.25rem}
.action-btn__title{font-size:1rem;font-weight:600;color:#1E1E1E}
.action-btn__subtitle{font-size:0.875rem;color:#666;line-height:1.3}
@media (max-width:640px){.booking-actions{grid-template-columns:1fr;gap:0.75rem}}

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
</style>
@endpush

@section('content')
  <!-- =====================================================
       SECTION 2: TOUR HEADER INFO (Title, Rating, Meta, Tabs)
       ===================================================== -->
  <section class="tour-header"
           hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/hero"
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
           hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/gallery"
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
                   hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/overview"
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
                   hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/highlights"
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
                   hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/included-excluded"
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
                   hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/cancellation"
                   hx-trigger="load"
                   hx-swap="innerHTML">
            <div class="loading-spinner">Loading cancellation policy...</div>
          </section>

          <!-- Itinerary Section -->
          <section class="tour-itinerary" id="itinerary"
                   hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/itinerary"
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
                   hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/meeting-point"
                   hx-trigger="load"
                   hx-swap="innerHTML"
                   data-tour-slug="{{ $tour->slug }}">

            <!-- Loading Skeleton -->
            <h2 class="section-title">Meeting Point & Pickup</h2>
            <div class="skeleton skeleton--text" style="width: 95%; height: 16px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--text" style="width: 92%; height: 16px; margin-bottom: 1rem;"></div>
            <div class="skeleton skeleton--rect" style="width: 100%; height: 360px; margin-top: 2rem;"></div>
          </section>

          <!-- Know Before You Go Section -->
          <section class="tour-know-before" id="know-before"
                   hx-get="http://127.0.0.1:8000/partials/tours/5-day-silk-road-classic/requirements"
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
                   hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/faqs"
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

          <!-- Extra Services Section -->
          <section class="tour-extras" id="extras"
                   hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/extras"
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

          <!-- Customer Reviews Section -->
          <section class="tour-reviews" id="reviews"
                   hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/reviews"
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

        </main>

        <!-- RIGHT COLUMN: Booking Sidebar -->
        <aside class="booking-sidebar" data-sticky="true">

          <!-- Tour Data for JavaScript -->
          <script type="application/json" id="tour-data">
            {
              "id": "{{ $tour->slug }}",
              "name": "{{ $tour->name }}",
              "pricePerPerson": {{ $tour->price }},
              "currency": "USD",
              "maxGuests": {{ $tour->max_guests ?? 10 }},
              "minGuests": {{ $tour->min_guests ?? 1 }},
              "duration": "{{ $tour->duration }}"
            }
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
              <div class="booking-price">
                <span class="price-label">from</span>
                <span class="price-amount" data-base-price="{{ $tour->price }}">${{ number_format($tour->price, 2) }}</span>
                <span class="price-unit">/person</span>
              </div>
            </div>

            <!-- Price Breakdown -->
            <div class="price-breakdown" data-breakdown-visible="true">
              <h3 class="breakdown-title">Price Breakdown</h3>
              <div class="breakdown-items">
                <div class="breakdown-item">
                  <span class="breakdown-label">
                    <span class="breakdown-guests" data-guests="2">2 guests</span> ×
                    <span class="breakdown-unit-price" data-unit-price="{{ $tour->price }}">${{ number_format($tour->price, 2) }}</span>
                  </span>
                  <span class="breakdown-value" data-subtotal="{{ $tour->price * 2 }}">${{ number_format($tour->price * 2, 2) }}</span>
                </div>
                <div class="breakdown-item breakdown-item--total">
                  <span class="breakdown-label">Total</span>
                  <span class="breakdown-value breakdown-total" data-total="{{ $tour->price * 2 }}">${{ number_format($tour->price * 2, 2) }}</span>
                </div>
              </div>
              <p class="breakdown-note">Free cancellation up to 24 hours before the tour</p>
            </div>

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
                    <span class="btn__text" id="submit-text">Send Booking Request</span>
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
                <span>Free cancellation</span>
              </div>
            </div>

            <!-- Booking Clarification -->
            <div class="booking-clarification">
              <p class="clarification-text">
                <svg class="icon icon--clock" width="18" height="18" viewBox="0 0 18 18" fill="currentColor" aria-hidden="true"><path d="M9 0a9 9 0 100 18A9 9 0 009 0zm4 10H9a1 1 0 01-1-1V4a1 1 0 112 0v4h3a1 1 0 010 2z"/></svg>
                <strong>Confirmation within 24 hours</strong> — we'll verify availability and send you a confirmation.
              </p>
              <p class="clarification-note">
                No payment required now. Pay after confirmation.
              </p>
            </div>

            <!-- Request to Book Button -->
            <button type="button" class="btn btn--accent btn--block btn--large" id="request-booking">
              <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/></svg>
              Request to Book
            </button>

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
                  <span>Free cancellation (24h)</span>
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

            <!-- WhatsApp Contact -->
            <div class="booking-contact">
              <p class="contact-text">Questions? Contact us directly:</p>
              <a href="https://wa.me/998901234567" class="btn btn--whatsapp btn--block" target="_blank" rel="noopener noreferrer">
                <svg class="icon icon--whatsapp" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.05 0C4.5 0 0 4.48 0 10c0 1.77.46 3.43 1.27 4.87L.05 19.1l4.35-1.14A9.95 9.95 0 0010.05 20c5.52 0 10-4.48 10-10s-4.48-10-10-10zm5.89 14.21c-.24.68-1.41 1.28-1.95 1.35-.51.06-1.02.24-3.44-.74-3.11-1.25-5.11-4.39-5.27-4.59-.15-.2-1.24-1.65-1.24-3.15s.78-2.23 1.06-2.53c.28-.3.61-.38.81-.38.2 0 .41.01.59.01.19 0 .44-.07.69.52.25.61.86 2.1.93 2.25.08.15.13.33.03.53-.1.2-.15.33-.3.51-.15.18-.32.4-.46.54-.15.15-.31.31-.13.61.18.3.79 1.31 1.7 2.12 1.17 1.04 2.16 1.37 2.46 1.52.3.15.48.13.66-.08.18-.2.76-.89.96-1.19.2-.3.41-.25.69-.15.28.1 1.78.84 2.08.99.3.15.5.23.58.35.07.13.07.74-.17 1.42z"/></svg>
                WhatsApp: +998 90 123 45 67
              </a>
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
      <div class="mobile-cta__price">
        <span class="mobile-cta__amount" data-mobile-price="{{ $tour->price }}">${{ number_format($tour->price, 2) }}</span>
        <span class="mobile-cta__unit">per person</span>
      </div>
      <button type="button" class="btn btn--accent mobile-cta__button" data-scroll-to="booking-form" aria-label="Scroll to booking form">
        <svg class="icon icon--calendar-check" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6 2a2 2 0 00-2 2v1H2a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2h-2V4a2 2 0 00-2-2H6zm1 2h4v2H7V4zM2 9h14v8H2V9zm11.707 1.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
        Book Now
      </button>
    </div>
  </div>
@endsection

@push('scripts')
<script src="{{ asset('js/htmx.min.js') }}"></script>
<script src="{{ asset('tour-details.js') }}"></script>
<script src="{{ asset('js/booking-form.js') }}"></script>
<script src="{{ asset('js/gallery-lightbox.js') }}"></script>
<script src="{{ asset('js/tour-reviews.js') }}"></script>
@endpush
