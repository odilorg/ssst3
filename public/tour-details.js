/**
 * TOUR DETAILS PAGE - JAVASCRIPT
 * Jahongir Travel
 *
 * Features:
 * - Form validation with aria-live feedback
 * - Dynamic price calculation
 * - Skeleton loader management
 * - Navigation toggle
 * - GA4 event tracking
 * - FAQ accordion
 */

'use strict';

// =============================================================================
// 1. FORM VALIDATION
// =============================================================================

const bookingForm = document.getElementById('booking-form');
const dateInput = document.getElementById('tour-date');
const guestsInput = document.getElementById('tour-guests'); // Note: HTML uses 'tour-guests' not 'guests'

// Error containers
const dateError = document.getElementById('date-error');
const guestsError = document.getElementById('guests-error');

// aria-live status region
const bookingStatus = document.getElementById('booking-status');

/**
 * Validation Functions
 */
function validateDate(value) {
  if (!value) return 'Please select a tour date';

  const selectedDate = new Date(value);
  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  tomorrow.setHours(0, 0, 0, 0);

  if (selectedDate < tomorrow) {
    return 'Tour must be booked at least 24 hours in advance';
  }
  return '';
}

function validateGuests(value) {
  const num = parseInt(value);
  if (!value || isNaN(num) || num < 1) {
    return 'Please select number of guests';
  }
  if (num > 10) {
    return 'Maximum 10 guests per tour';
  }
  return '';
}

// Note: Name, email, phone validation not needed yet - those fields don't exist in current form
// The form only has date + guests + check availability buttons
// Add these validators when contact fields are added to the booking form

/**
 * Show/hide error in UI
 */
function showError(errorElement, message) {
  if (!errorElement) return;

  errorElement.textContent = message;
  errorElement.style.display = message ? 'block' : 'none';
}

/**
 * Validate single field
 */
function validateField(input, validator, errorElement) {
  if (!input) return true;

  const errorMsg = validator(input.value);
  showError(errorElement, errorMsg);

  if (errorMsg) {
    input.setAttribute('aria-invalid', 'true');
  } else {
    input.removeAttribute('aria-invalid');
  }

  return !errorMsg; // return true if valid
}

/**
 * Attach blur event listeners for validation
 */
function initFormValidation() {
  dateInput?.addEventListener('blur', () => validateField(dateInput, validateDate, dateError));
  guestsInput?.addEventListener('blur', () => validateField(guestsInput, validateGuests, guestsError));

  // Form submission (currently no submit button - using check availability instead)
  // This will be used when actual booking form with contact fields is added
  bookingForm?.addEventListener('submit', (e) => {
    e.preventDefault();

    // Validate fields
    const isDateValid = validateField(dateInput, validateDate, dateError);
    const isGuestsValid = validateField(guestsInput, validateGuests, guestsError);

    const isFormValid = isDateValid && isGuestsValid;

    if (!isFormValid) {
      if (bookingStatus) {
        bookingStatus.textContent = 'Please fix the errors above before submitting';
      }
      // Focus first invalid field
      [dateInput, guestsInput].find(input =>
        input?.getAttribute('aria-invalid') === 'true'
      )?.focus();
      return;
    }

    // Success - announce to screen readers
    if (bookingStatus) {
      bookingStatus.textContent = 'Submitting your booking request...';
    }

    // Track GA4 event
    gtagEvent('booking_form_submit', {
      tour_id: 'samarkand-city-tour',
      guests: guestsInput?.value,
      tour_date: dateInput?.value
    });

    // TODO: Send to backend API or show booking modal
    console.log('Form valid, ready to proceed:', {
      date: dateInput?.value,
      guests: guestsInput?.value
    });

    // Simulate success (remove in production)
    setTimeout(() => {
      if (bookingStatus) {
        bookingStatus.textContent = 'Thank you! Your booking request has been received.';
      }
      bookingForm?.reset();
      updatePrice(); // Reset price display
    }, 1000);
  });
}

// =============================================================================
// 2. DYNAMIC PRICE CALCULATION
// =============================================================================

// Price display elements and BASE_PRICE will be initialized when DOM is ready
let breakdownGuests;
let breakdownSubtotal;
let breakdownTotal;
let breakdownUnitPrice;
let BASE_PRICE = 0;
let SHOW_PRICE = true;

/**
 * Initialize BASE_PRICE from DOM or JSON data
 */
function initializePrice() {
  // Get DOM elements
  breakdownGuests = document.querySelector('.breakdown-guests');
  breakdownSubtotal = document.querySelector('[data-subtotal]');
  breakdownTotal = document.querySelector('[data-total]');
  breakdownUnitPrice = document.querySelector('.breakdown-unit-price');

  // Try to read from data attribute first (server-rendered)
  console.log('breakdownUnitPrice element:', breakdownUnitPrice);
  if (breakdownUnitPrice) {
    const attrPrice = breakdownUnitPrice.getAttribute('data-unit-price');
    console.log('Data attribute price:', attrPrice);
    const parsed = parseFloat(attrPrice);
    console.log('Parsed price:', parsed);
    if (parsed && !isNaN(parsed)) {
      BASE_PRICE = parsed;
    }
  }

  console.log('BASE_PRICE after attribute:', BASE_PRICE);

  // Always read tour-data JSON for showPrice flag and fallback price
  const tourDataEl = document.getElementById('tour-data');
  console.log('tourDataEl:', tourDataEl);
  if (tourDataEl) {
    const jsonText = tourDataEl.textContent.trim();
    console.log('JSON text:', jsonText);
    try {
      const tourData = JSON.parse(jsonText);
      console.log('Parsed tour data:', tourData);

      // Read showPrice flag (always)
      SHOW_PRICE = tourData.showPrice !== false;

      // Only use JSON price if we didn't get it from attribute
      if (BASE_PRICE === 0) {
        console.log('pricePerPerson value:', tourData.pricePerPerson);
        BASE_PRICE = parseFloat(tourData.pricePerPerson) || 0;
      }
    } catch (e) {
      console.error('Failed to parse tour data:', e);
    }
  } else {
    console.error('tour-data element not found!');
  }

  console.log('Final BASE_PRICE:', BASE_PRICE);
  console.log('SHOW_PRICE:', SHOW_PRICE);
}

/**
 * Update price based on guest count
 */
function updatePrice() {
  const guests = parseInt(guestsInput?.value || 1);

  // Check if price should be displayed
  const shouldShowPrice = SHOW_PRICE && BASE_PRICE > 0;

  if (!shouldShowPrice) {
    // Hide price, show contact message
    if (breakdownUnitPrice) {
      breakdownUnitPrice.textContent = 'Contact us';
      breakdownUnitPrice.setAttribute('data-unit-price', '0');
    }

    if (breakdownSubtotal) {
      breakdownSubtotal.textContent = 'Please contact us';
      breakdownSubtotal.setAttribute('data-subtotal', '0');
    }

    if (breakdownTotal) {
      breakdownTotal.textContent = 'Please contact us';
      breakdownTotal.setAttribute('data-total', '0');
    }

    // Update booking status for screen readers
    if (bookingStatus) {
      bookingStatus.textContent = 'Price available upon request. Please contact us for pricing.';
    }

    return; // Exit early
  }

  // Normal price calculation when price should be shown
  const total = BASE_PRICE * guests;

  // Update unit price display
  if (breakdownUnitPrice) {
    breakdownUnitPrice.textContent = `$${BASE_PRICE.toFixed(2)}`;
    breakdownUnitPrice.setAttribute('data-unit-price', BASE_PRICE.toFixed(2));
  }

  // Update price breakdown display
  if (breakdownGuests) {
    breakdownGuests.textContent = `${guests} guest${guests !== 1 ? 's' : ''}`;
    breakdownGuests.setAttribute('data-guests', guests);
  }

  if (breakdownSubtotal) {
    breakdownSubtotal.textContent = `$${total.toFixed(2)}`;
    breakdownSubtotal.setAttribute('data-subtotal', total.toFixed(2));
  }

  if (breakdownTotal) {
    breakdownTotal.textContent = `$${total.toFixed(2)}`;
    breakdownTotal.setAttribute('data-total', total.toFixed(2));
  }

  // Update booking status for screen readers (only if guests > 1 to avoid spam)
  if (bookingStatus && guests > 1) {
    bookingStatus.textContent = `Total price updated: $${total} for ${guests} guests`;
  }
}

/**
 * Initialize price calculation
 */
function initPriceCalculation() {
  initializePrice(); // Initialize BASE_PRICE from DOM
  guestsInput?.addEventListener('change', updatePrice);
  updatePrice(); // Set initial price
}

// =============================================================================
// 3. SKELETON LOADER
// =============================================================================

/**
 * Hide skeleton loader, show gallery
 */
function initGalleryLoader() {
  const skeleton = document.querySelector('.tour-hero__skeleton');
  const gallery = document.querySelector('.tour-hero__gallery');

  if (skeleton && gallery) {
    // Hide skeleton
    skeleton.classList.add('is-hidden');

    // Show gallery
    gallery.classList.remove('is-hidden');
  }
}

// =============================================================================
// 4. NAVIGATION TOGGLE
// =============================================================================

/**
 * Initialize mobile navigation toggle
 */
function initNavigationToggle() {
  const navToggle = document.getElementById('navToggle');
  const navMenu = document.getElementById('navMenu');

  if (!navToggle || !navMenu) return;

  navToggle.addEventListener('click', () => {
    const isExpanded = navToggle.getAttribute('aria-expanded') === 'true';

    // Toggle aria-expanded
    navToggle.setAttribute('aria-expanded', !isExpanded);

    // Toggle menu visibility
    navMenu.classList.toggle('is-open');

    // Toggle body scroll lock on mobile
    if (window.innerWidth < 768) {
      document.body.style.overflow = !isExpanded ? 'hidden' : '';
    }

    // Focus first menu link when opening
    if (!isExpanded) {
      navMenu.querySelector('a')?.focus();
    }
  });

  // Close menu on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && navToggle.getAttribute('aria-expanded') === 'true') {
      navToggle.click();
      navToggle.focus();
    }
  });

  // Close menu when clicking outside
  document.addEventListener('click', (e) => {
    if (navToggle.getAttribute('aria-expanded') === 'true' &&
        !navMenu.contains(e.target) &&
        !navToggle.contains(e.target)) {
      navToggle.click();
    }
  });
}

// Initialize navigation toggle on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initNavigationToggle);
} else {
  initNavigationToggle();
}

// =============================================================================
// 5. GA4 EVENT TRACKING
// =============================================================================

/**
 * Send event to Google Analytics 4
 */
function gtagEvent(eventName, params) {
  if (typeof gtag === 'function') {
    gtag('event', eventName, params);
  } else {
    // Fallback for development/testing
    console.log('GA4 Event:', eventName, params);
  }
}

/**
 * Initialize GA4 event tracking
 */
function initGA4Tracking() {
  // Track Check Availability
  document.getElementById('check-availability')?.addEventListener('click', () => {
    gtagEvent('check_availability', {
      tour_id: 'samarkand-city-tour',
      tour_name: 'Samarkand City Tour',
      price: BASE_PRICE
    });
  });

  // Track Request Booking
  document.getElementById('request-booking')?.addEventListener('click', () => {
    const currentPrice = parseInt(breakdownTotal?.getAttribute('data-total') || BASE_PRICE);
    gtagEvent('request_booking', {
      tour_id: 'samarkand-city-tour',
      tour_name: 'Samarkand City Tour',
      price: currentPrice
    });
  });

  // Track WhatsApp Clicks
  document.querySelectorAll('.btn--whatsapp, a[href*="whatsapp"]').forEach(btn => {
    btn.addEventListener('click', () => {
      gtagEvent('whatsapp_click', {
        tour_id: 'samarkand-city-tour',
        source: 'tour_details_page'
      });
    });
  });

  // Track Add-on Selection (Extra Services)
  document.querySelectorAll('input[name^="addon"]').forEach(checkbox => {
    checkbox.addEventListener('change', (e) => {
      gtagEvent(e.target.checked ? 'addon_selected' : 'addon_deselected', {
        tour_id: 'samarkand-city-tour',
        addon_name: e.target.value || e.target.id
      });
    });
  });
}

// =============================================================================
// 6. FAQ ACCORDION
// =============================================================================

/**
 * Initialize FAQ accordion tracking
 */
function initFAQAccordion() {
  const faqItems = document.querySelectorAll('.faq-item details');

  faqItems.forEach(detail => {
    detail.addEventListener('toggle', () => {
      if (detail.open) {
        // Track which FAQ was opened
        gtagEvent('faq_opened', {
          question: detail.querySelector('summary')?.textContent.trim()
        });
      }
    });
  });
}

// =============================================================================
// 7. INITIALIZATION
// =============================================================================

/**
 * Initialize all features when DOM is ready
 */
document.addEventListener('DOMContentLoaded', () => {
  console.log('Tour Details JS: Initializing...');

  // Core features
  initFormValidation();
  initPriceCalculation();
  initGalleryLoader();

  // Interactivity
  initNavigationToggle();
  initGA4Tracking();
  initFAQAccordion();

  console.log('Tour Details JS: Ready');
});

// =============================================================================
// 8. UTILITY FUNCTIONS
// =============================================================================

/**
 * Debounce function for performance
 */
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Check if user prefers reduced motion
 */
function prefersReducedMotion() {
  return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

// =============================================================================
// SECTION NAVIGATION with Arrows & Auto-Center
// =============================================================================

/**
 * Initialize section navigation with arrow buttons and auto-centering
 */
function initSectionNavigation() {
  const scroller = document.getElementById('sectionScroller');
  const prevBtn = document.querySelector('.section-nav__btn--prev');
  const nextBtn = document.querySelector('.section-nav__btn--next');

  if (!scroller) return;

  // Get sticky offset from CSS variable
  const OFFSET = parseInt(
    getComputedStyle(document.documentElement).getPropertyValue('--sticky-offset') || '88',
    10
  );

  // Add shadow when nav is stuck (desktop only)
  const nav = document.querySelector('.section-nav');
  if (nav && window.innerWidth >= 992) {
    // Create a sentinel element to detect when nav becomes sticky
    const sentinel = document.createElement('div');
    sentinel.className = 'sticky-sentinel';
    sentinel.style.height = '1px';
    sentinel.style.visibility = 'hidden';
    nav.parentElement.insertBefore(sentinel, nav);

    const stickyObserver = new IntersectionObserver(
      ([entry]) => {
        nav.classList.toggle('is-stuck', !entry.isIntersecting);
      },
      { rootMargin: `-${OFFSET}px 0px 0px 0px`, threshold: 0 }
    );

    stickyObserver.observe(sentinel);
  }

  // Prevent horizontal scroller from trapping vertical page scroll (desktop)
  scroller.addEventListener('wheel', (e) => {
    // Only translate horizontal scroll; let vertical pass through
    if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) {
      scroller.scrollLeft += e.deltaY || e.deltaX;
      e.preventDefault();
    }
    // If vertical scroll (deltaY > deltaX), don't preventDefault - let it scroll the page
  }, { passive: false });

  // Mobile/touch: Let browser handle naturally with CSS
  // The CSS properties (overflow-y: visible, overscroll-behavior-x: contain)
  // allow vertical page scroll while enabling horizontal tab scrolling

  // Update button visibility based on scroll position
  const updateButtons = () => {
    const { scrollLeft, scrollWidth, clientWidth } = scroller;
    prevBtn.hidden = scrollLeft <= 2;
    nextBtn.hidden = scrollLeft + clientWidth >= scrollWidth - 2;
  };

  // Smooth scroll left/right
  const smoothScroll = (dir = 1) => {
    scroller.scrollBy({
      left: dir * Math.round(scroller.clientWidth * 0.7),
      behavior: 'smooth'
    });
  };

  // Show/hide arrows based on overflow
  updateButtons();
  scroller.addEventListener('scroll', updateButtons);
  window.addEventListener('resize', updateButtons);

  // Arrow button clicks
  prevBtn.addEventListener('click', () => smoothScroll(-1));
  nextBtn.addEventListener('click', () => smoothScroll(1));

  // Center link helper - only scroll the horizontal scroller, never the page
  const centerLink = (el) => {
    if (!el) return;

    // Calculate position to center the link in the scroller
    const scrollerRect = scroller.getBoundingClientRect();
    const linkRect = el.getBoundingClientRect();
    const scrollerCenter = scrollerRect.left + (scrollerRect.width / 2);
    const linkCenter = linkRect.left + (linkRect.width / 2);
    const offset = linkCenter - scrollerCenter;

    // Scroll the horizontal scroller only (not the page)
    scroller.scrollBy({
      left: offset,
      behavior: prefersReducedMotion() ? 'auto' : 'smooth'
    });
  };

  // Center active link on load
  centerLink(scroller.querySelector('.is-active'));

  // Handle link clicks - scroll to section, update active, and center
  scroller.addEventListener('click', e => {
    const a = e.target.closest('a');
    if (!a) return;

    e.preventDefault();

    // Scroll to the target section
    const targetId = a.getAttribute('href').substring(1);
    const targetSection = document.getElementById(targetId);

    if (targetSection) {
      const offsetTop = targetSection.offsetTop - OFFSET - 8; // Account for sticky header + padding
      window.scrollTo({
        top: offsetTop,
        behavior: prefersReducedMotion() ? 'auto' : 'smooth'
      });

      // Update URL hash without jumping
      if (window.history && window.history.pushState) {
        window.history.pushState(null, '', '#' + targetId);
      }
    }

    // Update active state and center in nav
    scroller.querySelectorAll('a').forEach(x => x.classList.remove('is-active'));
    a.classList.add('is-active');
    centerLink(a);
  });

  // Scroll-spy: Update active tab based on scroll position
  const links = [...scroller.querySelectorAll('a')];
  const linkMap = new Map(links.map(a => [a.getAttribute('href').slice(1), a]));

  const scrollSpyObserver = new IntersectionObserver(
    entries => {
      entries.forEach(({ isIntersecting, target }) => {
        if (!isIntersecting) return;

        // Update active state
        links.forEach(link => link.classList.remove('is-active'));
        const activeLink = linkMap.get(target.id);
        if (activeLink) {
          activeLink.classList.add('is-active');
          centerLink(activeLink);
        }
      });
    },
    {
      rootMargin: `-${OFFSET + 40}px 0px -60% 0px`,
      threshold: 0
    }
  );

  // Observe all sections that have corresponding nav links
  document.querySelectorAll('section[id]').forEach(section => {
    if (linkMap.has(section.id)) {
      scrollSpyObserver.observe(section);
    }
  });
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initSectionNavigation);
} else {
  initSectionNavigation();
}

// =============================================================================
// ITINERARY ACCORDION
// =============================================================================

/**
 * Initialize itinerary accordion - minimal implementation
 */
function initItineraryAccordion() {
  const allDetails = Array.from(document.querySelectorAll('#itinerary-list details'));

  if (!allDetails.length) return;

  // Expand all
  document.getElementById('expandAll')?.addEventListener('click', () => {
    allDetails.forEach(d => d.open = true);
  });

  // Collapse all
  document.getElementById('collapseAll')?.addEventListener('click', () => {
    allDetails.forEach(d => d.open = false);
  });

  // Deep link support - open specific stop when hash matches
  if (location.hash) {
    const targetStop = document.querySelector(location.hash + ' details');
    if (targetStop) {
      targetStop.open = true;
      setTimeout(() => {
        document.querySelector(location.hash)?.scrollIntoView({
          behavior: 'smooth',
          block: 'center'
        });
      }, 100);
    }
  }

  // Close all on mobile by default
  if (window.innerWidth < 768) {
    allDetails.forEach(d => d.open = false);
  }
}

// Initialize itinerary when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initItineraryAccordion);
} else {
  initItineraryAccordion();
}

// Export for testing (if using modules)
if (typeof module !== 'undefined' && module.exports) {
  module.exports = {
    validateDate,
    validateGuests,
    gtagEvent,
    updatePrice
  };
}

// =============================================================================
// PROGRESSIVE DISCLOSURE - 2-STEP BOOKING FORM
// =============================================================================

/**
 * Initialize progressive disclosure for booking form
 */
function initProgressiveBookingForm() {
  const checkAvailBtn = document.getElementById('check-availability');
  const step2 = document.getElementById('step-2-full-form');
  const dateInput = document.getElementById('tour-date');
  const guestsInput = document.getElementById('tour-guests');

  if (!checkAvailBtn || !step2) return;

  checkAvailBtn.addEventListener('click', () => {
    // Basic validation
    if (dateInput && !dateInput.value) {
      dateInput.focus();
      return;
    }

    if (guestsInput && !guestsInput.value) {
      guestsInput.focus();
      return;
    }

    // Show step 2
    step2.style.display = 'block';

    // Scroll to step 2 smoothly
    step2.scrollIntoView({ behavior: 'smooth', block: 'start' });

    // Track event
    if (typeof gtag === 'function') {
      gtag('event', 'check_availability_clicked', {
        tour_date: dateInput?.value,
        guests: guestsInput?.value
      });
    }
  });
}

/**
 * Initialize payment card interactions
 */
function initPaymentCardInteractions() {
  const paymentCards = document.querySelectorAll('.payment-card');

  paymentCards.forEach(card => {
    card.addEventListener('click', () => {
      // Remove selected class from all cards
      paymentCards.forEach(c => c.classList.remove('payment-card--selected'));

      // Add selected class to clicked card
      card.classList.add('payment-card--selected');

      // Check the radio button
      const radio = card.querySelector('input[type="radio"]');
      if (radio) {
        radio.checked = true;
      }
    });
  });

  // Also handle direct radio button clicks
  const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
  paymentRadios.forEach(radio => {
    radio.addEventListener('change', () => {
      // Update card selected state
      paymentCards.forEach(card => {
        const cardRadio = card.querySelector('input[type="radio"]');
        if (cardRadio === radio) {
          card.classList.add('payment-card--selected');
        } else {
          card.classList.remove('payment-card--selected');
        }
      });
    });
  });

  // Set initial state
  const checkedRadio = document.querySelector('input[name="payment_method"]:checked');
  if (checkedRadio) {
    const selectedCard = checkedRadio.closest('.payment-card');
    if (selectedCard) {
      selectedCard.classList.add('payment-card--selected');
    }
  }
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    initProgressiveBookingForm();
    initPaymentCardInteractions();
  });
} else {
  initProgressiveBookingForm();
  initPaymentCardInteractions();
}
// =============================================================================
// BOOK NOW BUTTON - SCROLL TO BOOKING FORM
// =============================================================================

/**
 * Handle "Book Now" buttons that scroll to booking form
 */
function initBookNowButtons() {
  const bookNowButtons = document.querySelectorAll('[data-scroll-to="booking-form"]');
  
  bookNowButtons.forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault();
      
      const bookingForm = document.getElementById('booking-form');
      if (!bookingForm) return;
      
      // Calculate offset (accounting for sticky header)
      const header = document.querySelector('.site-header');
      const headerHeight = header ? header.offsetHeight : 0;
      const offset = headerHeight + 20; // Add 20px padding
      
      const elementPosition = bookingForm.getBoundingClientRect().top;
      const offsetPosition = elementPosition + window.pageYOffset - offset;
      
      // Smooth scroll to booking form
      window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
      });
      
      // Focus on first input field for accessibility
      setTimeout(() => {
        const firstInput = bookingForm.querySelector('input, select, textarea');
        if (firstInput) {
          firstInput.focus();
        }
      }, 500);
      
      // Track event
      if (typeof gtag === 'function') {
        gtag('event', 'book_now_clicked', {
          button_location: button.classList.contains('mobile-cta__button') ? 'mobile_cta' : 'desktop'
        });
      }
    });
  });
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initBookNowButtons);
} else {
  initBookNowButtons();
}

/* ============================================
   QUOTE SIDEBAR FUNCTIONALITY
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
  // Quote Form Elements
  const openQuoteFormBtn = document.getElementById('open-quote-form');
  const quoteFormContainer = document.getElementById('quote-request-form-container');
  const quoteRequestForm = document.getElementById('quote-request-form');
  const quoteRequestSuccess = document.getElementById('quote-request-success');
  const askQuestionLink = document.getElementById('ask-question-link');

  // Check if we're in quote mode
  if (!openQuoteFormBtn) return;

  // Set minimum date for quote date picker
  const quoteDateInput = document.getElementById('quote-date');
  if (quoteDateInput) {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    quoteDateInput.min = tomorrow.toISOString().split('T')[0];
  }

  // Open Quote Form
  openQuoteFormBtn.addEventListener('click', function() {
    if (quoteFormContainer.style.display === 'none') {
      quoteFormContainer.style.display = 'block';
      openQuoteFormBtn.style.display = 'none';
      if (askQuestionLink) askQuestionLink.style.display = "none";

      // Pre-fill date and guests from Card 1
      const selectedDate = document.getElementById('quote-date')?.value;
      const selectedGuests = document.getElementById('quote-guests')?.value;

      if (quoteRequestForm) {
        const hiddenDate = quoteRequestForm.querySelector('input[name="selected_date"]');
        const hiddenGuests = quoteRequestForm.querySelector('input[name="guests"]');

        if (!hiddenDate && selectedDate) {
          const dateInput = document.createElement('input');
          dateInput.type = 'hidden';
          dateInput.name = 'selected_date';
          dateInput.value = selectedDate;
          quoteRequestForm.appendChild(dateInput);
        }

        if (!hiddenGuests && selectedGuests) {
          const guestsInput = document.createElement('input');
          guestsInput.type = 'hidden';
          guestsInput.name = 'guests';
          guestsInput.value = selectedGuests;
          quoteRequestForm.appendChild(guestsInput);
        }
      }

      // Focus on first input
      setTimeout(() => {
        const firstInput = quoteFormContainer.querySelector('input[type="text"]');
        if (firstInput) firstInput.focus();
      }, 100);

      // Track analytics
      if (typeof gtag === 'function') {
        gtag('event', 'quote_form_opened', {
          'event_category': 'engagement',
          'event_label': document.querySelector('input[name="tour_slug"]')?.value
        });
      }
    }
  });

  // Handle Quote Form Submission
  if (quoteRequestForm) {
    quoteRequestForm.addEventListener('submit', async function(e) {
      e.preventDefault();

      const submitBtn = document.getElementById('submit-quote-request');
      const originalText = submitBtn.innerHTML;

      // Show loading state
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<svg class="icon animate-spin" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2" stroke-dasharray="32" stroke-dashoffset="32"/></svg> Sending...';

      // Get form data
      const formData = new FormData(quoteRequestForm);
      const selectedDate = document.getElementById('quote-date')?.value;
      const selectedGuests = document.getElementById('quote-guests')?.value;

      if (selectedDate) formData.append('selected_date', selectedDate);
      if (selectedGuests) formData.append('guests', selectedGuests);

      try {
        // Submit to inquiry endpoint
        const response = await fetch('/partials/inquiries', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json',
          },
          body: formData
        });

        if (response.ok || response.status === 201) {
          // Show success state - hide form, show success message
          const formDivider = quoteFormContainer.querySelector('.quote-form-divider');
          if (formDivider) formDivider.style.display = 'none';
          quoteRequestForm.style.display = 'none';
          quoteRequestSuccess.style.display = 'block';

          // Track success
          if (typeof gtag === 'function') {
            gtag('event', 'quote_request_submitted', {
              'event_category': 'conversion',
              'event_label': formData.get('tour_slug')
            });
          }
        } else {
          throw new Error('Submission failed');
        }
      } catch (error) {
        console.error('Quote request error:', error);
        alert('Sorry, there was an error sending your request. Please try again or contact us directly.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    });
  }

  // Ask a Question Link
  if (askQuestionLink) {
    askQuestionLink.addEventListener('click', function(e) {
      e.preventDefault();

      // Open the form with notes field focused
      if (quoteFormContainer.style.display === 'none') {
        openQuoteFormBtn.click();
        setTimeout(() => {
          const notesField = document.getElementById('quote-notes');
          if (notesField) notesField.focus();
        }, 150);
      }
    });
  }

  // Scroll to included section
  const scrollToIncludedLink = document.querySelector('[data-scroll-to="included-excluded"]');
  if (scrollToIncludedLink) {
    scrollToIncludedLink.addEventListener('click', function(e) {
      e.preventDefault();
      const includedSection = document.getElementById('included-excluded') ||
                              document.querySelector('[data-section="included-excluded"]') ||
                              document.querySelector('.included-excluded-section');
      if (includedSection) {
        includedSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  }
});

/* End Quote Sidebar Functionality */

  // Reset Quote Form (send another request)
  const resetQuoteBtn = document.getElementById('reset-quote-form');
  if (resetQuoteBtn) {
    resetQuoteBtn.addEventListener('click', function() {
      const formDivider = quoteFormContainer.querySelector('.quote-form-divider');
      
      // Show form elements again
      if (formDivider) formDivider.style.display = 'block';
      quoteRequestForm.style.display = 'block';
      quoteRequestSuccess.style.display = 'none';
      
      // Reset form fields
      quoteRequestForm.reset();
      
      // Reset submit button
      const submitBtn = document.getElementById('submit-quote-request');
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<span>Send Quote Request</span><svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>';
      }
    });
  }
