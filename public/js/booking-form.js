
    document.addEventListener('DOMContentLoaded', function() {
      const bookingBtn = document.querySelector('[data-action="booking"]');
      const inquiryBtn = document.querySelector('[data-action="inquiry"]');
      const bookingForm = document.getElementById('booking-form');
      const step2Form = document.getElementById('step-2-full-form');
      const actionTypeField = document.getElementById('action-type');
      const messageSection = document.getElementById('message-section');
      const messageField = document.getElementById('inquiry-message');
      const submitText = document.getElementById('submit-text');
      const tourDateField = document.getElementById('tour-date');
      const tourGuestsField = document.getElementById('tour-guests');
      const tourIdField = document.getElementById('tour-id');
      const csrfTokenField = document.getElementById('csrf-token');

      // Fetch CSRF token and populate field
      fetch('http://127.0.0.1:8000/csrf-token')
        .then(response => response.json())
        .then(data => {
          if (data.token && csrfTokenField) {
            csrfTokenField.value = data.token;
            console.log('[Booking] CSRF token loaded');
          }
        })
        .catch(error => console.error('[Booking] Error loading CSRF token:', error));

      // Extract tour slug from URL and fetch tour ID
      const pathParts = window.location.pathname.split('/').filter(Boolean);
      if (pathParts[0] === 'tours' && pathParts[1]) {
        const tourSlug = pathParts[1];
        console.log('[Booking] Tour slug:', tourSlug);

        // Fetch tour ID from backend
        fetch('http://127.0.0.1:8000/api/tours/' + tourSlug)
          .then(response => response.json())
          .then(data => {
            if (data.id && tourIdField) {
              tourIdField.value = data.id;
              console.log('[Booking] Tour ID set:', data.id);

              // Also set inquiry form tour ID
              const inquiryTourIdField = document.getElementById('inquiry-tour-id');
              if (inquiryTourIdField) {
                inquiryTourIdField.value = data.id;
                console.log('[Inquiry] Tour ID pre-populated:', data.id);
              }
            }
          })
          .catch(error => console.error('[Booking] Error fetching tour ID:', error));
      }

      // Handle booking button click - SHOWS FULL BOOKING FORM
      if (bookingBtn) {
        bookingBtn.addEventListener('click', function() {
          console.log('[Booking] Book This Tour clicked');

          // Set action type
          if (actionTypeField) actionTypeField.value = 'booking';

          // Update button states
          bookingBtn.classList.add('active');
          inquiryBtn.classList.remove('active');

          // Hide simple inquiry form if showing
          const simpleInquiryForm = document.getElementById('simple-inquiry-form');
          if (simpleInquiryForm) {
            simpleInquiryForm.style.display = 'none';
          }

          // Make date & guests required for booking
          if (tourDateField) tourDateField.required = true;
          if (tourGuestsField) tourGuestsField.required = true;

          // Hide message section (optional for booking)
          if (messageSection) messageSection.style.display = 'none';
          if (messageField) messageField.required = false;

          // Update submit button text
          if (submitText) submitText.textContent = 'Send Booking Request';

          // Show step 2 form
          if (step2Form) {
            step2Form.style.display = 'block';
            // Scroll to form
            step2Form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            // Focus first input
            const firstInput = step2Form.querySelector('input[type="text"], input[type="email"]');
            if (firstInput) setTimeout(() => firstInput.focus(), 300);
          }

          console.log('[Booking] Full booking form shown');
        });
      }

      // Handle inquiry button click - SHOWS SIMPLE INQUIRY FORM
      if (inquiryBtn) {
        inquiryBtn.addEventListener('click', function() {
          console.log('[Inquiry] Ask a Question clicked');

          // Update button states
          inquiryBtn.classList.add('active');
          bookingBtn.classList.remove('active');

          // Hide STEP 2 (full booking form)
          if (step2Form) {
            step2Form.style.display = 'none';
          }

          // Show simple inquiry form
          const simpleInquiryForm = document.getElementById('simple-inquiry-form');
          if (simpleInquiryForm) {
            simpleInquiryForm.style.display = 'block';

            // Set tour ID in inquiry form
            const tourIdField = document.getElementById('tour-id');
            const tourId = tourIdField ? tourIdField.value : null;
            const inquiryTourId = document.getElementById('inquiry-tour-id');

            if (inquiryTourId) {
              if (tourId) {
                inquiryTourId.value = tourId;
                console.log('[Inquiry] Tour ID set:', tourId);
              } else {
                console.error('[Inquiry] Tour ID not found in hidden field');
                // Try to get from booking form tour_id
                const bookingTourId = document.getElementById('tour-id');
                if (bookingTourId && bookingTourId.value) {
                  inquiryTourId.value = bookingTourId.value;
                  console.log('[Inquiry] Tour ID copied from booking form:', bookingTourId.value);
                }
              }
            }

            // Scroll to inquiry form
            simpleInquiryForm.scrollIntoView({ behavior: 'smooth', block: 'start' });

            // Focus first input after animation
            setTimeout(() => {
              const firstInput = document.getElementById('inquiry-name');
              if (firstInput) firstInput.focus();
            }, 350);
          }

          console.log('[Inquiry] Simple inquiry form shown');
        });
      }

      // Handle inquiry back button click
      const inquiryBackBtn = document.getElementById('inquiry-back-btn');
      if (inquiryBackBtn) {
        inquiryBackBtn.addEventListener('click', function() {
          console.log('[Inquiry] Back button clicked');

          // Hide inquiry form
          const simpleInquiryForm = document.getElementById('simple-inquiry-form');
          if (simpleInquiryForm) {
            simpleInquiryForm.style.display = 'none';
          }

          // Clear form
          const inquiryForm = document.getElementById('inquiry-form');
          if (inquiryForm) {
            inquiryForm.reset();
          }

          // Remove active state from inquiry button
          if (inquiryBtn) {
            inquiryBtn.classList.remove('active');
          }

          // Scroll back to action buttons
          const bookingActions = document.querySelector('.booking-actions');
          if (bookingActions) {
            bookingActions.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }

          console.log('[Inquiry] Returned to action buttons');
        });
      }

      // Handle form submission
      if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
          e.preventDefault();

          const submitButton = document.getElementById('submit-button');
          const formData = new FormData(bookingForm);

          // Disable submit button
          if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add('loading');
          }

          // Send AJAX request
          fetch(bookingForm.action, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Get booking/inquiry data
              const record = data.booking || data.inquiry;
              const isBooking = !!data.booking;

              console.log('[Booking] Success response:', data);
              console.log('[Booking] Record data:', record);

              // Populate modal with data (with null checks)
              const modalRef = document.getElementById('modal-reference');
              const modalTourName = document.getElementById('modal-tour-name');
              const modalDate = document.getElementById('modal-date');
              const modalGuests = document.getElementById('modal-guests');
              const modalTotal = document.getElementById('modal-total');
              const modalEmail = document.getElementById('modal-customer-email');
              const modalEmailInline = document.getElementById('modal-customer-email-inline');

              if (modalRef) modalRef.textContent = record.reference || 'N/A';
              if (modalTourName) modalTourName.textContent = record.tour?.title || 'Your Selected Tour';
              if (modalDate) modalDate.textContent = record.start_date ? new Date(record.start_date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : 'Date TBD';
              if (modalGuests) modalGuests.textContent = (record.pax_total || formData.get('number_of_guests') || '1') + ' guest(s)';
              if (modalTotal) modalTotal.textContent = record.total_price ? '$' + parseFloat(record.total_price).toFixed(2) : 'TBD';
              if (modalEmail) modalEmail.textContent = record.customer?.email || formData.get('customer_email') || 'your email';
              if (modalEmailInline) modalEmailInline.textContent = record.customer?.email || formData.get('customer_email') || 'your email';

              // Update modal title for inquiry
              // Update modal title for inquiry
              if (!isBooking) {
                const modalTitle = document.querySelector('.modal-title');
                const modalSubtitle = document.querySelector('.modal-subtitle');
                const totalItem = document.querySelector('.summary-item--total');
                
                if (modalTitle) modalTitle.textContent = 'Inquiry Submitted!';
                if (modalSubtitle) modalSubtitle.textContent = 'We've received your question and will respond soon';
                if (totalItem) totalItem.style.display = 'none';
              }
              bookingForm.reset();
              step2Form.style.display = 'none';
              bookingBtn.classList.remove('active');
              inquiryBtn.classList.remove('active');

              console.log('[Booking] Confirmation modal shown for:', record.reference);
            } else {
              // Show error message with validation errors if available
              let errorMessage = data.message || 'Please check your form and try again.';

              if (data.errors) {
                errorMessage += '\n\nValidation errors:\n';
                Object.keys(data.errors).forEach(field => {
                  errorMessage += '- ' + field + ': ' + data.errors[field].join(', ') + '\n';
                });
              }

              alert('Error: ' + errorMessage);
              console.error('[Booking] Validation errors:', data.errors);
            }
          })
          .catch(error => {
            console.error('[Booking] Submission error:', error);
            alert('An error occurred. Please try again.');
          })
          .finally(() => {
            // Re-enable submit button
            if (submitButton) {
              submitButton.disabled = false;
              submitButton.classList.remove('loading');
            }
          });
        });
      }

      // Update form field names to match backend expectations
      if (tourDateField) tourDateField.name = 'start_date';
      if (tourGuestsField) tourGuestsField.name = 'number_of_guests';

      console.log('[Booking] Action buttons initialized');
    });

    // ================================================================
    // SIMPLE INQUIRY FORM SUBMISSION
    // ================================================================
    const inquiryForm = document.getElementById('inquiry-form');

    if (inquiryForm) {
      inquiryForm.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('[Inquiry] Form submitted');

        const submitBtn = document.getElementById('submit-inquiry-btn');
        const btnText = submitBtn.querySelector('.btn__text');
        const spinner = submitBtn.querySelector('.spinner');

        // Show loading state
        submitBtn.disabled = true;
        btnText.textContent = 'Sending...';
        spinner.style.display = 'inline-block';

        // Prepare form data
        const formData = new FormData(inquiryForm);

        // Add CSRF token from hidden field
        const csrfToken = document.getElementById('csrf-token');

        // Function to actually submit the form
        const doSubmit = (token) => {
          formData.append('_token', token);

          console.log('[Inquiry] Sending data:', {
            tour_id: formData.get('tour_id'),
            customer_name: formData.get('customer_name'),
            customer_email: formData.get('customer_email'),
            message: formData.get('message'),
            _token: 'present',
          });

          // Submit to backend
          fetch('/partials/inquiries', {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          console.log('[Inquiry] Response:', data);

          // Reset button state
          submitBtn.disabled = false;
          btnText.textContent = 'Send Question';
          spinner.style.display = 'none';

          if (data.success) {
            const inquiry = data.inquiry;

            // Populate inquiry confirmation modal
            document.getElementById('inquiry-modal-reference').textContent = inquiry.reference || 'N/A';
            document.getElementById('inquiry-modal-tour').textContent = inquiry.tour?.title || 'Your Selected Tour';
            document.getElementById('inquiry-modal-email').textContent = inquiry.customer_email || 'your email';

            // Show inquiry confirmation modal
            document.getElementById('inquiry-confirmation-modal').style.display = 'flex';

            // Hide inquiry form
            document.getElementById('simple-inquiry-form').style.display = 'none';

            // Reset form
            inquiryForm.reset();

            // Remove active state from inquiry button
            const inquiryBtn = document.querySelector('.action-btn--inquiry');
            if (inquiryBtn) {
              inquiryBtn.classList.remove('active');
            }

            console.log('[Inquiry] Confirmation modal shown for:', inquiry.reference);
          } else {
            // Show error
            let errorMessage = data.message || 'Please check your form and try again.';

            if (data.errors) {
              errorMessage += '\n\nValidation errors:\n';
              Object.keys(data.errors).forEach(field => {
                errorMessage += '- ' + field + ': ' + data.errors[field].join(', ') + '\n';
              });
            }

            alert('Error: ' + errorMessage);
            console.error('[Inquiry] Submission failed:', data);
          }
        })
        .catch(error => {
          console.error('[Inquiry] Submission error:', error);

          // Reset button state
          submitBtn.disabled = false;
          btnText.textContent = 'Send Question';
          spinner.style.display = 'none';

          alert('An error occurred. Please try again.');
        });
        };

        // Check if token exists, if not fetch it
        if (!csrfToken || !csrfToken.value) {
          console.warn('[Inquiry] CSRF token not loaded, fetching now...');

          fetch('http://127.0.0.1:8000/csrf-token')
            .then(response => response.json())
            .then(data => {
              if (data.token) {
                if (csrfToken) csrfToken.value = data.token;
                console.log('[Inquiry] CSRF token fetched');
                doSubmit(data.token);
              } else {
                throw new Error('No token in response');
              }
            })
            .catch(error => {
              console.error('[Inquiry] Failed to load CSRF token:', error);
              alert('Security token not available. Please refresh the page.');

              // Reset button state
              submitBtn.disabled = false;
              btnText.textContent = 'Send Question';
              spinner.style.display = 'none';
            });
        } else {
          // Token already loaded, submit immediately
          doSubmit(csrfToken.value);
        }
      });
    }

    // ================================================================
    // BOOKING CONFIRMATION MODAL - CLOSE HANDLERS
    // ================================================================
    document.addEventListener('DOMContentLoaded', function() {
      const modal = document.getElementById('booking-confirmation-modal');
      const closeBtn = document.querySelector('.modal-close');
      const continueBrowsingBtn = document.getElementById('continue-browsing');

      // Close modal function
      function closeModal() {
        if (modal) {
          modal.style.display = 'none';
          console.log('[Modal] Confirmation modal closed');
        }
      }

      // Close on X button click
      if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
      }

      // Close on "Got It, Thanks!" button
      if (continueBrowsingBtn) {
        continueBrowsingBtn.addEventListener('click', closeModal);
      }

      // Close on overlay click (clicking outside modal)
      if (modal) {
        modal.addEventListener('click', function(e) {
          if (e.target === modal) {
            closeModal();
          }
        });
      }

      // Close on ESC key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
          closeModal();
        }
      });

      console.log('[Modal] Close handlers initialized');
    });

    // ================================================================
    // INQUIRY CONFIRMATION MODAL - CLOSE HANDLERS
    // ================================================================
    document.addEventListener('DOMContentLoaded', function() {
      const inquiryModal = document.getElementById('inquiry-confirmation-modal');
      const inquiryCloseBtn = inquiryModal ? inquiryModal.querySelector('.modal-close') : null;
      const closeInquiryModalBtn = document.getElementById('close-inquiry-modal');

      // Close modal function
      function closeInquiryModal() {
        if (inquiryModal) {
          inquiryModal.style.display = 'none';
          console.log('[Modal] Inquiry confirmation modal closed');
        }
      }

      // Close on X button
      if (inquiryCloseBtn) {
        inquiryCloseBtn.addEventListener('click', closeInquiryModal);
      }

      // Close on "Got It, Thanks!" button
      if (closeInquiryModalBtn) {
        closeInquiryModalBtn.addEventListener('click', closeInquiryModal);
      }

      // Close on overlay click (clicking outside modal)
      if (inquiryModal) {
        inquiryModal.addEventListener('click', function(e) {
          if (e.target === inquiryModal) {
            closeInquiryModal();
          }
        });
      }

      // Close on ESC key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && inquiryModal && inquiryModal.style.display === 'flex') {
          closeInquiryModal();
        }
      });

      console.log('[Modal] Inquiry modal close handlers initialized');
    });

