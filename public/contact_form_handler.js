// ========================================
// Form Submission with AJAX
// ========================================
(function() {
  'use strict';

  const form = document.getElementById('contactForm');
  if (!form) return;

  form.addEventListener('submit', function(e) {
    e.preventDefault();

    console.log('[Contact Form] Submitting...');

    // Get submit button
    const submitBtn = form.querySelector('.form-submit');
    const btnText = submitBtn?.querySelector('.button-text');
    const originalText = btnText?.textContent;

    // Show loading state
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.style.opacity = '0.6';
    }
    if (btnText) {
      btnText.textContent = 'Sending...';
    }

    // Prepare form data
    const formData = new FormData(form);

    // Submit via AJAX
    fetch(form.action || '/contact', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      console.log('[Contact Form] Response:', data);

      if (data.success) {
        // Populate modal data
        if (data.contact) {
          const modalReference = document.getElementById('contact-modal-reference');
          const modalName = document.getElementById('contact-modal-name');
          const modalEmail = document.getElementById('contact-modal-email');

          if (modalReference) modalReference.textContent = data.contact.reference || 'N/A';
          if (modalName) modalName.textContent = data.contact.name || 'N/A';
          if (modalEmail) modalEmail.textContent = data.contact.email || 'N/A';
        }

        // Show success modal
        const successModal = document.getElementById('contact-success-modal');
        if (successModal) {
          successModal.style.display = 'flex';
          console.log('[Contact Form] Success modal displayed');
        } else {
          console.error('[Contact Form] Success modal not found!');
          alert(data.message || 'Thank you! We will contact you soon.');
        }

        // Reset form
        form.reset();
        const messageInput = document.getElementById('message');
        if (messageInput) messageInput.style.height = 'auto';

      } else {
        // Show error modal
        const errorModal = document.getElementById('contact-error-modal');
        if (errorModal) {
          const errorMessage = document.getElementById('contact-error-message');
          if (errorMessage) {
            errorMessage.textContent = data.message || 'An error occurred. Please try again.';
          }
          errorModal.style.display = 'flex';
        } else {
          alert(data.message || 'An error occurred. Please try again.');
        }
      }
    })
    .catch(error => {
      console.error('[Contact Form] Submission error:', error);

      const errorModal = document.getElementById('contact-error-modal');
      if (errorModal) {
        const errorMessage = document.getElementById('contact-error-message');
        if (errorMessage) {
          errorMessage.textContent = 'Network error. Please check your connection and try again.';
        }
        errorModal.style.display = 'flex';
      } else {
        alert('An error occurred. Please try again or contact us via WhatsApp.');
      }
    })
    .finally(() => {
      // Restore button state
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
      }
      if (btnText && originalText) {
        btnText.textContent = originalText;
      }
    });
  });

})();

// ========================================
// Modal Close Handlers
// ========================================
(function() {
  'use strict';

  function initModalHandlers() {
    const successModal = document.getElementById('contact-success-modal');
    const errorModal = document.getElementById('contact-error-modal');

    // Success modal close handlers
    const successCloseBtn = document.getElementById('contact-success-close');
    const successCloseX = document.getElementById('contact-success-close-x');

    if (successCloseBtn) {
      successCloseBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (successModal) successModal.style.display = 'none';
      });
    }

    if (successCloseX) {
      successCloseX.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (successModal) successModal.style.display = 'none';
      });
    }

    // Prevent clicks inside modal content from closing
    if (successModal) {
      const successContainer = successModal.querySelector('.modal-container');
      if (successContainer) {
        successContainer.addEventListener('click', function(e) {
          e.stopPropagation();
        });
      }

      // Close when clicking overlay background
      successModal.addEventListener('click', function(e) {
        if (e.target === successModal) {
          successModal.style.display = 'none';
        }
      });
    }

    // Error modal close handlers
    const errorCloseBtn = document.getElementById('contact-error-close');
    const errorCloseX = document.getElementById('contact-error-close-x');

    if (errorCloseBtn) {
      errorCloseBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (errorModal) errorModal.style.display = 'none';
      });
    }

    if (errorCloseX) {
      errorCloseX.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (errorModal) errorModal.style.display = 'none';
      });
    }

    // Prevent clicks inside modal content from closing
    if (errorModal) {
      const errorContainer = errorModal.querySelector('.modal-container');
      if (errorContainer) {
        errorContainer.addEventListener('click', function(e) {
          e.stopPropagation();
        });
      }

      // Close when clicking overlay background
      errorModal.addEventListener('click', function(e) {
        if (e.target === errorModal) {
          errorModal.style.display = 'none';
        }
      });
    }

    console.log('[Contact] Modal handlers initialized');
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initModalHandlers);
  } else {
    initModalHandlers();
  }

})();
