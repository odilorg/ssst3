/**
 * JAHONGIR TRAVEL - Main JavaScript
 * Purpose: Navigation, counter animations, and interactions
 */

// Wrap in IIFE to avoid global scope pollution and redeclaration errors
(function() {
'use strict';

// ==========================================
// 1. STICKY NAVIGATION ON SCROLL
// ==========================================
const nav = document.querySelector('.nav');
let lastScrollY = window.scrollY;

window.addEventListener('scroll', () => {
  if (window.scrollY > 100) {
    nav.classList.add('nav--sticky');
  } else {
    nav.classList.remove('nav--sticky');
  }
});

// ==========================================
// 2. MOBILE MENU TOGGLE
// ==========================================
const navToggle = document.getElementById('navToggle');
const navMenu = document.getElementById('navMenu');

if (navToggle && navMenu) {
  navToggle.addEventListener('click', () => {
    const isExpanded = navToggle.getAttribute('aria-expanded') === 'true';

    // Toggle aria-expanded
    navToggle.setAttribute('aria-expanded', !isExpanded);

    // Toggle menu visibility
    navMenu.classList.toggle('nav__menu--open');

    // Prevent body scroll when menu is open
    document.body.style.overflow = !isExpanded ? 'hidden' : '';
  });

  // Close menu when clicking on a link
  navMenu.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      navToggle.setAttribute('aria-expanded', 'false');
      navMenu.classList.remove('nav__menu--open');
      document.body.style.overflow = '';
    });
  });

  // Close menu when clicking outside
  document.addEventListener('click', (e) => {
    if (!nav.contains(e.target) && navMenu.classList.contains('nav__menu--open')) {
      navToggle.setAttribute('aria-expanded', 'false');
      navMenu.classList.remove('nav__menu--open');
      document.body.style.overflow = '';
    }
  });
}

// ==========================================
// 3. COUNTER ANIMATION (Intersection Observer)
// ==========================================
const animateCounter = (element, target, duration = 2000) => {
  const start = 0;
  const increment = target / (duration / 16); // 60 FPS
  let current = start;

  const timer = setInterval(() => {
    current += increment;
    if (current >= target) {
      element.textContent = target.toLocaleString();
      clearInterval(timer);
    } else {
      element.textContent = Math.floor(current).toLocaleString();
    }
  }, 16);
};

const statsObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const counters = entry.target.querySelectorAll('.stat__number');

      counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        animateCounter(counter, target);
      });

      // Unobserve after animation (run only once)
      statsObserver.unobserve(entry.target);
    }
  });
}, {
  threshold: 0.5 // Trigger when 50% visible
});

// Observe stats section
const statsSection = document.querySelector('.hero__stats');
if (statsSection) {
  statsObserver.observe(statsSection);
}

// ==========================================
// 4. SMOOTH SCROLL FOR ANCHOR LINKS
// ==========================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    const href = this.getAttribute('href');

    // Only prevent default if it's not just "#"
    if (href !== '#' && href !== '') {
      e.preventDefault();
      const target = document.querySelector(href);

      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    }
  });
});

// ==========================================
// 5. PAGE LOAD PERFORMANCE TRACKING (OPTIONAL)
// ==========================================
window.addEventListener('load', () => {
  // Log Core Web Vitals (optional - for development)
  if ('performance' in window && 'getEntriesByType' in performance) {
    const paint = performance.getEntriesByType('paint');
    const navigation = performance.getEntriesByType('navigation')[0];

    console.log('Performance Metrics:');
    paint.forEach(entry => {
      console.log(`${entry.name}: ${entry.startTime.toFixed(2)}ms`);
    });

    if (navigation) {
      console.log(`DOM Content Loaded: ${navigation.domContentLoadedEventEnd.toFixed(2)}ms`);
      console.log(`Page Load Complete: ${navigation.loadEventEnd.toFixed(2)}ms`);
    }
  }
});

// ==========================================
// 6. FOOTER - LOCALE SWITCHER
// ==========================================
const localeBtn = document.querySelector('.footer-locale');

if (localeBtn) {
  localeBtn.addEventListener('click', () => {
    const expanded = localeBtn.getAttribute('aria-expanded') === 'true';
    localeBtn.setAttribute('aria-expanded', !expanded);

    // TODO: Show dropdown menu with language/currency options
    // This will be connected to Laravel backend in Phase 2
    console.log('Locale switcher clicked - awaiting backend integration');
  });

  // Close on escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && localeBtn.getAttribute('aria-expanded') === 'true') {
      localeBtn.setAttribute('aria-expanded', 'false');
    }
  });
}

// ==========================================
// 7. FOOTER - DYNAMIC COPYRIGHT YEAR
// ==========================================
const copyrightEl = document.querySelector('.footer-bottom__copyright');
if (copyrightEl) {
  const currentYear = new Date().getFullYear();
  copyrightEl.textContent = `© ${currentYear} Jahongir Travel. All rights reserved.`;
}

// ==========================================
// 8. FOOTER - MOBILE ACCORDION ANALYTICS
// ==========================================
document.querySelectorAll('.footer-accordion__item').forEach((accordionItem) => {
  accordionItem.addEventListener('toggle', () => {
    if (accordionItem.open) {
      const sectionName = accordionItem.querySelector('.footer-accordion__summary').textContent.trim();

      // Google Analytics 4 tracking (if gtag is available)
      if (typeof gtag !== 'undefined') {
        gtag('event', 'footer_accordion_open', {
          'event_category': 'engagement',
          'event_label': sectionName
        });
      }

      // Console log for development
      console.log(`Footer accordion opened: ${sectionName}`);
    }
  });
});

console.log('Jahongir Travel - JavaScript Loaded Successfully ✓');

})(); // End of IIFE
