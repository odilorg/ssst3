@extends('layouts.main')

@section('title', 'Contact Us - Jahongir Travel | Get in Touch with Our Silk Road Experts')
@section('meta_description', 'Contact Jahongir Travel for personalized Uzbekistan tour planning. Our local experts in Samarkand are ready to help you plan your perfect Silk Road adventure.')
@section('canonical', url('/contact'))

{{-- Open Graph --}}
@section('og_type', 'website')
@section('og_url', url('/contact'))
@section('og_title', 'Contact Us - Jahongir Travel')
@section('og_description', 'Contact our local experts in Samarkand to plan your perfect Silk Road adventure.')
@section('og_image', asset('images/og-contact.jpg'))

{{-- Structured Data - ContactPage + LocalBusiness --}}
@section('structured_data')
{
  "@@context": "https://schema.org",
  "@@type": "ContactPage",
  "name": "Contact Jahongir Travel",
  "description": "Contact Jahongir Travel for personalized Uzbekistan tour planning.",
  "url": "{{ url('/contact') }}",
  "mainEntity": {
    "@@type": "TravelAgency",
    "name": "Jahongir Travel",
    "telephone": "+998915550808",
    "email": "info@jahongir-travel.uz",
    "url": "{{ url('/') }}",
    "address": {
      "@@type": "PostalAddress",
      "streetAddress": "Registan Street, 15",
      "addressLocality": "Samarkand",
      "addressRegion": "Samarkand",
      "postalCode": "140100",
      "addressCountry": "UZ"
    },
    "geo": {
      "@@type": "GeoCoordinates",
      "latitude": "39.6542",
      "longitude": "66.9597"
    },
    "openingHoursSpecification": [
      {
        "@@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
        "opens": "09:00",
        "closes": "18:00"
      }
    ],
    "contactPoint": {
      "@@type": "ContactPoint",
      "telephone": "+998915550808",
      "contactType": "customer service",
      "availableLanguage": ["English", "Russian", "Uzbek"]
    }
  }
}
@endsection

{{-- Breadcrumb Structured Data --}}
@push('structured_data_breadcrumb')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "{{ url('/') }}"
    },
    {
      "@@type": "ListItem",
      "position": 2,
      "name": "Contact",
      "item": "{{ url('/contact') }}"
    }
  ]
}
</script>
@endpush

{{-- FAQ Structured Data --}}
@push('structured_data_faq')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "How can I book a tour?",
      "acceptedAnswer": {
        "@@type": "Answer",
        "text": "You can book a tour by browsing our tours page, selecting your preferred tour, and clicking the Book Now button. Alternatively, you can contact us directly via phone or email."
      }
    },
    {
      "@@type": "Question",
      "name": "Are there any age restrictions for the tour?",
      "acceptedAnswer": {
        "@@type": "Answer",
        "text": "Most of our tours are suitable for all ages. However, some adventure tours may have minimum age requirements for safety reasons."
      }
    },
    {
      "@@type": "Question",
      "name": "What should I pack for the tour?",
      "acceptedAnswer": {
        "@@type": "Answer",
        "text": "We recommend packing comfortable walking shoes, weather-appropriate clothing, sunscreen, a hat, and a reusable water bottle."
      }
    },
    {
      "@@type": "Question",
      "name": "Can I cancel or reschedule my booking?",
      "acceptedAnswer": {
        "@@type": "Answer",
        "text": "Yes, cancellations made 30+ days before the tour start date receive a full refund. For cancellations within 30 days, please contact us."
      }
    },
    {
      "@@type": "Question",
      "name": "Do you offer group discounts?",
      "acceptedAnswer": {
        "@@type": "Answer",
        "text": "Yes, we offer special discounts for group bookings of 6 or more people. Contact us for customized group rates."
      }
    }
  ]
}
</script>
@endpush

@push('styles')
<style>
/* Force visibility of animated elements */
.animate-on-scroll {
    opacity: 1 !important;
    transform: none !important;
}

/* ======================================================
   MODAL STYLES
   ====================================================== */

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    justify-content: center;
    align-items: center;
    z-index: 10000;
    opacity: 1;
}

@keyframes fadeIn {
    to { opacity: 1; }
}

.modal-container {
    background: white;
    border-radius: 16px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    transform: scale(1);
    animation: scaleIn 0.3s ease;
}

@keyframes scaleIn {
    to { transform: scale(1); }
}


.modal-close-x {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 40px;
    height: 40px;
    border: none;
    background: rgba(0, 0, 0, 0.05);
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    color: #666;
    z-index: 10;
}

.modal-close-x:hover {
    background: rgba(0, 0, 0, 0.1);
    color: #1a1a1a;
    transform: rotate(90deg);
}

.modal-close-x:active {
    transform: rotate(90deg) scale(0.95);
}

.modal-header {
    position: relative;
    text-align: center;
    padding: 2.5rem 2rem 1.5rem;
    border-bottom: 1px solid #eee;
}

.success-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    color: white;
}

.error-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    color: white;
}

.modal-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #1a1a1a;
}

.modal-subtitle {
    font-size: 1rem;
    color: #666;
    line-height: 1.5;
}

.modal-body {
    padding: 2rem;
}

.confirmation-reference {
    background: #f8f9fa;
    border-left: 4px solid #1a5490;
    padding: 1.25rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.confirmation-reference .label {
    display: block;
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.reference-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a5490;
    font-family: monospace;
}

.confirmation-details {
    margin-bottom: 1.5rem;
}

.confirmation-details h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #1a1a1a;
}

.detail-row {
    display: flex;
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #666;
    min-width: 120px;
}

.detail-value {
    color: #1a1a1a;
}

.modal-message {
    background: #e3f2fd;
    border-left: 4px solid #2196f3;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: start;
    gap: 0.75rem;
}

.modal-message i {
    color: #2196f3;
    font-size: 1.25rem;
    margin-top: 0.1rem;
    flex-shrink: 0;
}

.modal-message p {
    margin: 0;
    color: #0d47a1;
    font-size: 0.9rem;
    line-height: 1.5;
}

.modal-next-steps {
    margin-top: 1.5rem;
}

.modal-next-steps h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #1a1a1a;
}

.modal-next-steps ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.modal-next-steps li {
    padding: 0.5rem 0;
    padding-left: 2rem;
    position: relative;
    color: #666;
    line-height: 1.6;
}

.modal-next-steps li:before {
    content: "✓";
    position: absolute;
    left: 0;
    top: 0.5rem;
    width: 20px;
    height: 20px;
    background: #10b981;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
}

.alternative-contact-methods {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.alt-method {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    text-decoration: none;
    color: #1a1a1a;
    transition: all 0.3s ease;
}

.alt-method:hover {
    background: #e9ecef;
    transform: translateX(4px);
}

.alt-method i {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 50%;
    color: #1a5490;
}

.modal-footer {
    padding: 1.5rem 2rem;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: center;
    gap: 1rem;
}

.modal-header--error .error-icon {
    animation: shake 0.5s ease;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}

@media (max-width: 768px) {
    .modal-container {
        width: 95%;
        max-height: 95vh;
    }

    .modal-header {
        padding: 2rem 1.5rem 1rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-title {
        font-size: 1.5rem;
    }

    .reference-number {
        font-size: 1.25rem;
    }

    .detail-row {
        flex-direction: column;
        gap: 0.25rem;
    }

    .detail-label {
        min-width: auto;
        font-size: 0.85rem;
    }
}
</style>
<link rel="stylesheet" href="{{ asset('contact.css') }}">
@endpush
@section('content')
    <!-- ========================================
         HERO SECTION
         ======================================== -->
    <section class="contact-hero">
        <div class="contact-hero__overlay"></div>
        <div class="container">
            <div class="contact-hero__content">
                <h1 class="contact-hero__title">Contact the Team</h1>
                <p class="contact-hero__subtitle">
                    Planning your next trip to Uzbekistan? Let's talk!<br>
                    Our team of Silk Road experts is here to help you every step of the way.
                </p>
            </div>
        </div>
    </section>

    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb" aria-label="Breadcrumb" style="background: #f8f9fa; padding: 1rem 0;">
        <div class="container">
            <ol style="list-style: none; padding: 0; margin: 0; display: flex; align-items: center; flex-wrap: wrap;">
                <li style="display: flex; align-items: center;">
                    <a href="{{ url('/') }}" style="color: #1a5490; text-decoration: none;">Home</a>
                    <span style="margin: 0 0.5rem; color: #666;">/</span>
                </li>
                <li style="color: #666; font-weight: 500;" aria-current="page">Contact</li>
            </ol>
        </div>
    </nav>

    <!-- ========================================
         CONTACT SECTION
         ======================================== -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <!-- ========================================
                     LEFT COLUMN: CONTACT FORM
                     ======================================== -->
                <div class="contact-form-wrapper animate-on-scroll" id="contact-form">
                    <h2 class="contact-form__title">Send us a message</h2>
                    <p class="contact-form__intro">We typically respond within 24 hours. Let's start planning your perfect Uzbekistan adventure!</p>

                    <!-- Benefits Section -->
                    <div class="form-benefits">
                        <div class="form-benefit">
                            <i class="fas fa-headset"></i>
                            <span>Expert local guidance</span>
                        </div>
                        <div class="form-benefit">
                            <i class="fas fa-clock"></i>
                            <span>24-hour response</span>
                        </div>
                        <div class="form-benefit">
                            <i class="fas fa-shield-alt"></i>
                            <span>Secure & confidential</span>
                        </div>
                    </div>

                                        <form class="contact-form" id="contactForm">
                                            @csrf

                        <!-- Name Field -->
                        <div class="form-group">
                            <label for="name" class="form-label">
                                Your Name <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-input"
                                placeholder="e.g., John Smith"
                                required
                                aria-required="true"
                                aria-describedby="name-error"
                            >
                            <span class="form-error" id="name-error" role="alert"></span>
                        </div>

                        <!-- Email Field -->
                        <div class="form-group">
                            <label for="email" class="form-label">
                                Email Address <span class="required">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input"
                                placeholder="you@example.com"
                                required
                                inputmode="email"
                                aria-required="true"
                                aria-describedby="email-error"
                            >
                            <span class="form-error" id="email-error" role="alert"></span>
                        </div>

                        <!-- Phone Field (Optional) -->
                        <div class="form-group">
                            <label for="phone" class="form-label">
                                Phone <span class="optional">(optional)</span>
                            </label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                class="form-input"
                                placeholder="+998 90 123 4567"
                                inputmode="tel"
                                aria-describedby="phone-error"
                            >
                            <span class="form-error" id="phone-error" role="alert"></span>
                        </div>

                        <!-- Message Field -->
                        <div class="form-group">
                            <label for="message" class="form-label">
                                Your Message <span class="required">*</span>
                            </label>
                            <textarea
                                id="message"
                                name="message"
                                class="form-input form-textarea"
                                rows="4"
                                placeholder="Tell us about your travel plans..."
                                required
                                aria-required="true"
                                aria-describedby="message-error"
                            ></textarea>
                            <span class="form-error" id="message-error" role="alert"></span>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn--primary btn--large form-submit">
                            <span class="button-text">Send Message</span>
                            <i class="fas fa-arrow-right button-icon"></i>
                        </button>

                        <!-- Trust Signal -->
                        <p class="form-trust-signal">
                            <i class="fas fa-lock"></i> Your information is secure & private
                        </p>
                    </form>

                    <!-- Alternative Contact Methods -->
                    <div class="alternative-contact">
                        <p class="alt-contact-title">Need immediate help?</p>
                        <div class="alt-contact-methods">
                            <a href="https://wa.me/998915550808" class="alt-contact-link" target="_blank" rel="noopener">
                                <i class="fab fa-whatsapp"></i> WhatsApp: +998 91 555 0808
                            </a>
                            <a href="mailto:info@jahongir-travel.uz" class="alt-contact-link">
                                <i class="fas fa-envelope"></i> info@jahongir-travel.uz
                            </a>
                        </div>
                    </div>

                    <!-- Testimonial Card -->
                    <div class="contact-card contact-card--testimonial animate-on-scroll">
                        <div class="testimonial-header">
                            <div class="testimonial-quote">
                                <i class="fas fa-quote-left"></i>
                            </div>
                            <div class="testimonial-stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="testimonial-text">
                            "Exceptional service from start to finish! The team helped us plan the perfect Silk Road journey.
                            Their local knowledge and attention to detail made our trip unforgettable."
                        </p>
                        <div class="testimonial-author">
                            <div class="testimonial-author__avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="testimonial-author__info">
                                <p class="testimonial-author__name">Sarah Mitchell</p>
                                <p class="testimonial-author__location">London, UK</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================
                     RIGHT COLUMN: CONTACT INFO
                     ======================================== -->
                <div class="contact-info">
                    <!-- Get in Touch Card -->
                    <div class="contact-card animate-on-scroll">
                        <h3 class="contact-card__title">Get in touch</h3>
                        <p class="contact-card__description">
                            We love to chat about your travel plans and
                            are ready to help in any way we can.
                        </p>

                        <!-- Trust Badge -->
                        <div class="trust-badge">
                            <div class="trust-badge__rating">
                                <i class="fab fa-google"></i>
                                <span class="trust-badge__score">4.9</span>
                                <div class="trust-badge__stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <p class="trust-badge__text">Based on 127+ reviews</p>
                        </div>

                        <div class="contact-card__item">
                            <i class="fas fa-phone contact-card__icon"></i>
                            <div class="contact-card__content">
                                <span class="contact-card__label">Call us</span>
                                <a href="tel:+998915550808" class="contact-card__link">
                                    +998 91 555 08 08
                                </a>
                            </div>
                        </div>

                        <div class="contact-card__item">
                            <i class="fas fa-envelope contact-card__icon"></i>
                            <div class="contact-card__content">
                                <span class="contact-card__label">Email us</span>
                                <a href="mailto:info@jahongir-travel.uz" class="contact-card__link">
                                    info@jahongir-travel.uz
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Opening Hours Card -->
                    <div class="contact-card animate-on-scroll">
                        <h3 class="contact-card__title">
                            <i class="fas fa-clock contact-card__title-icon"></i>
                            Opening Hours
                        </h3>

                        <div class="hours-block">
                            <p class="hours-block__season">Monday through Friday</p>
                            <p class="hours-block__time">9:00 – 18:00</p>
                            <p class="hours-block__time">Sat – Sun: Closed</p>
                        </div>

                        <div class="hours-block">
                            <p class="hours-block__season">Peak Season (Apr – Sep)</p>
                            <p class="hours-block__time">Mon – Fri: 8:00 – 20:00</p>
                            <p class="hours-block__time">Sat: 9:00 – 15:00</p>
                        </div>

                        <p class="contact-card__timezone">
                            <i class="fas fa-info-circle"></i>
                            All times are in Uzbekistan Standard Time (UTC+5)
                        </p>
                    </div>

                    <!-- Head Office Card -->
                    <div class="contact-card animate-on-scroll">
                        <h3 class="contact-card__title">
                            <i class="fas fa-building contact-card__title-icon"></i>
                            Head Office
                        </h3>
                        <p class="contact-card__description">
                            Visit our cozy office in the heart of Samarkand, right near the historic Registan Square.
                            We'd love to meet you in person!
                        </p>

                        <address class="contact-address">
                            <i class="fas fa-map-marker-alt contact-card__icon"></i>
                            <div class="contact-card__content">
                                Registan Street, 15<br>
                                Samarkand, Chirokchi 4
                            </div>
                        </address>

                        <a href="https://maps.google.com/?q=Registan+Street+15+Samarkand+Uzbekistan"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn--outline btn--small contact-card__map-btn">
                            <i class="fas fa-map"></i> Open in Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
         PERSONALITY SECTION
         ======================================== -->
    <section id="contact-personality" class="contact-personality" aria-labelledby="contact-personality-title">
        <div class="container cp-grid">
            <figure class="cp-media">
                <img
                    src="/images/team-photo.jpg"
                    width="405"
                    height="340"
                    alt="Jahongir Travel team in Samarkand office"
                    loading="lazy"
                    decoding="async"
                    class="cp-media__img" />
                <figcaption class="visually-hidden">The local team that crafts and supports your trip</figcaption>
            </figure>
            <div class="cp-copy">
                <h2 id="contact-personality-title" class="cp-title">Faces behind every journey</h2>
                <p class="cp-sub">We're a small local team based in Samarkand — the same people who greet travelers, plan routes, and make every trip feel like family.</p>
                <p class="cp-body">Whether it's crafting your first Silk Road itinerary or helping you choose the best guest house, you'll always talk to someone who knows Uzbekistan by heart.</p>
                <a href="/about-us" class="btn btn--outline cp-cta">Meet the Team</a>
            </div>
        </div>
    </section>

    <!-- ========================================
         FAQ SECTION
         ======================================== -->
    <section class="faq-section">
        <div class="container">
            <h2 class="faq-section__title">Frequently asked questions</h2>
            <p class="faq-section__subtitle">Quick answers to common questions about touring Uzbekistan</p>

            <div class="faq-grid">
                <!-- FAQ Item 1 -->
                <details class="faq-item animate-on-scroll">
                    <summary class="faq-item__question">
                        <span>How can I book a tour?</span>
                        <i class="fas fa-chevron-down faq-item__icon"></i>
                    </summary>
                    <div class="faq-item__answer">
                        <p>
                            You can book a tour by browsing our tours page, selecting your preferred tour,
                            and clicking the "Book Now" button. Alternatively, you can contact us directly
                            via phone or email, and our team will assist you with the booking process.
                        </p>
                    </div>
                </details>

                <!-- FAQ Item 2 -->
                <details class="faq-item animate-on-scroll">
                    <summary class="faq-item__question">
                        <span>Are there any age restrictions for the tour?</span>
                        <i class="fas fa-chevron-down faq-item__icon"></i>
                    </summary>
                    <div class="faq-item__answer">
                        <p>
                            Most of our tours are suitable for all ages. However, some adventure tours
                            may have minimum age requirements for safety reasons. Please check the specific
                            tour details or contact us for more information about age restrictions.
                        </p>
                    </div>
                </details>

                <!-- FAQ Item 3 -->
                <details class="faq-item animate-on-scroll">
                    <summary class="faq-item__question">
                        <span>What should I pack for the tour?</span>
                        <i class="fas fa-chevron-down faq-item__icon"></i>
                    </summary>
                    <div class="faq-item__answer">
                        <p>
                            We recommend packing comfortable walking shoes, weather-appropriate clothing,
                            sunscreen, a hat, and a reusable water bottle. For specific tours, we'll send
                            you a detailed packing list after booking. Don't forget your camera to capture
                            the beautiful sights of Uzbekistan!
                        </p>
                    </div>
                </details>

                <!-- FAQ Item 4 -->
                <details class="faq-item animate-on-scroll">
                    <summary class="faq-item__question">
                        <span>How can I contact customer support after the tour?</span>
                        <i class="fas fa-chevron-down faq-item__icon"></i>
                    </summary>
                    <div class="faq-item__answer">
                        <p>
                            You can reach our customer support team anytime via email at
                            info@jahongir-travel.uz or by calling +998 91 555 08 08. We're here to help
                            with any questions or feedback you may have about your tour experience.
                        </p>
                    </div>
                </details>

                <!-- FAQ Item 5 -->
                <details class="faq-item animate-on-scroll">
                    <summary class="faq-item__question">
                        <span>Can I cancel or reschedule my booking?</span>
                        <i class="fas fa-chevron-down faq-item__icon"></i>
                    </summary>
                    <div class="faq-item__answer">
                        <p>
                            Yes, you can cancel or reschedule your booking according to our cancellation
                            policy. Cancellations made 30+ days before the tour start date receive a full
                            refund. For cancellations within 30 days, please contact us to discuss available
                            options and any applicable fees.
                        </p>
                    </div>
                </details>

                <!-- FAQ Item 6 -->
                <details class="faq-item animate-on-scroll">
                    <summary class="faq-item__question">
                        <span>Do you offer group discounts?</span>
                        <i class="fas fa-chevron-down faq-item__icon"></i>
                    </summary>
                    <div class="faq-item__answer">
                        <p>
                            Yes, we offer special discounts for group bookings of 6 or more people.
                            The discount varies depending on the tour and group size. Contact us with
                            your travel details, and we'll provide you with a customized group rate.
                        </p>
                    </div>
                </details>
            </div>

            <!-- FAQ Help Link -->
            <div class="faq-help">
                <p class="faq-help__text">Didn't find your answer?</p>
                <a href="#contact-form" class="faq-help__link">
                    Contact us
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ========================================
         WHATSAPP FLOATING BUTTON (Desktop)
         ======================================== -->
    <a href="https://wa.me/998915550808?text=Hi!%20I'm%20interested%20in%20learning%20more%20about%20your%20tours%20in%20Uzbekistan."
       class="whatsapp-float"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="Chat with us on WhatsApp">
        <i class="fab fa-whatsapp"></i>
        <span class="whatsapp-float__tooltip">Chat with us!</span>
    </a>

    <!-- ========================================
         MOBILE STICKY CTA BAR (Mobile Only)
         ======================================== -->
    <div class="mobile-cta-bar">
        <a href="tel:+998915550808" class="mobile-cta-bar__button mobile-cta-bar__button--call">
            <i class="fas fa-phone"></i>
            <span>Call</span>
        </a>
        <a href="https://wa.me/998915550808?text=Hi!%20I'm%20interested%20in%20learning%20more%20about%20your%20tours%20in%20Uzbekistan."
           target="_blank"
           rel="noopener noreferrer"
           class="mobile-cta-bar__button mobile-cta-bar__button--whatsapp">
            <i class="fab fa-whatsapp"></i>
            <span>WhatsApp</span>
        </a>
        <a href="mailto:info@jahongir-travel.uz" class="mobile-cta-bar__button mobile-cta-bar__button--email">
            <i class="fas fa-envelope"></i>
            <span>Email</span>
        </a>
    </div>

    <!-- ========================================
         FOOTER
         ======================================== -->

    <!-- ========================================
         SUCCESS MODAL
         ======================================== -->
    <div id="contact-success-modal" class="modal-overlay" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <button type="button" class="modal-close-x" id="contact-success-close-x" aria-label="Close modal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <div class="success-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <h2 class="modal-title">Message Sent Successfully!</h2>
                <p class="modal-subtitle">Thank you for contacting us. We will get back to you within 24 hours.</p>
            </div>

            <div class="modal-body">
                <div class="confirmation-reference">
                    <span class="label">Your Reference Number</span>
                    <span class="reference-number" id="contact-modal-reference">N/A</span>
                </div>

                <div class="confirmation-details">
                    <h3>Your Contact Information</h3>
                    <div class="detail-row">
                        <span class="detail-label">Name:</span>
                        <span class="detail-value" id="contact-modal-name">N/A</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value" id="contact-modal-email">N/A</span>
                    </div>
                </div>

                <div class="modal-message">
                    <i class="fas fa-info-circle"></i>
                    <p>We have sent a confirmation email to your address. Please check your inbox (and spam folder).</p>
                </div>

                <div class="modal-next-steps">
                    <h3>What happens next?</h3>
                    <ul>
                        <li>Our team will review your message</li>
                        <li>We'll respond within 24 hours</li>
                        <li>Keep your reference number for future correspondence</li>
                    </ul>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn--primary" id="contact-success-close">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================
         ERROR MODAL
         ======================================== -->
    <div id="contact-error-modal" class="modal-overlay" style="display: none;">
        <div class="modal-container">
            <div class="modal-header modal-header--error">
                <button type="button" class="modal-close-x" id="contact-error-close-x" aria-label="Close modal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <div class="error-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h2 class="modal-title">Submission Failed</h2>
                <p class="modal-subtitle" id="contact-error-message">An error occurred. Please try again.</p>
            </div>

            <div class="modal-body">
                <div class="modal-message">
                    <i class="fas fa-lightbulb"></i>
                    <p>You can also reach us through these alternative methods:</p>
                </div>

                <div class="alternative-contact-methods">
                    <a href="https://wa.me/998915550808" class="alt-method" target="_blank" rel="noopener">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp: +998 91 555 0808</span>
                    </a>
                    <a href="mailto:info@jahongir-travel.uz" class="alt-method">
                        <i class="fas fa-envelope"></i>
                        <span>Email: info@jahongir-travel.uz</span>
                    </a>
                    <a href="tel:+998915550808" class="alt-method">
                        <i class="fas fa-phone"></i>
                        <span>Call: +998 91 555 08 08</span>
                    </a>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn--outline" id="contact-error-close">
                    Close
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('contact.js') }}"></script>
<script src="{{ asset('contact_form_handler.js') }}"></script>
@endpush
