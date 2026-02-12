
    // i18n helper: replace :placeholder with values
    function _t(key, replacements) {
      var str = (window.bookingI18n && window.bookingI18n[key]) || key;
      if (replacements) {
        Object.keys(replacements).forEach(function(k) {
          str = str.replace(':' + k, replacements[k]);
        });
      }
      return str;
    }

    // Global payment display update function (accessible from all scopes)
    function updatePaymentDisplay() {
      const totalPrice = parseFloat(document.getElementById('modal-total')?.textContent.replace(/[^0-9.-]/g, '')) || 200;
      const selectedOption = document.querySelector('input[name="payment_type"]:checked')?.value || 'deposit';
      const paymentBtnText = document.getElementById('payment-btn-text');
      const depositAmountEl = document.getElementById('deposit-amount');
      const fullAmountEl = document.getElementById('full-amount');

      // Update border styling: remove 'selected' class from all cards, add to checked one
      document.querySelectorAll('.payment-card-compact').forEach(card => {
        card.classList.remove('selected');
      });
      const checkedInput = document.querySelector('input[name="payment_type"]:checked');
      if (checkedInput) {
        checkedInput.closest('.payment-card-compact')?.classList.add('selected');
      }

      if (selectedOption === 'deposit') {
        const depositAmount = totalPrice * 0.30;
        if (depositAmountEl) depositAmountEl.textContent = '$' + depositAmount.toFixed(0);
        if (paymentBtnText) paymentBtnText.textContent = _t('payNow', {amount: depositAmount.toFixed(0)});
      } else {
        const fullAmount = totalPrice * 0.97; // 3% discount
        if (fullAmountEl) fullAmountEl.textContent = '$' + fullAmount.toFixed(0);
        if (paymentBtnText) paymentBtnText.textContent = _t('payNowSave', {amount: fullAmount.toFixed(0), discount: '3'});
      }
    }

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

      // ================================================================
      // DATE PICKER FIX: Set min date to tomorrow (24 hours in advance)
      // ================================================================
      if (tourDateField) {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const minDate = tomorrow.toISOString().split('T')[0];
        tourDateField.min = minDate;
        console.log('[Booking] Date picker min date set to:', minDate);

        // Clear any date error when user selects new date
        tourDateField.addEventListener('change', function(e) {
          console.log('[Booking] Date changed to:', e.target.value);
          clearFieldError('tour-date');
        });
      }

      // ================================================================
      // INLINE VALIDATION: Helper functions for showing/hiding errors
      // ================================================================
      function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        // Try multiple error span naming conventions
        let errorSpan = document.getElementById(fieldId + '-error') ||
                        document.getElementById(fieldId.replace('tour-', '') + '-error') ||
                        document.getElementById(fieldId.replace('customer-', '') + '-error');

        if (field) {
          field.classList.add('form-input--error');
          field.setAttribute('aria-invalid', 'true');
        }

        if (errorSpan) {
          errorSpan.textContent = message;
          errorSpan.style.display = 'block';
        } else {
          // Create error span if it doesn't exist
          const parent = field ? field.parentElement : null;
          if (parent) {
            errorSpan = document.createElement('span');
            errorSpan.id = fieldId + '-error';
            errorSpan.className = 'form-error';
            errorSpan.setAttribute('role', 'alert');
            errorSpan.textContent = message;
            errorSpan.style.display = 'block';
            parent.appendChild(errorSpan);
          }
        }
      }

      function clearFieldError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorSpan = document.getElementById(fieldId + '-error') ||
                          document.getElementById(fieldId.replace('tour-', '') + '-error') ||
                          document.getElementById(fieldId.replace('customer-', '') + '-error');

        if (field) {
          field.classList.remove('form-input--error');
          field.setAttribute('aria-invalid', 'false');
        }

        if (errorSpan) {
          errorSpan.textContent = '';
          errorSpan.style.display = 'none';
        }
      }

      function clearAllErrors() {
        document.querySelectorAll('.form-input--error').forEach(el => {
          el.classList.remove('form-input--error');
          el.setAttribute('aria-invalid', 'false');
        });
        document.querySelectorAll('.form-error').forEach(el => {
          el.textContent = '';
          el.style.display = 'none';
        });
      }

      function showValidationErrors(errors) {
        // Map backend field names to frontend field IDs
        const fieldMap = {
          'start_date': document.getElementById('private_start_date') ? 'private_start_date' : 'tour-date',
          'number_of_guests': 'tour-guests',
          'customer_name': 'customer-name',
          'customer_email': 'customer-email',
          'customer_phone': 'customer-phone',
          'tour_id': 'tour-id'
        };

        Object.keys(errors).forEach(field => {
          const fieldId = fieldMap[field] || field;
          const message = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
          showFieldError(fieldId, message);
        });

        // Scroll to first error
        const firstError = document.querySelector('.form-input--error');
        if (firstError) {
          firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
          firstError.focus();
        }
      }

      function showGeneralError(message) {
        // Show error in a general error container if available
        let errorContainer = document.getElementById('form-general-error');
        if (!errorContainer) {
          // Create one if it doesn't exist
          const form = document.getElementById('booking-form') || document.getElementById('inquiry-form');
          if (form) {
            errorContainer = document.createElement('div');
            errorContainer.id = 'form-general-error';
            errorContainer.className = 'form-error form-error--general';
            errorContainer.setAttribute('role', 'alert');
            errorContainer.style.cssText = 'background: #fee2e2; color: #dc2626; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; display: none;';
            form.insertBefore(errorContainer, form.firstChild);
          }
        }

        if (errorContainer) {
          errorContainer.textContent = message;
          errorContainer.style.display = 'block';
          errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }

      function hideGeneralError() {
        const errorContainer = document.getElementById('form-general-error');
        if (errorContainer) {
          errorContainer.style.display = 'none';
          errorContainer.textContent = '';
        }
      }

      // Global variable to store current booking ID for payment
      window.currentBookingId = null;

      // Fetch CSRF token and populate field
      fetch('/csrf-token')
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
        fetch('/api/tours/' + tourSlug)
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

      // Add guest count change listener for price preview
      if (tourGuestsField && tourIdField) {
        // Fetch initial price on page load
        const initialTourId = tourIdField.value;
        const initialGuestCount = parseInt(tourGuestsField.value) || 2;

        if (initialTourId && typeof window.fetchPricePreview === 'function') {
          window.fetchPricePreview(initialTourId, initialGuestCount);
        }

        // Update price when guest count changes
        const updatePrice = function() {
          const tourId = tourIdField.value;
          const guestCount = parseInt(tourGuestsField.value) || 1;

          if (tourId && guestCount > 0 && typeof window.fetchPricePreview === 'function') {
            const pricePreview = document.getElementById('price-preview');
            if (pricePreview) {
              pricePreview.style.display = 'block';
            }
            window.fetchPricePreview(tourId, guestCount);
          }
        };

        // Listen to both 'change' and 'input' events for maximum compatibility
        tourGuestsField.addEventListener('change', updatePrice);
        tourGuestsField.addEventListener('input', updatePrice);
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
          if (submitText) submitText.textContent = _t('confirmBooking');

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

          // Clear previous errors before submission
          clearAllErrors();
          hideGeneralError();

          const submitButton = document.getElementById('submit-button');
          const formData = new FormData(bookingForm);

          // Detect tour type (private vs group)
          const tourType = formData.get('tour_type');
          const isPrivate = tourType === 'private';

          if (isPrivate) {
            // Private tour: validate start_date from date picker, no departure_id needed
            const startDate = formData.get('start_date');
            const dateField = document.getElementById('private_start_date');

            console.log('[Booking] Private tour validation - start_date from FormData:', startDate);
            console.log('[Booking] Private tour validation - date field value:', dateField ? dateField.value : 'field not found');
            console.log('[Booking] Private tour validation - date field name:', dateField ? dateField.name : 'field not found');

            // Check both FormData and direct field value
            const actualDateValue = dateField ? dateField.value : startDate;

            if (!actualDateValue || actualDateValue === '') {
              alert(_t('selectTravelDate'));
              if (dateField) dateField.focus();
              console.error('[Booking] No start date selected for private tour');
              return;
            }

            // If date is in field but not in FormData, add it manually
            if (actualDateValue && (!startDate || startDate === '')) {
              formData.set('start_date', actualDateValue);
              console.log('[Booking] Added start_date to FormData manually:', actualDateValue);
            }

            console.log('[Booking] Private tour submitting with start_date:', formData.get('start_date'));
          } else {
            // Group tour: validate departure_id and start_date from calendar
            const departureId = formData.get('departure_id');
            if (!departureId || departureId === '') {
              alert(_t('selectDeparture'));
              console.error('[Booking] No departure selected');
              return;
            }

            const startDate = formData.get('start_date');
            if (!startDate || startDate === '') {
              alert(_t('selectDeparture'));
              console.error('[Booking] No start date set');
              return;
            }
            console.log('[Booking] Group tour submitting with departure_id:', departureId, 'start_date:', startDate);
          }

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

              // Store booking ID, reference, and email globally for payment
              if (isBooking && record.id) {
                window.currentBookingId = record.id;
                window.currentBookingRef = record.reference;
                window.currentBookingEmail = record.customer?.email;
                console.log('[Payment] Booking stored:', record.id, record.reference);
              }

              // Populate modal with data (with null checks)
              const modalRef = document.getElementById('modal-reference');
              const modalTourName = document.getElementById('modal-tour-name');
              const modalDate = document.getElementById('modal-date');
              const modalGuests = document.getElementById('modal-guests');
              const modalTotal = document.getElementById('modal-total');
              const modalEmail = document.getElementById('modal-customer-email');

              if (modalRef) modalRef.textContent = record.reference || 'N/A';
              if (modalTourName) modalTourName.textContent = record.tour?.title || _t('fallbackTourName');
              if (modalDate) modalDate.textContent = record.start_date ? new Date(record.start_date).toLocaleDateString(document.documentElement.lang || 'en', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : _t('dateTbd');

              // Guest count with proper pluralization
              const guestCount = record.pax_total || formData.get('number_of_guests') || 1;
              if (modalGuests) modalGuests.textContent = `${guestCount} ${guestCount == 1 ? _t('guestSingular') : _t('guestPlural')}`;

              if (modalTotal) {
                const totalPrice = record.total_price ? parseFloat(record.total_price) : 200;
                modalTotal.textContent = '$' + totalPrice.toFixed(2);

                // Update payment amounts
                const depositAmountEl = document.getElementById('deposit-amount');
                const fullAmountEl = document.getElementById('full-amount');
                const paymentBtnText = document.getElementById('payment-btn-text');

                if (depositAmountEl) depositAmountEl.textContent = '$' + (totalPrice * 0.30).toFixed(0);
                if (fullAmountEl) fullAmountEl.textContent = '$' + (totalPrice * 0.97).toFixed(0);
                if (paymentBtnText) paymentBtnText.textContent = _t('payNow', {amount: (totalPrice * 0.30).toFixed(0)});
              }
              if (modalEmail) modalEmail.textContent = record.customer?.email || formData.get('customer_email') || _t('yourEmail');

              // Update modal for inquiry vs booking
              const paymentBtn = document.getElementById('proceed-to-payment-btn');

              if (!isBooking) {
                // This is an INQUIRY - hide payment button and update text
                const modalTitle = document.querySelector('.modal-title');
                const modalSubtitle = document.querySelector('.modal-subtitle');
                const totalItem = document.querySelector('.summary-item--total');

                if (modalTitle) modalTitle.textContent = _t('inquirySubmitted');
                if (modalSubtitle) modalSubtitle.textContent = _t('inquirySubmittedText');
                if (totalItem) totalItem.style.display = 'none';
                if (paymentBtn) paymentBtn.style.display = 'none';

                console.log('[Inquiry] Payment button hidden for inquiry');
              } else {
                // This is a BOOKING - button should be visible (it's visible by default now)
                console.log('[Payment] Booking created with price:', record.total_price);
                console.log('[Payment] Payment button should be visible');
                console.log('[Payment] Button element:', paymentBtn);

                if (record.total_price && parseFloat(record.total_price) === 0) {
                  console.warn('[Payment] Warning: Booking has zero price - button will still show');
                }
              }

              // Show modal
              const bookingModal = document.getElementById('booking-confirmation-modal');
              console.log('[Booking] Modal element:', bookingModal);
              if (bookingModal) {
                bookingModal.style.display = 'flex';
                console.log('[Booking] Modal display set to flex');

                // Initialize payment option styling
                if (typeof updatePaymentDisplay === 'function') {
                  updatePaymentDisplay();
                }
              } else {
                console.error('[Booking] Modal not found in DOM!');
              }

              bookingForm.reset();
              step2Form.style.display = 'none';
              bookingBtn.classList.remove('active');
              inquiryBtn.classList.remove('active');

              console.log('[Booking] Confirmation modal shown for:', record.reference);
            } else {
              // Show inline validation errors instead of alert
              console.error('[Booking] Validation errors:', data.errors);

              if (data.errors) {
                showValidationErrors(data.errors);
              } else {
                showGeneralError(data.message || _t('formError'));
              }
            }
          })
          .catch(error => {
            console.error('[Booking] Submission error:', error);
            showGeneralError(_t('networkError'));
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
        btnText.textContent = _t('sending');
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
          btnText.textContent = _t('sendQuestion');
          spinner.style.display = 'none';

          if (data.success) {
            const inquiry = data.inquiry;

            // Populate inquiry confirmation modal (with null checks)
            const inquiryModalRef = document.getElementById('inquiry-modal-reference');
            const inquiryModalTour = document.getElementById('inquiry-modal-tour');
            const inquiryModalEmail = document.getElementById('inquiry-modal-email');

            if (inquiryModalRef) inquiryModalRef.textContent = inquiry.reference || 'N/A';
            if (inquiryModalTour) inquiryModalTour.textContent = inquiry.tour?.title || _t('fallbackTourName');
            if (inquiryModalEmail) inquiryModalEmail.textContent = inquiry.customer_email || _t('yourEmail');

            // Show inquiry confirmation modal
            const inquiryModal = document.getElementById('inquiry-confirmation-modal');
            if (inquiryModal) inquiryModal.style.display = 'flex';

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
            // Show inline error instead of alert
            console.error('[Inquiry] Submission failed:', data);

            // Show inline validation errors
            if (data.errors) {
              // Map inquiry form field names
              const fieldMap = {
                'customer_name': 'inquiry-name',
                'customer_email': 'inquiry-email',
                'message': 'inquiry-message',
                'tour_id': 'inquiry-tour-id'
              };

              Object.keys(data.errors).forEach(field => {
                const fieldId = fieldMap[field] || field;
                const message = Array.isArray(data.errors[field]) ? data.errors[field][0] : data.errors[field];
                const inputField = document.getElementById(fieldId);
                if (inputField) {
                  inputField.classList.add('form-input--error');
                  // Show error below field
                  let errorEl = inputField.nextElementSibling;
                  if (!errorEl || !errorEl.classList.contains('form-error')) {
                    errorEl = document.createElement('span');
                    errorEl.className = 'form-error';
                    errorEl.setAttribute('role', 'alert');
                    inputField.parentNode.insertBefore(errorEl, inputField.nextSibling);
                  }
                  errorEl.textContent = message;
                  errorEl.style.display = 'block';
                }
              });

              // Focus first error field
              const firstError = inquiryForm.querySelector('.form-input--error');
              if (firstError) firstError.focus();
            } else {
              // Show general error message in the form
              let errorContainer = inquiryForm.querySelector('.form-error--general');
              if (!errorContainer) {
                errorContainer = document.createElement('div');
                errorContainer.className = 'form-error form-error--general';
                errorContainer.style.cssText = 'background: #fee2e2; color: #dc2626; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;';
                inquiryForm.insertBefore(errorContainer, inquiryForm.firstChild);
              }
              errorContainer.textContent = data.message || _t('formError');
              errorContainer.style.display = 'block';
            }
          }
        })
        .catch(error => {
          console.error('[Inquiry] Submission error:', error);

          // Reset button state
          submitBtn.disabled = false;
          btnText.textContent = _t('sendQuestion');
          spinner.style.display = 'none';

          // Show inline error instead of alert
          let errorContainer = inquiryForm.querySelector('.form-error--general');
          if (!errorContainer) {
            errorContainer = document.createElement('div');
            errorContainer.className = 'form-error form-error--general';
            errorContainer.style.cssText = 'background: #fee2e2; color: #dc2626; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;';
            inquiryForm.insertBefore(errorContainer, inquiryForm.firstChild);
          }
          errorContainer.textContent = _t('networkError');
          errorContainer.style.display = 'block';
        });
        };

        // Check if token exists, if not fetch it
        if (!csrfToken || !csrfToken.value) {
          console.warn('[Inquiry] CSRF token not loaded, fetching now...');

          fetch('/csrf-token')
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

              // Show inline error instead of alert
              let errorContainer = inquiryForm.querySelector('.form-error--general');
              if (!errorContainer) {
                errorContainer = document.createElement('div');
                errorContainer.className = 'form-error form-error--general';
                errorContainer.style.cssText = 'background: #fee2e2; color: #dc2626; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;';
                inquiryForm.insertBefore(errorContainer, inquiryForm.firstChild);
              }
              errorContainer.textContent = _t('csrfError');
              errorContainer.style.display = 'block';

              // Reset button state
              submitBtn.disabled = false;
              btnText.textContent = _t('sendQuestion');
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
      const closeBtn = document.querySelector('.modal-close, .modal-close-minimal');
      const closeXBtn = document.getElementById('booking-modal-close-x');
      const continueBrowsingBtn = document.getElementById('continue-browsing');

      // Close modal function
      function closeModal() {
        if (modal) {
          modal.style.display = 'none';
          console.log('[Modal] Confirmation modal closed');
        }
      }

      // Close on X button click (old)
      if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
      }

      // Close on X button click (new top-right X)
      if (closeXBtn) {
        closeXBtn.addEventListener('click', closeModal);
      }

      // Close on "I'll Pay Later" button
      if (continueBrowsingBtn) {
        continueBrowsingBtn.addEventListener('click', closeModal);
      }

      // Handle payment option selection
      const paymentOptions = document.querySelectorAll('input[name="payment_type"]');

      // Attach event listeners to payment options (function is now global)
      paymentOptions.forEach(option => {
        option.addEventListener('change', updatePaymentDisplay);
      });

      // Handle payment button click
      const paymentBtn = document.getElementById('proceed-to-payment-btn');
      if (paymentBtn) {
        paymentBtn.addEventListener('click', function() {
          const selectedPaymentType = document.querySelector('input[name="payment_type"]:checked')?.value || 'deposit';
          console.log('[Payment] Payment button clicked, booking ID:', window.currentBookingId, 'Type:', selectedPaymentType);

          if (window.currentBookingId && typeof initiatePayment === 'function') {
            initiatePayment(window.currentBookingId, selectedPaymentType);
          } else {
            console.error('[Payment] Cannot initiate payment - missing booking ID or function');
          }
        });
      }

      // Handle "Pay Later" link click
      const payLaterLink = document.getElementById('pay-later-link');
      if (payLaterLink) {
        payLaterLink.addEventListener('click', function(e) {
          e.preventDefault();
          console.log('[Payment] User chose Pay Later');
          if (typeof handlePaymentFallback === 'function') {
            handlePaymentFallback('user_choice');
          }
        });
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
      const inquiryCloseXBtn = document.getElementById('inquiry-modal-close-x');
      const closeInquiryModalBtn = document.getElementById('close-inquiry-modal');

      // Close modal function
      function closeInquiryModal() {
        if (inquiryModal) {
          inquiryModal.style.display = 'none';
          console.log('[Modal] Inquiry confirmation modal closed');
        }
      }

      // Close on X button (old)
      if (inquiryCloseBtn) {
        inquiryCloseBtn.addEventListener('click', closeInquiryModal);
      }

      // Close on X button (new top-right X)
      if (inquiryCloseXBtn) {
        inquiryCloseXBtn.addEventListener('click', closeInquiryModal);
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

    // ================================================================
    // EXTRAS / ADD-ONS: Price calculation with event delegation
    // ================================================================
    (function() {
      function updateExtrasTotal() {
        var checkboxes = document.querySelectorAll('.booking-extra-checkbox');
        if (!checkboxes.length) return;

        var guestsInput = document.getElementById('guests_count');
        var guestCount = guestsInput ? parseInt(guestsInput.value) || 1 : 1;
        var addonsTotal = 0;
        var anyChecked = false;

        checkboxes.forEach(function(cb) {
          if (cb.checked) {
            anyChecked = true;
            var price = parseFloat(cb.dataset.price) || 0;
            var unit = cb.dataset.unit || 'per_person';
            if (unit === 'per_person') {
              addonsTotal += price * guestCount;
            } else {
              addonsTotal += price;
            }
          }
        });

        // Group form layout (existing)
        var subtotalEl = document.getElementById('extras-subtotal');
        var amountEl = document.getElementById('extras-total-amount');
        if (subtotalEl) {
          subtotalEl.style.display = anyChecked ? 'block' : 'none';
        }
        if (amountEl) {
          amountEl.textContent = '$' + addonsTotal.toFixed(2);
        }

        // Private form layout (unified price summary)
        var addonsRowEl = document.getElementById('price-addons-row');
        var addonsAmountEl = document.getElementById('price-addons-amount');
        var grandTotalEl = document.getElementById('price-grand-total');

        if (addonsRowEl) {
          addonsRowEl.style.display = anyChecked ? 'flex' : 'none';
        }
        if (addonsAmountEl) {
          addonsAmountEl.textContent = '+$' + addonsTotal.toFixed(2);
        }
        if (grandTotalEl) {
          var basePrice = parseFloat(grandTotalEl.dataset.base) || 0;
          var grandTotal = basePrice + addonsTotal;
          grandTotalEl.textContent = '$' + grandTotal.toFixed(2);
        }

        // Sync sticky price after updating extras
        syncStickyPrice();
      }

      // Sync sticky price box with booking form computed total
      var userHasInteracted = false; // Track if user has changed guest count or selected extras

      function syncStickyPrice() {
        var stickyLabel = document.getElementById('sticky-price-label');
        var stickyAmount = document.getElementById('sticky-price-amount');
        var stickyUnit = document.getElementById('sticky-price-unit');

        // If sticky elements don't exist, skip (not all pages have sticky price)
        if (!stickyLabel || !stickyAmount || !stickyUnit) return;

        var computedTotal = null;
        var guestsInput = document.getElementById('guests_count');
        var guestCount = guestsInput ? parseInt(guestsInput.value) || 1 : 1;

        // Try to get computed total from private form layout first
        var grandTotalEl = document.getElementById('price-grand-total');

        if (grandTotalEl) {
          // Private form: read data-base attribute + calculate with addons
          var basePrice = parseFloat(grandTotalEl.dataset.base) || 0;
          var checkboxes = document.querySelectorAll('.booking-extra-checkbox');
          var addonsTotal = 0;

          checkboxes.forEach(function(cb) {
            if (cb.checked) {
              var price = parseFloat(cb.dataset.price) || 0;
              var unit = cb.dataset.unit || 'per_person';
              if (unit === 'per_person') {
                addonsTotal += price * guestCount;
              } else {
                addonsTotal += price;
              }
            }
          });

          computedTotal = basePrice + addonsTotal;

          // Mark user has interacted if they changed from default or selected extras
          if (addonsTotal > 0 || guestCount !== 1) {
            userHasInteracted = true;
          }

          computedTotal = basePrice + addonsTotal;
        }

        // If we have a computed total and user has interacted, update sticky
        if (computedTotal !== null && computedTotal > 0 && userHasInteracted) {
          stickyLabel.textContent = 'Selected total';
          stickyAmount.textContent = '$' + computedTotal.toFixed(2);
          stickyUnit.textContent = ''; // Remove "/person" suffix
        }
        // Otherwise, leave sticky in default "from $X.XX /person" state
      }

      // Event delegation: listen on document for checkbox changes inside booking form
      document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('booking-extra-checkbox')) {
          updateExtrasTotal();
        }
      });

      // Recalculate after HTMX swaps (guest count change re-renders the form)
      document.addEventListener('htmx:afterSettle', function(event) {
        // Only recalculate if the swap target was the booking form
        if (event.detail.target && event.detail.target.id === 'booking-form-container') {
          updateExtrasTotal(); // This will call syncStickyPrice() internally
        }
      });

      console.log('[Extras] Add-on price calculation initialized');
    })();

    // ================================================================
    // GUEST COUNT HANDLERS: Event delegation for +/- buttons
    // ================================================================
    (function() {
      // Event delegation: listen on document for guest count button clicks
      document.addEventListener('click', function(e) {
        // Check if clicked element is a guest count button
        if (e.target && (e.target.classList.contains('guest-decrease-btn') || e.target.classList.contains('guest-increase-btn'))) {
          const btn = e.target;
          const input = document.getElementById('guests_count');
          if (!input) return;

          let currentValue = parseInt(input.value) || 1;
          const action = btn.dataset.action;
          const min = parseInt(btn.dataset.min || input.min || 1);
          const max = parseInt(btn.dataset.max || input.max || 10);

          // Update guest count
          if (action === 'decrease' && currentValue > min) {
            currentValue--;
          } else if (action === 'increase' && currentValue < max) {
            currentValue++;
          } else {
            return; // No change needed
          }

          input.value = currentValue;

          // Get tour ID and type
          const tourIdInput = document.getElementById('tour_id_for_htmx');
          const tourTypeInput = document.querySelector('input[name="tour_type"]');
          if (!tourIdInput || !tourTypeInput) return;

          const tourId = tourIdInput.value;
          const tourType = tourTypeInput.value;

          // Prepare HTMX values
          const values = {
            tour_id: tourId,
            type: tourType,
            guests_count: currentValue
          };

          // Include selected extras to preserve state across swaps
          const container = document.getElementById('booking-form-container') || document;
          const selectedExtras = Array.from(container.querySelectorAll('.booking-extra-checkbox:checked')).map(cb => cb.value);
          // Format extras as Laravel expects (extras[0]=id, extras[1]=id, etc.)
          selectedExtras.forEach((id, index) => {
            values[`extras[${index}]`] = id;
          });

          // For group tours, include selected departure ID
          if (tourType === 'group') {
            const selectedDepartureId = document.querySelector('input[name="group_departure_id"]:checked')?.value;
            if (!selectedDepartureId) return; // Group tours require departure selection
            values.group_departure_id = selectedDepartureId;
          }

          // Trigger HTMX update
          if (typeof htmx === 'undefined') {
            console.error('[Guest Count] HTMX not loaded - cannot update preview');
            alert('Booking system not loaded. Please refresh the page.');
            return;
          }

          htmx.ajax('POST', '/bookings/preview', {
            target: '#booking-form-container',
            swap: 'innerHTML',
            values: values
          });

          // Update button states
          const decreaseBtn = document.querySelector('.guest-decrease-btn');
          const increaseBtn = document.querySelector('.guest-increase-btn');
          if (decreaseBtn) decreaseBtn.disabled = currentValue <= min;
          if (increaseBtn) increaseBtn.disabled = currentValue >= max;
        }
      });

      console.log('[Guest Count] Event delegation initialized');
    })();

