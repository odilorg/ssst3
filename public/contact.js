/* ======================================================
   CONTACT FORM - VALIDATION & ENHANCEMENTS
   ====================================================== */

(function() {
  'use strict';

  // ========================================
  // DOM Elements
  // ========================================
  const form = document.getElementById('contactForm');
  const nameInput = document.getElementById('name');
  const emailInput = document.getElementById('email');
  const phoneInput = document.getElementById('phone');
  const messageInput = document.getElementById('message');

  // Check if elements exist before proceeding
  if (!form || !nameInput || !emailInput || !messageInput) {
    console.log('Contact form not found on this page');
    return;
  }

  // ========================================
  // Auto-expanding Textarea
  // ========================================
  messageInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
  });

  // ========================================
  // Character Counter
  // ========================================
  messageInput.addEventListener('input', function() {
    const maxChars = 2000;
    if (this.value.length > maxChars) {
      this.value = this.value.substring(0, maxChars);
    }
  });

  console.log('Contact form enhancements initialized');

})();

/* ======================================================
   FAQ ACCORDION
   ====================================================== */

(function() {
  'use strict';

  const faqItems = document.querySelectorAll('.faq-item');

  faqItems.forEach(item => {
    const question = item.querySelector('.faq-item__question');
    
    if (question) {
      question.addEventListener('click', function() {
        // Optional: Close other items for accordion behavior
        // faqItems.forEach(otherItem => {
        //   if (otherItem !== item && otherItem.open) {
        //     otherItem.open = false;
        //   }
        // });
      });
    }
  });

})();

/* ======================================================
   SCROLL ANIMATIONS
   ====================================================== */

(function() {
  'use strict';

  const animatedElements = document.querySelectorAll('.animate-on-scroll');

  if (!animatedElements.length) return;

  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  animatedElements.forEach(element => {
    observer.observe(element);
  });

})();

/* ======================================================
   ANALYTICS TRACKING
   ====================================================== */

(function() {
  'use strict';

  function trackEvent(category, action, label) {
    // Google Analytics 4 (gtag.js)
    if (typeof gtag !== 'undefined') {
      gtag('event', action, {
        event_category: category,
        event_label: label
      });
    }

    // Google Analytics Universal (analytics.js)
    if (typeof ga !== 'undefined') {
      ga('send', 'event', category, action, label);
    }

    console.log('Event tracked:', category, action, label);
  }

  // Track phone link clicks
  const phoneLinks = document.querySelectorAll('a[href^="tel:"]');
  phoneLinks.forEach(link => {
    link.addEventListener('click', function() {
      trackEvent('Contact', 'phone_click', this.href.replace('tel:', ''));
    });
  });

  // Track email link clicks
  const emailLinks = document.querySelectorAll('a[href^="mailto:"]');
  emailLinks.forEach(link => {
    link.addEventListener('click', function() {
      trackEvent('Contact', 'email_click', this.href.replace('mailto:', ''));
    });
  });

  // Track WhatsApp button clicks
  const whatsappButton = document.querySelector('.whatsapp-float');
  if (whatsappButton) {
    whatsappButton.addEventListener('click', function() {
      trackEvent('Contact', 'whatsapp_click', 'Floating Button');
    });
  }

  // Track FAQ interactions
  const faqItems = document.querySelectorAll('.faq-item');
  faqItems.forEach((item, index) => {
    item.addEventListener('toggle', function() {
      if (this.open) {
        const questionEl = this.querySelector('.faq-item__question span');
        const question = questionEl ? questionEl.textContent : 'Unknown';
        trackEvent('FAQ', 'faq_expand', 'Q' + (index + 1) + ': ' + question);
      }
    });
  });

})();
