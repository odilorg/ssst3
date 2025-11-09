@extends('layouts.main')

@section('title', 'About Us - Jahongir Travel | Family-Run Silk Road Tours Since 2012')
@section('meta_description', 'Meet the family behind Jahongir Travel. Since 2012, we have been crafting authentic Silk Road journeys from our home in Samarkand with local hospitality and expert care.')
@section('meta_keywords', 'About Jahongir Travel, Uzbekistan tour company, family-run tours, Samarkand tours, local tour operator')
@section('canonical', 'https://jahongirtravel.com/about')

@section('content')

    <!-- =====================================================
         HERO SECTION
         ===================================================== -->
    <section class="about-hero" aria-labelledby="about-hero-heading">
      <div class="about-hero__overlay"></div>
      <div class="container">
        <div class="about-hero__content">
          <h1 id="about-hero-heading" class="about-hero__title">About Us</h1>
          <p class="about-hero__subtitle">
            Family-run in Samarkand since 2012.<br>
            We craft authentic Silk Road journeys with local hospitality and expert care.
          </p>
        </div>
      </div>
    </section>

    <!-- =====================================================
         WHY WE ARE BEST - ICON GRID
         ===================================================== -->
    <section class="why-best">
      <div class="container">
        <h2 class="section-heading text-center">Why Travelers Choose Jahongir Travel</h2>
        <p class="text-center section-tagline">Authenticity, care, and trust in every journey.</p>

        <div class="icon-grid">
          <div class="icon-card">
            <div class="icon-card__icon">
              <i class="fas fa-home" aria-hidden="true"></i>
            </div>
            <h3 class="icon-card__title">Local family hospitality</h3>
            <p class="icon-card__text">Born and raised in Samarkand, we treat every guest like family. Flexible, human support 24/7—we reply in 24 hours (often faster).</p>
          </div>

          <div class="icon-card">
            <div class="icon-card__icon">
              <i class="fas fa-hands-helping" aria-hidden="true"></i>
            </div>
            <h3 class="icon-card__title">Artisan partnerships</h3>
            <p class="icon-card__text">Direct connections with local craftspeople, family-run guesthouses, and authentic restaurants you won't find in guidebooks.</p>
          </div>

          <div class="icon-card">
            <div class="icon-card__icon">
              <i class="fas fa-dollar-sign" aria-hidden="true"></i>
            </div>
            <h3 class="icon-card__title">Transparent pricing</h3>
            <p class="icon-card__text">Clear, upfront quotes in UZS/USD with no hidden fees. No prepayment required for most day tours.</p>
          </div>

          <div class="icon-card">
            <div class="icon-card__icon">
              <i class="fas fa-map-marked-alt" aria-hidden="true"></i>
            </div>
            <h3 class="icon-card__title">Expert local planning</h3>
            <p class="icon-card__text">We plan, host, and personally guide journeys across Uzbekistan. Every detail handled with care and attention.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         OUR STORY - TWO COLUMN
         ===================================================== -->
    <section class="our-story">
      <div class="container">
        <div class="two-col-section">
          <div class="two-col-section__content">
            <span class="eyebrow">OUR STORY</span>
            <h2 class="section-heading">It feels like family (because it is)</h2>
            <p>Our story began in the heart of Samarkand, where our family opened the first Jahongir Guest House in 2012. What started as a cozy guesthouse welcoming travelers to experience authentic Uzbek hospitality has grown into a full-service travel company, but our values remain the same.</p>
            <p>Born and raised in Samarkand, our founders dreamed of sharing their homeland's hidden treasures with the world. Every tour we craft carries the care and attention we'd give our own family. From recommending the best local restaurants to ensuring you experience authentic cultural moments, <strong>we treat every traveler as part of our extended family.</strong></p>
            <p>If you're going to visit a new place, it should feel like coming home.</p>
            <a href="#team" class="btn btn--outline-coral">Meet our team</a>
          </div>

          <div class="two-col-section__images">
            <img src="images/about/team-sunset.jpg" alt="Team silhouette at sunset" class="story-image story-image--large">
            <img src="images/about/workspace.jpg" alt="Team member planning tours" class="story-image story-image--small">
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         STATS SHOWCASE
         ===================================================== -->
    <section class="stats-showcase">
      <div class="container">
        <div class="stats-showcase__intro">
          <h2 class="section-heading text-center">Over a decade of journeys that connect cultures</h2>
          <p class="text-center stats-showcase__subtitle">Since 2012, our family has guided travelers through the heart of Uzbekistan and beyond, building lasting relationships and unforgettable memories across the Silk Road.</p>
        </div>

        <div class="stat-cards">
          <div class="stat-card">
            <div class="stat-card__number">10,000+</div>
            <div class="stat-card__label">Happy customers</div>
          </div>

          <div class="stat-card">
            <div class="stat-card__number">5,000+</div>
            <div class="stat-card__label">Tours completed</div>
          </div>

          <div class="stat-card">
            <div class="stat-card__number">12+</div>
            <div class="stat-card__label">Years of experience</div>
          </div>

          <div class="stat-card">
            <div class="stat-card__number">200+</div>
            <div class="stat-card__label">Local partners</div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         LEADERSHIP TEAM
         ===================================================== -->
    <section class="team-section" id="team">
      <div class="container">
        <div class="team-section__header">
          <span class="eyebrow">LEADERSHIP TEAM</span>
          <h2 class="section-heading text-center">Our people are your people, too</h2>
          <p class="text-center team-section__subtitle">The dedicated team guiding your Uzbekistan journey to success</p>
        </div>

        <div class="team-grid">
          <div class="team-member">
            <img src="images/about/team-member-1.jpg" alt="Jahongir Karimov" class="team-member__photo">
            <h3 class="team-member__name">Jahongir Karimov</h3>
            <p class="team-member__position">Founder & CEO</p>
          </div>

          <div class="team-member">
            <img src="images/about/team-member-2.jpg" alt="Dilshod Rahimov" class="team-member__photo">
            <h3 class="team-member__name">Dilshod Rahimov</h3>
            <p class="team-member__position">Head of Operations</p>
          </div>

          <div class="team-member">
            <img src="images/about/team-member-3.jpg" alt="Madina Sultanova" class="team-member__photo">
            <h3 class="team-member__name">Madina Sultanova</h3>
            <p class="team-member__position">Chief Experience Officer</p>
          </div>
        </div>

        <div class="team-section__cta">
          <a href="/contact" class="btn btn--outline-coral">Meet our team</a>
        </div>
      </div>
    </section>

    <!-- =====================================================
         TESTIMONIALS
         ===================================================== -->
    <section class="testimonials-section">
      <div class="container">
        <h2 class="section-heading text-center">People love us</h2>

        <div class="tripadvisor-badge">
          <img src="images/tripadvisor-logo.svg" alt="TripAdvisor" class="tripadvisor-badge__logo">
          <div class="tripadvisor-badge__rating">
            <div class="stars">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <span class="tripadvisor-badge__count">1,000+ reviews</span>
          </div>
        </div>

        <div class="testimonial-grid">
          <div class="testimonial-card">
            <div class="testimonial-card__header">
              <div class="stars stars--green">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
              </div>
              <span class="testimonial-card__date">15 Jul 2025</span>
            </div>
            <h3 class="testimonial-card__title">Unforgettable Silk Road Experience</h3>
            <p class="testimonial-card__text">Jahongir Travel made our Uzbekistan dream come true! From Samarkand's Registan to Bukhara's ancient streets, every detail was perfectly arranged. Our guide was knowledgeable and the local connections made it truly authentic.</p>
            <div class="testimonial-card__author">
              <img src="images/testimonials/author-1.jpg" alt="Sarah Mitchell" class="testimonial-card__avatar">
              <div class="testimonial-card__author-info">
                <strong class="testimonial-card__author-name">Sarah Mitchell</strong>
                <span class="testimonial-card__author-location">United Kingdom</span>
              </div>
            </div>
          </div>

          <div class="testimonial-card">
            <div class="testimonial-card__header">
              <div class="stars stars--green">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
              </div>
              <span class="testimonial-card__date">25 Jun 2025</span>
            </div>
            <h3 class="testimonial-card__title">Professional and Personalized Service</h3>
            <p class="testimonial-card__text">Best travel agency for Central Asia! They customized our 10-day tour to include hidden gems we never would have found. The hotels were excellent and transfers were seamless. Highly recommend for first-timers to Uzbekistan!</p>
            <div class="testimonial-card__author">
              <img src="images/testimonials/author-2.jpg" alt="Michael Chen" class="testimonial-card__avatar">
              <div class="testimonial-card__author-info">
                <strong class="testimonial-card__author-name">Michael Chen</strong>
                <span class="testimonial-card__author-location">United States</span>
              </div>
            </div>
          </div>

          <div class="testimonial-card">
            <div class="testimonial-card__header">
              <div class="stars stars--green">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
              </div>
              <span class="testimonial-card__date">8 Apr 2025</span>
            </div>
            <h3 class="testimonial-card__title">Exceeded All Expectations</h3>
            <p class="testimonial-card__text">From Khiva to the Fergana Valley, every moment was magical. The family-run guesthouses Jahongir arranged gave us incredible cultural insight. Their 24/7 support made us feel safe throughout our journey. Worth every penny!</p>
            <div class="testimonial-card__author">
              <img src="images/testimonials/author-3.jpg" alt="Emma Rodriguez" class="testimonial-card__avatar">
              <div class="testimonial-card__author-info">
                <strong class="testimonial-card__author-name">Emma Rodriguez</strong>
                <span class="testimonial-card__author-location">Australia</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         HELP / CONTACT OPTIONS
         ===================================================== -->
    <section class="help-section">
      <div class="container">
        <h2 class="section-heading text-center">Let's plan your perfect Silk Road adventure together</h2>
        <p class="text-center help-section__subtitle">Not sure where to start? Our local experts are here to guide you every step of the way.</p>

        <!-- Primary CTA -->
        <div class="help-primary-cta">
          <a href="/contact" class="btn btn--primary btn--large">Plan my trip</a>
          <p class="help-primary-cta__note">We'll reply within 24 hours with a personalized itinerary</p>
        </div>

        <!-- Secondary Contact Options -->
        <div class="help-grid">
          <div class="help-option">
            <div class="help-option__icon">
              <i class="fas fa-comments" aria-hidden="true"></i>
            </div>
            <h3 class="help-option__title">Chat with us</h3>
            <p class="help-option__text">Quick questions? Chat instantly with our team. We're online 24/7.</p>
            <a href="#" class="help-option__link">Start chat <i class="fas fa-arrow-right"></i></a>
          </div>

          <div class="help-option">
            <div class="help-option__icon">
              <i class="fas fa-phone-alt" aria-hidden="true"></i>
            </div>
            <h3 class="help-option__title">Call now</h3>
            <p class="help-option__text">Speak with a local expert about your perfect Silk Road journey.</p>
            <a href="tel:+998991234567" class="help-option__link">+998 99 123 4567 <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </section>

@endsection
