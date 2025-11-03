/* ======================================================
   CONTACT FORM VALIDATION & SUBMISSION
   ====================================================== */

(function() {
  'use strict';

  // ========================================
  // DOM Elements
  // ========================================
  const form = document.getElementById('contactForm');
  const firstNameInput = document.getElementById('firstName');
  const lastNameInput = document.getElementById('lastName');
  const emailInput = document.getElementById('email');
  const phoneInput = document.getElementById('phone');
  const messageInput = document.getElementById('message');
  const newsletterCheckbox = document.getElementById('newsletter');
  const formSuccess = document.getElementById('formSuccess');
  const formError = document.getElementById('formError');

  // ========================================
  // Validation Patterns
  // ========================================
  const patterns = {
    name: /^[a-zA-Z\s'-]{2,50}$/,
    email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    phone: /^[\d\s\-\+\(\)]{10,20}$/,
    message: /^.{10,1000}$/s
  };

  // ========================================
  // Error Messages
  // ========================================
  const errorMessages = {
    firstName: {
      empty: 'First name is required',
      invalid: 'Please enter a valid first name (2-50 characters, letters only)'
    },
    lastName: {
      empty: 'Last name is required',
      invalid: 'Please enter a valid last name (2-50 characters, letters only)'
    },
    email: {
      empty: 'Email is required',
      invalid: 'Please enter a valid email address (e.g., name@example.com)'
    },
    phone: {
      invalid: 'Please enter a valid phone number (10-20 digits)'
    },
    message: {
      empty: 'Message is required',
      invalid: 'Message must be between 10 and 1000 characters'
    }
  };

  // ========================================
  // Validation Functions
  // ========================================

  /**
   * Show error message and style input
   */
  function showError(input, message) {
    const errorElement = document.getElementById(`${input.id}-error`);
    errorElement.textContent = message;
    input.classList.add('error');
    input.classList.remove('success');
    input.setAttribute('aria-invalid', 'true');
    return false;
  }

  /**
   * Clear error message and show success style
   */
  function showSuccess(input) {
    const errorElement = document.getElementById(`${input.id}-error`);
    errorElement.textContent = '';
    input.classList.remove('error');
    input.classList.add('success');
    input.setAttribute('aria-invalid', 'false');
    return true;
  }

  /**
   * Clear all validation styles from input
   */
  function clearValidation(input) {
    const errorElement = document.getElementById(`${input.id}-error`);
    errorElement.textContent = '';
    input.classList.remove('error', 'success');
    input.removeAttribute('aria-invalid');
  }

  /**
   * Validate first name
   */
  function validateFirstName() {
    const value = firstNameInput.value.trim();

    if (value === '') {
      return showError(firstNameInput, errorMessages.firstName.empty);
    }

    if (!patterns.name.test(value)) {
      return showError(firstNameInput, errorMessages.firstName.invalid);
    }

    return showSuccess(firstNameInput);
  }

  /**
   * Validate last name
   */
  function validateLastName() {
    const value = lastNameInput.value.trim();

    if (value === '') {
      return showError(lastNameInput, errorMessages.lastName.empty);
    }

    if (!patterns.name.test(value)) {
      return showError(lastNameInput, errorMessages.lastName.invalid);
    }

    return showSuccess(lastNameInput);
  }

  /**
   * Validate email
   */
  function validateEmail() {
    const value = emailInput.value.trim();

    if (value === '') {
      return showError(emailInput, errorMessages.email.empty);
    }

    if (!patterns.email.test(value)) {
      return showError(emailInput, errorMessages.email.invalid);
    }

    return showSuccess(emailInput);
  }

  /**
   * Validate phone (optional field)
   */
  function validatePhone() {
    const value = phoneInput.value.trim();

    // Phone is optional, so empty is OK
    if (value === '') {
      clearValidation(phoneInput);
      return true;
    }

    if (!patterns.phone.test(value)) {
      return showError(phoneInput, errorMessages.phone.invalid);
    }

    return showSuccess(phoneInput);
  }

  /**
   * Validate message
   */
  function validateMessage() {
    const value = messageInput.value.trim();

    if (value === '') {
      return showError(messageInput, errorMessages.message.empty);
    }

    if (!patterns.message.test(value)) {
      return showError(messageInput, errorMessages.message.invalid);
    }

    return showSuccess(messageInput);
  }

  /**
   * Validate entire form
   */
  function validateForm() {
    const isFirstNameValid = validateFirstName();
    const isLastNameValid = validateLastName();
    const isEmailValid = validateEmail();
    const isPhoneValid = validatePhone();
    const isMessageValid = validateMessage();

    return isFirstNameValid && isLastNameValid && isEmailValid && isPhoneValid && isMessageValid;
  }

  // ========================================
  // Real-time Validation (on blur)
  // ========================================

  firstNameInput.addEventListener('blur', validateFirstName);
  lastNameInput.addEventListener('blur', validateLastName);
  emailInput.addEventListener('blur', validateEmail);
  phoneInput.addEventListener('blur', validatePhone);
  messageInput.addEventListener('blur', validateMessage);

  // ========================================
  // Clear validation on input (optional, improves UX)
  // ========================================

  function setupInputListener(input) {
    input.addEventListener('input', function() {
      if (input.classList.contains('error')) {
        clearValidation(input);
      }
    });
  }

  setupInputListener(firstNameInput);
  setupInputListener(lastNameInput);
  setupInputListener(emailInput);
  setupInputListener(phoneInput);
  setupInputListener(messageInput);

  // ========================================
  // Form Submission
  // ========================================

  form.addEventListener('submit', async function(e) {
    e.preventDefault();

    // Hide any previous messages
    formSuccess.classList.remove('show');
    formError.classList.remove('show');

    // Validate all fields
    const isValid = validateForm();

    if (!isValid) {
      // Show error message
      formError.classList.add('show');

      // Scroll to first error
      const firstError = form.querySelector('.error');
      if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstError.focus();
      }

      return;
    }

    // Collect form data
    const formData = {
      firstName: firstNameInput.value.trim(),
      lastName: lastNameInput.value.trim(),
      email: emailInput.value.trim(),
      phone: phoneInput.value.trim(),
      message: messageInput.value.trim(),
      newsletter: newsletterCheckbox.checked,
      timestamp: new Date().toISOString()
    };

    try {
      // Disable submit button to prevent double submission
      const submitBtn = form.querySelector('.form-submit');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Sending...';

      // TODO: Replace with your actual API endpoint
      // Example: await fetch('/api/contact', {
      //   method: 'POST',
      //   headers: { 'Content-Type': 'application/json' },
      //   body: JSON.stringify(formData)
      // });

      // Simulate API call (remove this in production)
      await simulateApiCall(formData);

      // Show success message
      formSuccess.classList.add('show');

      // Reset form
      form.reset();

      // Clear all validation styles
      [firstNameInput, lastNameInput, emailInput, phoneInput, messageInput].forEach(input => {
        clearValidation(input);
      });

      // Scroll to success message
      formSuccess.scrollIntoView({ behavior: 'smooth', block: 'center' });

      // Re-enable submit button
      submitBtn.disabled = false;
      submitBtn.textContent = 'Send Message';

      // Hide success message after 10 seconds
      setTimeout(() => {
        formSuccess.classList.remove('show');
      }, 10000);

      // Track form submission (Google Analytics, etc.)
      if (typeof gtag !== 'undefined') {
        gtag('event', 'form_submission', {
          event_category: 'Contact',
          event_label: 'Contact Form Submitted'
        });
      }

    } catch (error) {
      console.error('Form submission error:', error);

      // Show error message
      formError.classList.add('show');

      // Re-enable submit button
      const submitBtn = form.querySelector('.form-submit');
      submitBtn.disabled = false;
      submitBtn.textContent = 'Send Message';

      // Scroll to error message
      formError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });

  // ========================================
  // Simulate API Call (for demo purposes)
  // Remove this in production
  // ========================================

  function simulateApiCall(data) {
    return new Promise((resolve, reject) => {
      console.log('Form Data:', data);

      // Simulate network delay
      setTimeout(() => {
        // Simulate 90% success rate
        if (Math.random() > 0.1) {
          resolve({ success: true, message: 'Form submitted successfully' });
        } else {
          reject(new Error('Simulated network error'));
        }
      }, 1500);
    });
  }

  // ========================================
  // Auto-expand Textarea
  // ========================================

  messageInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
  });

  // ========================================
  // Character Counter (optional enhancement)
  // ========================================

  // Uncomment to add character counter
  /*
  const maxChars = 1000;
  const charCounter = document.createElement('div');
  charCounter.className = 'char-counter';
  charCounter.style.cssText = 'font-size: 13px; color: #6b7280; text-align: right; margin-top: 4px;';
  messageInput.parentNode.appendChild(charCounter);

  function updateCharCounter() {
    const remaining = maxChars - messageInput.value.length;
    charCounter.textContent = `${remaining} characters remaining`;
    charCounter.style.color = remaining < 100 ? '#e74c3c' : '#6b7280';
  }

  messageInput.addEventListener('input', updateCharCounter);
  updateCharCounter();
  */

  // ========================================
  // Accessibility: Announce validation errors
  // ========================================

  function announceError(message) {
    const announcement = document.createElement('div');
    announcement.setAttribute('role', 'status');
    announcement.setAttribute('aria-live', 'polite');
    announcement.className = 'sr-only';
    announcement.textContent = message;
    document.body.appendChild(announcement);
    setTimeout(() => announcement.remove(), 1000);
  }

  // ========================================
  // Phone Number Formatting (optional)
  // ========================================

  phoneInput.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');

    // Limit to 15 digits
    if (value.length > 15) {
      value = value.slice(0, 15);
    }

    // Format: +998 99 123 4567
    if (value.length > 0) {
      if (value.startsWith('998')) {
        value = '+' + value;
      }
    }

    // Don't update if user is deleting
    if (e.inputType === 'deleteContentBackward') {
      return;
    }
  });

  // ========================================
  // Prevent form submission on Enter in text inputs
  // ========================================

  [firstNameInput, lastNameInput, emailInput, phoneInput].forEach(input => {
    input.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        // Move to next input
        const inputs = Array.from(form.querySelectorAll('input, textarea, button'));
        const index = inputs.indexOf(e.target);
        if (index < inputs.length - 1) {
          inputs[index + 1].focus();
        }
      }
    });
  });

  // ========================================
  // Email validation enhancement
  // ========================================

  emailInput.addEventListener('blur', function() {
    const value = emailInput.value.trim();

    // Check for common typos in email domains
    const commonDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
    const domainPart = value.split('@')[1];

    if (domainPart && !commonDomains.includes(domainPart.toLowerCase())) {
      // You could add a subtle suggestion here if needed
      // For example: "Did you mean gmail.com?"
    }
  });

  // ========================================
  // Spam Protection: Honeypot field (optional)
  // ========================================

  // Add a hidden field that bots will fill but humans won't
  const honeypot = document.createElement('input');
  honeypot.type = 'text';
  honeypot.name = 'website';
  honeypot.style.cssText = 'position: absolute; left: -9999px; width: 1px; height: 1px;';
  honeypot.tabIndex = -1;
  honeypot.setAttribute('aria-hidden', 'true');
  form.appendChild(honeypot);

  // Check honeypot on submit
  form.addEventListener('submit', function(e) {
    if (honeypot.value !== '') {
      e.preventDefault();
      console.warn('Spam detected');
      return false;
    }
  }, true);

  // ========================================
  // Google Analytics Event Tracking
  // ========================================

  // Helper function to track events
  function trackEvent(category, action, label) {
    // Google Analytics 4 (gtag.js)
    if (typeof gtag !== 'undefined') {
      gtag('event', action, {
        event_category: category,
        event_label: label
      });
    }

    // Google Analytics Universal (analytics.js) - legacy support
    if (typeof ga !== 'undefined') {
      ga('send', 'event', category, action, label);
    }

    // Log to console for debugging
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

  // Track Google Maps link clicks
  const mapsLinks = document.querySelectorAll('a[href*="maps.google"]');
  mapsLinks.forEach(link => {
    link.addEventListener('click', function() {
      trackEvent('Contact', 'maps_click', 'Head Office Directions');
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
        const question = this.querySelector('.faq-item__question span').textContent;
        trackEvent('FAQ', 'faq_expand', `Q${index + 1}: ${question}`);
      }
    });
  });

  // Track "Didn't find your answer?" link
  const faqHelpLink = document.querySelector('.faq-help__link');
  if (faqHelpLink) {
    faqHelpLink.addEventListener('click', function() {
      trackEvent('FAQ', 'help_link_click', 'Contact Us from FAQ');
    });
  }

  // Track mobile CTA bar button clicks
  const mobileCallBtn = document.querySelector('.mobile-cta-bar__button--call');
  const mobileWhatsAppBtn = document.querySelector('.mobile-cta-bar__button--whatsapp');
  const mobileEmailBtn = document.querySelector('.mobile-cta-bar__button--email');

  if (mobileCallBtn) {
    mobileCallBtn.addEventListener('click', function() {
      trackEvent('Mobile CTA', 'call_click', 'Mobile Sticky Bar');
    });
  }

  if (mobileWhatsAppBtn) {
    mobileWhatsAppBtn.addEventListener('click', function() {
      trackEvent('Mobile CTA', 'whatsapp_click', 'Mobile Sticky Bar');
    });
  }

  if (mobileEmailBtn) {
    mobileEmailBtn.addEventListener('click', function() {
      trackEvent('Mobile CTA', 'email_click', 'Mobile Sticky Bar');
    });
  }

  // ========================================
  // Scroll Animations - Intersection Observer
  // ========================================

  // Check if browser supports Intersection Observer
  if ('IntersectionObserver' in window) {
    const observerOptions = {
      root: null, // viewport
      rootMargin: '0px 0px -50px 0px', // trigger slightly before element enters viewport
      threshold: 0.1 // trigger when 10% of element is visible
    };

    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          // Optionally stop observing after animation triggers
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    // Observe all elements with animate-on-scroll class
    const animateElements = document.querySelectorAll('.animate-on-scroll');
    animateElements.forEach(element => {
      observer.observe(element);
    });
  } else {
    // Fallback for browsers that don't support Intersection Observer
    // Just show all elements immediately
    const animateElements = document.querySelectorAll('.animate-on-scroll');
    animateElements.forEach(element => {
      element.classList.add('is-visible');
    });
  }

})();
