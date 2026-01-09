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

{{-- Structured Data --}}
@section('structured_data')
{!! $structuredData ?? '{}' !!}
@endsection

@section('content')
  <!-- =====================================================
       WORKSHOP HERO SECTION
       ===================================================== -->
  <section class="workshop-hero">
    <div class="workshop-hero__background">
      @if($workshop->hero_image_url)
        <img src="{{ $workshop->hero_image_url }}" alt="{{ $workshop->title }}" class="workshop-hero__image">
      @else
        <div class="workshop-hero__placeholder"></div>
      @endif
      <div class="workshop-hero__overlay"></div>
    </div>
    <div class="container">
      <div class="workshop-hero__content">
        <nav class="workshop-breadcrumb" aria-label="Breadcrumb">
          <a href="{{ url('/') }}">Home</a>
          <span>/</span>
          <a href="{{ url('/workshops') }}">Workshops</a>
          <span>/</span>
          <span>{{ $workshop->title }}</span>
        </nav>
        <span class="workshop-hero__badge">{{ ucfirst($workshop->craft_type ?? 'Artisan Workshop') }}</span>
        <h1 class="workshop-hero__title">{{ $workshop->title }}</h1>
        @if($workshop->subtitle)
          <p class="workshop-hero__subtitle">{{ $workshop->subtitle }}</p>
        @endif
        <div class="workshop-hero__meta">
          @if($workshop->city)
            <span class="workshop-hero__location">
              <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
              </svg>
              {{ $workshop->city->name }}
            </span>
          @endif
          @if($workshop->duration_text)
            <span class="workshop-hero__duration">
              <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
              </svg>
              {{ $workshop->duration_text }}
            </span>
          @endif
          @if($workshop->rating)
            <span class="workshop-hero__rating">
              <svg width="16" height="16" viewBox="0 0 20 20" fill="#D4A853">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              {{ number_format($workshop->rating, 1) }} ({{ $workshop->review_count }} reviews)
            </span>
          @endif
        </div>
      </div>
    </div>
  </section>

  <!-- =====================================================
       TWO-COLUMN LAYOUT: CONTENT + BOOKING SIDEBAR
       ===================================================== -->
  <div class="workshop-content-wrapper">
    <div class="container">
      <div class="workshop-layout">

        <!-- LEFT COLUMN: Main Content -->
        <main class="workshop-main-content">

          <!-- Quick Facts Grid -->
          <section class="workshop-quick-facts" id="quick-facts">
            <div class="quick-facts-grid">
              @if($workshop->duration_text)
              <div class="quick-fact">
                <svg class="quick-fact__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"/>
                  <polyline points="12 6 12 12 16 14"/>
                </svg>
                <div class="quick-fact__content">
                  <span class="quick-fact__label">Duration</span>
                  <span class="quick-fact__value">{{ $workshop->duration_text }}</span>
                </div>
              </div>
              @endif

              @if($workshop->group_size_text)
              <div class="quick-fact">
                <svg class="quick-fact__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <div class="quick-fact__content">
                  <span class="quick-fact__label">Group Size</span>
                  <span class="quick-fact__value">{{ $workshop->group_size_text }}</span>
                </div>
              </div>
              @endif

              @php $languages = (is_array($workshop->languages) ? $workshop->languages : json_decode($workshop->languages, true)) ?: []; @endphp
              @if(count($languages) > 0)
              <div class="quick-fact">
                <svg class="quick-fact__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"/>
                  <line x1="2" y1="12" x2="22" y2="12"/>
                  <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                </svg>
                <div class="quick-fact__content">
                  <span class="quick-fact__label">Languages</span>
                  <span class="quick-fact__value">{{ implode(", ", $languages) }}</span>
                </div>
              </div>
              @endif

              @if($workshop->advance_booking_days)
              <div class="quick-fact">
                <svg class="quick-fact__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                  <line x1="16" y1="2" x2="16" y2="6"/>
                  <line x1="8" y1="2" x2="8" y2="6"/>
                  <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <div class="quick-fact__content">
                  <span class="quick-fact__label">Book Ahead</span>
                  <span class="quick-fact__value">{{ $workshop->advance_booking_days }} days</span>
                </div>
              </div>
              @endif
            </div>
          </section>

          <!-- About the Workshop -->
          <section class="workshop-section" id="about">
            <h2 class="workshop-section__title">About This Workshop</h2>
            @if($workshop->short_description)
              <p class="workshop-section__lead">{{ $workshop->short_description }}</p>
            @endif
            @if($workshop->long_description)
              <div class="workshop-section__content">
                {!! nl2br(e($workshop->long_description)) !!}
              </div>
            @endif
          </section>

          <!-- Meet the Master -->
          <section class="workshop-section workshop-master" id="master">
            <h2 class="workshop-section__title">Meet the Master</h2>
            <div class="master-card">
              @if($workshop->master_image_url)
                <div class="master-card__image">
                  <img src="{{ $workshop->master_image_url }}" alt="{{ $workshop->master_name }}">
                </div>
              @endif
              <div class="master-card__content">
                <h3 class="master-card__name">{{ $workshop->master_name }}</h3>
                @if($workshop->master_title)
                  <p class="master-card__title">{{ $workshop->master_title }}</p>
                @endif
                @if($workshop->master_bio)
                  <div class="master-card__bio">
                    {!! nl2br(e($workshop->master_bio)) !!}
                  </div>
                @endif
                @if($workshop->craft_tradition)
                  <p class="master-card__tradition">
                    <strong>Tradition:</strong> {{ $workshop->craft_tradition }}
                  </p>
                @endif
              </div>
            </div>
          </section>

          <!-- What You'll Do -->
          @if($workshop->what_you_will_do && count($workshop->what_you_will_do) > 0)
          <section class="workshop-section" id="experience">
            <h2 class="workshop-section__title">What You'll Do</h2>
            <div class="experience-steps">
              @foreach($workshop->what_you_will_do as $index => $step)
              <div class="experience-step">
                <div class="experience-step__number">{{ $index + 1 }}</div>
                <div class="experience-step__content">
                  <h3 class="experience-step__title">{{ $step['title'] ?? 'Step ' . ($index + 1) }}</h3>
                  @if(isset($step['description']))
                    <p class="experience-step__description">{{ $step['description'] }}</p>
                  @endif
                  @if(isset($step['duration']))
                    <span class="experience-step__duration">{{ $step['duration'] }}</span>
                  @endif
                </div>
              </div>
              @endforeach
            </div>
          </section>
          @endif

          <!-- Included / Excluded -->
          <section class="workshop-section" id="included">
            <h2 class="workshop-section__title">What's Included</h2>
            <div class="included-excluded-grid">
              @if($workshop->included_items && count($workshop->included_items) > 0)
              <div class="included-list">
                <h3 class="list-heading list-heading--included">Included</h3>
                <ul>
                  @foreach($workshop->included_items as $item)
                  <li>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="#059669">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ $item }}
                  </li>
                  @endforeach
                </ul>
              </div>
              @endif

              @if($workshop->excluded_items && count($workshop->excluded_items) > 0)
              <div class="excluded-list">
                <h3 class="list-heading list-heading--excluded">Not Included</h3>
                <ul>
                  @foreach($workshop->excluded_items as $item)
                  <li>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="#DC2626">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ $item }}
                  </li>
                  @endforeach
                </ul>
              </div>
              @endif
            </div>
          </section>

          <!-- Who Is This For -->
          @if($workshop->who_is_it_for && count($workshop->who_is_it_for) > 0)
          <section class="workshop-section" id="audience">
            <h2 class="workshop-section__title">Who Is This For?</h2>
            <div class="audience-grid">
              @foreach($workshop->who_is_it_for as $audience)
              <div class="audience-card">
                @if(is_array($audience) && isset($audience['icon']))
                <i class="fas fa-{{ $audience['icon'] }}" style="color: #D4A853; font-size: 1.5rem;"></i>
                <div class="audience-card__content">
                  <h4>{{ $audience['title'] }}</h4>
                  <p>{{ $audience['description'] }}</p>
                </div>
                @else
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D4A853" stroke-width="2">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                  <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <span>{{ is_array($audience) ? ($audience['title'] ?? '') : $audience }}</span>
                @endif
              </div>
              @endforeach
            </div>
          </section>
          @endif
          <!-- Part of Your Journey -->
          @if($relatedTours && $relatedTours->count() > 0)
          <section class="workshop-section" id="related-tours">
            <h2 class="workshop-section__title">Part of Your Journey</h2>
            <p class="workshop-section__lead">This workshop can be part of these curated tours:</p>
            <div class="related-tours-grid">
              @foreach($relatedTours as $tour)
              <a href="{{ url('/tours/' . $tour->slug) }}" class="related-tour-card">
                @if($tour->hero_image)
                  <div class="related-tour-card__image">
                    <img src="{{ $tour->hero_image_url }}" alt="{{ $tour->title }}">
                  </div>
                @endif
                <div class="related-tour-card__content">
                  <h3 class="related-tour-card__title">{{ $tour->title }}</h3>
                  <div class="related-tour-card__meta">
                    <span>{{ $tour->duration_days }} days</span>
                    @if($tour->price_per_person)
                      <span>From ${{ number_format($tour->price_per_person, 0) }}</span>
                    @endif
                  </div>
                </div>
              </a>
              @endforeach
            </div>
          </section>
          @endif

          <!-- Practical Information -->
          @if($workshop->practical_info && count($workshop->practical_info) > 0)
          <section class="workshop-section" id="practical">
            <h2 class="workshop-section__title">Practical Information</h2>
            <div class="practical-info-grid">
              @foreach($workshop->practical_info as $info)
              <div class="practical-info-item">
                <h3 class="practical-info-item__title">{{ $info['title'] ?? '' }}</h3>
                <p class="practical-info-item__content">{{ $info['content'] ?? '' }}</p>
              </div>
              @endforeach
            </div>
          </section>
          @endif

          <!-- Location -->
          @if($workshop->location_description || ($workshop->latitude && $workshop->longitude))
          <section class="workshop-section" id="location">
            <h2 class="workshop-section__title">Location</h2>
            @if($workshop->location_description)
              <p class="workshop-section__content">{{ $workshop->location_description }}</p>
            @endif
            @if($workshop->address)
              <p class="workshop-address">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="#1E4A7C">
                  <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
                {{ $workshop->location_full }}
              </p>
            @endif
            @if($workshop->latitude && $workshop->longitude)
            <div class="workshop-map" id="workshop-map" data-lat="{{ $workshop->latitude }}" data-lng="{{ $workshop->longitude }}">
              <!-- Map placeholder - implement with Leaflet or Google Maps -->
              <div class="map-placeholder">
                <a href="https://www.google.com/maps?q={{ $workshop->latitude }},{{ $workshop->longitude }}" target="_blank" rel="noopener">
                  View on Google Maps
                </a>
              </div>
            </div>
            @endif
          </section>
          @endif

          <!-- FAQs -->
          @if($workshop->faqs && count($workshop->faqs) > 0)
          <section class="workshop-section" id="faqs">
            <h2 class="workshop-section__title">Frequently Asked Questions</h2>
            <div class="faq-list">
              @foreach($workshop->faqs as $index => $faq)
              <details class="faq-item" {{ $index === 0 ? 'open' : '' }}>
                <summary class="faq-item__question">
                  {{ $faq['question'] ?? '' }}
                  <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                  </svg>
                </summary>
                <div class="faq-item__answer">
                  {{ $faq['answer'] ?? '' }}
                </div>
              </details>
              @endforeach
            </div>
          </section>
          @endif

        </main>

        <!-- RIGHT COLUMN: Booking Sidebar -->
        <aside class="workshop-sidebar" data-sticky="true">
          <div class="booking-card">
            <!-- Price Header -->
            <div class="booking-card__header">
              @if($workshop->price_per_person)
                <div class="booking-price">
                  <span class="price-label">from</span>
                  <span class="price-amount">${{ number_format($workshop->price_per_person, 0) }}</span>
                  <span class="price-unit">/person</span>
                </div>
              @else
                <div class="booking-price">
                  <span class="price-amount">Contact for pricing</span>
                </div>
              @endif
              @if($workshop->rating)
                <div class="booking-rating">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="#D4A853">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                  <span>{{ number_format($workshop->rating, 1) }}</span>
                  <span class="rating-count">({{ $workshop->review_count }})</span>
                </div>
              @endif
            </div>

            <!-- Quick Details -->
            <div class="booking-card__details">
              @if($workshop->duration_text)
              <div class="detail-row">
                <span class="detail-label">Duration</span>
                <span class="detail-value">{{ $workshop->duration_text }}</span>
              </div>
              @endif
              @if($workshop->group_size_text)
              <div class="detail-row">
                <span class="detail-label">Group Size</span>
                <span class="detail-value">{{ $workshop->group_size_text }}</span>
              </div>
              @endif
              @if($workshop->operating_hours)
              <div class="detail-row">
                <span class="detail-label">Available</span>
                <span class="detail-value">{{ $workshop->operating_hours }}</span>
              </div>
              @endif
            </div>

            <!-- Private Session Option -->
            @if($workshop->private_session_price)
            <div class="private-session-option">
              <div class="private-session-header">
                <span class="private-label">Private Session</span>
                <span class="private-price">${{ number_format($workshop->private_session_price, 0) }}</span>
              </div>
              <p class="private-description">Exclusive experience for your group</p>
            </div>
            @endif

            <!-- CTA Button -->
            <a href="{{ url('/contact') }}?workshop={{ $workshop->slug }}" class="btn-book-workshop">
              <span>Request This Workshop</span>
              <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
            </a>

            <!-- Trust Signals -->
            <div class="trust-signals">
              <div class="trust-item">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="#059669">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Free cancellation up to 48h</span>
              </div>
              <div class="trust-item">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="#059669">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Instant confirmation</span>
              </div>
              <div class="trust-item">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="#059669">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Local expert host</span>
              </div>
            </div>

            <!-- Questions CTA -->
            <div class="questions-cta">
              <p>Have questions?</p>
              <a href="{{ url('/contact') }}">Contact our team</a>
            </div>
          </div>
        </aside>

      </div>
    </div>
  </div>

  <!-- Gallery Section -->
  @if($workshop->getGalleryUrls() && count($workshop->getGalleryUrls()) > 0)
  <section class="workshop-gallery" id="gallery">
    <div class="container">
      <h2 class="workshop-section__title">Gallery</h2>
      <div class="gallery-grid">
        @foreach($workshop->getGalleryUrls() as $index => $imageUrl)
        <div class="gallery-item {{ $index === 0 ? 'gallery-item--large' : '' }}">
          <img src="{{ $imageUrl }}" alt="{{ $workshop->title }} - Image {{ $index + 1 }}" loading="lazy">
        </div>
        @endforeach
      </div>
    </div>
  </section>
  @endif

@endsection

@push('styles')
<style>
/* =====================================================
   WORKSHOP DETAIL PAGE STYLES
   ===================================================== */

/* Color Variables */
:root {
  --ws-bg-cream: #FAF8F5;
  --ws-accent-blue: #1E4A7C;
  --ws-accent-gold: #D4A853;
  --ws-text-primary: #1f2937;
  --ws-text-secondary: #6b7280;
  --ws-success: #059669;
  --ws-error: #DC2626;
}

/* Hero Section */
.workshop-hero {
  position: relative;
  min-height: 60vh;
  display: flex;
  align-items: flex-end;
  padding-bottom: 80px;
  overflow: hidden;
}

.workshop-hero__background {
  position: absolute;
  inset: 0;
  z-index: 0;
}

.workshop-hero__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.workshop-hero__placeholder {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, var(--ws-accent-blue) 0%, #2d5a8c 100%);
}

.workshop-hero__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 50%, transparent 100%);
}

.workshop-hero__content {
  position: relative;
  z-index: 1;
  color: white;
  max-width: 800px;
}

.workshop-breadcrumb {
  font-size: 14px;
  margin-bottom: 16px;
  opacity: 0.8;
}

.workshop-breadcrumb a {
  color: white;
  text-decoration: none;
}

.workshop-breadcrumb a:hover {
  text-decoration: underline;
}

.workshop-breadcrumb span {
  margin: 0 8px;
}

.workshop-hero__badge {
  display: inline-block;
  background: var(--ws-accent-gold);
  color: white;
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  margin-bottom: 16px;
}

.workshop-hero__title {
  font-size: clamp(32px, 5vw, 56px);
  font-weight: 600;
  line-height: 1.1;
  margin: 0 0 16px;
  letter-spacing: -0.02em;
}

.workshop-hero__subtitle {
  font-size: 20px;
  opacity: 0.9;
  margin: 0 0 24px;
  line-height: 1.5;
}

.workshop-hero__meta {
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
  font-size: 15px;
}

.workshop-hero__meta > span {
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Content Wrapper */
.workshop-content-wrapper {
  background: var(--ws-bg-cream);
  padding: 60px 0 80px;
}

.workshop-layout {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 48px;
  align-items: start;
}

@media (max-width: 1024px) {
  .workshop-layout {
    grid-template-columns: 1fr;
  }
}

/* Main Content */
.workshop-main-content {
  min-width: 0;
}

/* Quick Facts Grid */
.workshop-quick-facts {
  background: white;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 40px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.quick-facts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 24px;
}

.quick-fact {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.quick-fact__icon {
  color: var(--ws-accent-blue);
  flex-shrink: 0;
}

.quick-fact__label {
  display: block;
  font-size: 12px;
  color: var(--ws-text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 4px;
}

.quick-fact__value {
  display: block;
  font-size: 15px;
  font-weight: 600;
  color: var(--ws-text-primary);
}

/* Section Styles */
.workshop-section {
  margin-bottom: 48px;
  padding-bottom: 48px;
  border-bottom: 1px solid rgba(0,0,0,0.06);
}

.workshop-section:last-child {
  border-bottom: none;
}

.workshop-section__title {
  font-size: 24px;
  font-weight: 600;
  color: var(--ws-text-primary);
  margin: 0 0 20px;
  letter-spacing: -0.01em;
}

.workshop-section__lead {
  font-size: 18px;
  color: var(--ws-text-secondary);
  line-height: 1.6;
  margin: 0 0 16px;
}

.workshop-section__content {
  font-size: 16px;
  line-height: 1.7;
  color: var(--ws-text-primary);
}

/* Master Card */
.master-card {
  display: flex;
  gap: 32px;
  background: white;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

@media (max-width: 640px) {
  .master-card {
    flex-direction: column;
  }
}

.master-card__image {
  flex-shrink: 0;
  width: 180px;
  height: 180px;
  border-radius: 12px;
  overflow: hidden;
}

.master-card__image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.master-card__name {
  font-size: 22px;
  font-weight: 600;
  color: var(--ws-text-primary);
  margin: 0 0 8px;
}

.master-card__title {
  font-size: 14px;
  color: var(--ws-accent-gold);
  font-weight: 600;
  margin: 0 0 16px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.master-card__bio {
  font-size: 15px;
  line-height: 1.7;
  color: var(--ws-text-secondary);
  margin-bottom: 16px;
}

.master-card__tradition {
  font-size: 14px;
  color: var(--ws-text-primary);
  padding-top: 16px;
  border-top: 1px solid rgba(0,0,0,0.06);
  margin: 0;
}

/* Experience Steps */
.experience-steps {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.experience-step {
  display: flex;
  gap: 20px;
  padding: 24px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.experience-step__number {
  flex-shrink: 0;
  width: 40px;
  height: 40px;
  background: var(--ws-accent-blue);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 16px;
}

.experience-step__title {
  font-size: 17px;
  font-weight: 600;
  color: var(--ws-text-primary);
  margin: 0 0 8px;
}

.experience-step__description {
  font-size: 15px;
  color: var(--ws-text-secondary);
  line-height: 1.6;
  margin: 0 0 8px;
}

.experience-step__duration {
  font-size: 13px;
  color: var(--ws-accent-gold);
  font-weight: 500;
}

/* Included / Excluded */
.included-excluded-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 32px;
}

@media (max-width: 640px) {
  .included-excluded-grid {
    grid-template-columns: 1fr;
  }
}

.list-heading {
  font-size: 14px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 16px;
}

.list-heading--included {
  color: var(--ws-success);
}

.list-heading--excluded {
  color: var(--ws-error);
}

.included-list ul,
.excluded-list ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.included-list li,
.excluded-list li {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 10px 0;
  font-size: 15px;
  color: var(--ws-text-primary);
  border-bottom: 1px solid rgba(0,0,0,0.04);
}

.included-list li:last-child,
.excluded-list li:last-child {
  border-bottom: none;
}

.included-list svg,
.excluded-list svg {
  flex-shrink: 0;
  margin-top: 2px;
}

/* Audience Grid */
.audience-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.audience-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 20px;
  background: white;
  border-radius: 10px;
  font-size: 15px;
  color: var(--ws-text-primary);
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}


.audience-card__content {
  flex: 1;
}

.audience-card__content h4 {
  margin: 0 0 4px 0;
  font-size: 16px;
  font-weight: 600;
  color: var(--ws-text-primary);
}

.audience-card__content p {
  margin: 0;
  font-size: 14px;
  color: var(--ws-text-secondary);
  line-height: 1.4;
}

.audience-card i {
  flex-shrink: 0;
}
/* Related Tours */
.related-tours-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
}

.related-tour-card {
  display: block;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  text-decoration: none;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  transition: transform 0.2s, box-shadow 0.2s;
}

.related-tour-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

.related-tour-card__image {
  aspect-ratio: 16/10;
  overflow: hidden;
}

.related-tour-card__image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.related-tour-card__content {
  padding: 20px;
}

.related-tour-card__title {
  font-size: 17px;
  font-weight: 600;
  color: var(--ws-text-primary);
  margin: 0 0 8px;
}

.related-tour-card__meta {
  display: flex;
  gap: 16px;
  font-size: 14px;
  color: var(--ws-text-secondary);
}

/* Practical Info */
.practical-info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
}

.practical-info-item {
  padding: 20px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.practical-info-item__title {
  font-size: 14px;
  font-weight: 600;
  color: var(--ws-accent-blue);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin: 0 0 8px;
}

.practical-info-item__content {
  font-size: 15px;
  color: var(--ws-text-primary);
  line-height: 1.6;
  margin: 0;
}

/* Location */
.workshop-address {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 15px;
  color: var(--ws-text-primary);
  margin: 16px 0;
}

.workshop-map {
  height: 300px;
  border-radius: 12px;
  overflow: hidden;
  background: #e5e7eb;
}

.map-placeholder {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.map-placeholder a {
  padding: 12px 24px;
  background: var(--ws-accent-blue);
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 500;
}

/* FAQs */
.faq-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.faq-item {
  background: white;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.faq-item__question {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  font-size: 16px;
  font-weight: 600;
  color: var(--ws-text-primary);
  cursor: pointer;
  list-style: none;
}

.faq-item__question::-webkit-details-marker {
  display: none;
}

.faq-item__question svg {
  flex-shrink: 0;
  transition: transform 0.2s;
}

.faq-item[open] .faq-item__question svg {
  transform: rotate(180deg);
}

.faq-item__answer {
  padding: 0 24px 20px;
  font-size: 15px;
  line-height: 1.7;
  color: var(--ws-text-secondary);
}

/* Sidebar */
.workshop-sidebar {
  position: sticky;
  top: 100px;
}

@media (max-width: 1024px) {
  .workshop-sidebar {
    position: static;
    order: -1;
  }
}

.booking-card {
  background: white;
  border-radius: 16px;
  padding: 28px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.08);
}

.booking-card__header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
  padding-bottom: 24px;
  border-bottom: 1px solid rgba(0,0,0,0.06);
}

.booking-price {
  display: flex;
  flex-wrap: wrap;
  align-items: baseline;
  gap: 4px;
}

.price-label {
  font-size: 14px;
  color: var(--ws-text-secondary);
}

.price-amount {
  font-size: 32px;
  font-weight: 700;
  color: var(--ws-text-primary);
}

.price-unit {
  font-size: 14px;
  color: var(--ws-text-secondary);
}

.booking-rating {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 14px;
  font-weight: 600;
  color: var(--ws-text-primary);
}

.rating-count {
  font-weight: normal;
  color: var(--ws-text-secondary);
}

.booking-card__details {
  margin-bottom: 24px;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px solid rgba(0,0,0,0.04);
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-label {
  font-size: 14px;
  color: var(--ws-text-secondary);
}

.detail-value {
  font-size: 14px;
  font-weight: 600;
  color: var(--ws-text-primary);
}

.private-session-option {
  background: linear-gradient(135deg, #fef3cd 0%, #fff8e1 100%);
  border: 1px solid var(--ws-accent-gold);
  border-radius: 10px;
  padding: 16px;
  margin-bottom: 24px;
}

.private-session-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 4px;
}

.private-label {
  font-size: 14px;
  font-weight: 600;
  color: var(--ws-text-primary);
}

.private-price {
  font-size: 18px;
  font-weight: 700;
  color: var(--ws-text-primary);
}

.private-description {
  font-size: 13px;
  color: var(--ws-text-secondary);
  margin: 0;
}

.btn-book-workshop {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 16px 24px;
  background: linear-gradient(135deg, var(--ws-accent-blue) 0%, #2d5a8c 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s;
  margin-bottom: 20px;
}

.btn-book-workshop:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(30, 74, 124, 0.3);
}

.trust-signals {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 20px 0;
  border-top: 1px solid rgba(0,0,0,0.06);
  border-bottom: 1px solid rgba(0,0,0,0.06);
}

.trust-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13px;
  color: var(--ws-text-secondary);
}

.questions-cta {
  text-align: center;
  padding-top: 20px;
}

.questions-cta p {
  font-size: 13px;
  color: var(--ws-text-secondary);
  margin: 0 0 8px;
}

.questions-cta a {
  font-size: 14px;
  font-weight: 600;
  color: var(--ws-accent-blue);
  text-decoration: none;
}

.questions-cta a:hover {
  text-decoration: underline;
}

/* Gallery Section */
.workshop-gallery {
  background: white;
  padding: 60px 0;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  grid-auto-rows: 200px;
  gap: 16px;
}

@media (max-width: 768px) {
  .gallery-grid {
    grid-template-columns: repeat(2, 1fr);
    grid-auto-rows: 150px;
  }
}

.gallery-item {
  border-radius: 12px;
  overflow: hidden;
}

.gallery-item--large {
  grid-column: span 2;
  grid-row: span 2;
}

.gallery-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.gallery-item:hover img {
  transform: scale(1.05);
}

/* Mobile Adjustments */
@media (max-width: 768px) {
  .workshop-hero {
    min-height: 50vh;
    padding-bottom: 40px;
  }

  .workshop-hero__title {
    font-size: 28px;
  }

  .workshop-content-wrapper {
    padding: 40px 0 60px;
  }

  .workshop-section {
    margin-bottom: 32px;
    padding-bottom: 32px;
  }

  .quick-facts-grid {
    grid-template-columns: 1fr 1fr;
  }

  .master-card {
    padding: 20px;
  }

  .master-card__image {
    width: 120px;
    height: 120px;
  }

  .experience-step {
    padding: 16px;
  }

  .booking-card {
    padding: 20px;
  }
}
</style>
@endpush
