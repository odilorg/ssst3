/**
 * Reviews Carousel - Swiper.js initialization for homepage reviews section
 *
 * Features:
 * - Auto-play with 6-second delay
 * - Pause on hover
 * - Responsive: 1 slide (mobile), 2 slides (tablet), 3 slides (desktop)
 * - Navigation arrows and pagination dots
 * - Loop for continuous scrolling
 */

document.addEventListener('DOMContentLoaded', function() {
  // Check if reviews swiper exists on the page
  const reviewsSwiperEl = document.querySelector('.reviews-swiper');

  if (reviewsSwiperEl) {
    const reviewsSwiper = new Swiper('.reviews-swiper', {
      // Number of slides per view
      slidesPerView: 1,
      spaceBetween: 20,

      // Loop mode for continuous scrolling
      loop: true,

      // Centered slides
      centeredSlides: false,

      // Prevent showing partial slides
      watchSlidesProgress: true,
      watchSlidesVisibility: true,

      // Auto-play configuration
      autoplay: {
        delay: 6000,
        disableOnInteraction: false,
        pauseOnMouseEnter: true
      },

      // Pagination dots
      pagination: {
        el: '.reviews-swiper-pagination',
        clickable: true,
        dynamicBullets: false
      },

      // Navigation arrows
      navigation: {
        nextEl: '.reviews-swiper-next',
        prevEl: '.reviews-swiper-prev'
      },

      // Responsive breakpoints
      breakpoints: {
        // Mobile (>=640px)
        640: {
          slidesPerView: 1,
          spaceBetween: 20
        },
        // Tablet (>=768px)
        768: {
          slidesPerView: 2,
          spaceBetween: 24
        },
        // Desktop (>=1024px)
        1024: {
          slidesPerView: 3,
          spaceBetween: 30
        }
      },

      // Smooth animations
      speed: 600,

      // Accessibility
      a11y: {
        prevSlideMessage: 'Previous review',
        nextSlideMessage: 'Next review',
        paginationBulletMessage: 'Go to review {{index}}'
      }
    });

    console.log('[Reviews Carousel] Initialized successfully with', reviewsSwiper.slides.length, 'reviews');
  }
});
