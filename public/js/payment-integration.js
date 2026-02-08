// ============================================
// OCTOBANK PAYMENT INTEGRATION
// ============================================

// Function to fetch price preview based on guest count
function fetchPricePreview(tourId, guestCount) {
  const priceDisplayEl = document.getElementById('price-preview');
  if (!priceDisplayEl || !tourId || !guestCount) return;

  fetch('/api/payment/price-preview?tour_id=' + tourId + '&guests=' + guestCount)
    .then(response => response.json())
    .then(data => {
      if (data.success && data.current_tier) {
        const priceUSD = parseFloat(data.current_tier.price_total);
        const pricePerPerson = parseFloat(data.current_tier.price_per_person);
        const priceUZS = data.current_tier.formatted_total || '';

        // Update price preview at bottom
        priceDisplayEl.innerHTML = '<div class="price-preview">' +
          '<div class="price-usd">$' + priceUSD.toFixed(2) + ' USD</div>' +
          '<div class="price-uzs">' + priceUZS + '</div>' +
          '<div class="price-label">' + (data.current_tier.label || guestCount + ' guest(s)') + '</div>' +
          '</div>';

        // Update price header badge at top
        const priceAmount = document.querySelector('.price-amount');
        if (priceAmount) {
          priceAmount.textContent = '$' + pricePerPerson.toFixed(2);
        }

        // Update price breakdown at top
        const breakdownGuests = document.querySelector('.breakdown-guests');
        const breakdownUnitPrice = document.querySelector('.breakdown-unit-price');
        const breakdownSubtotal = document.querySelector('.breakdown-value[data-subtotal]');
        const breakdownTotal = document.querySelector('.breakdown-total');

        if (breakdownGuests) {
          breakdownGuests.textContent = guestCount + (guestCount === 1 ? ' guest' : ' guests');
          breakdownGuests.setAttribute('data-guests', guestCount);
        }
        if (breakdownUnitPrice) {
          breakdownUnitPrice.textContent = '$' + pricePerPerson.toFixed(2);
          breakdownUnitPrice.setAttribute('data-unit-price', pricePerPerson);
        }
        if (breakdownSubtotal) {
          breakdownSubtotal.textContent = '$' + priceUSD.toFixed(2);
          breakdownSubtotal.setAttribute('data-subtotal', priceUSD);
        }
        if (breakdownTotal) {
          breakdownTotal.textContent = '$' + priceUSD.toFixed(2);
          breakdownTotal.setAttribute('data-total', priceUSD);
        }

        // Update mobile sticky footer price
        const mobilePriceAmount = document.querySelector('.mobile-cta__amount');
        if (mobilePriceAmount) {
          mobilePriceAmount.textContent = '$' + pricePerPerson.toFixed(2);
        }
      }
    })
    .catch(error => console.error('[Payment] Price preview error:', error));
}

// Function to initialize Octobank payment
function initiatePayment(bookingId, paymentType = 'full') {
  console.log('[Payment] Initializing payment for booking:', bookingId, 'Type:', paymentType);

  const paymentBtn = document.getElementById('proceed-to-payment-btn');
  if (paymentBtn) {
    paymentBtn.disabled = true;
    paymentBtn.textContent = 'Processing...';
  }

  fetch('/api/payment/initialize', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({
      booking_id: bookingId,
      payment_type: paymentType,
      save_card: false
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success && data.payment_url) {
      console.log('[Payment] Redirecting to Octobank:', data.payment_url);
      window.location.href = data.payment_url;
    } else {
      console.warn('[Payment] Payment init failed, activating fallback:', data);
      handlePaymentFallback('gateway_failed');
    }
  })
  .catch(error => {
    console.error('[Payment] Payment error:', error);
    handlePaymentFallback('gateway_failed');
  });
}

// Handle payment fallback (gateway failure or user choice)
function handlePaymentFallback(reason) {
  console.log('[Payment] Fallback activated. Reason:', reason);

  // Disable pay button and pay-later link immediately
  var payBtn = document.getElementById('proceed-to-payment-btn');
  if (payBtn) payBtn.disabled = true;
  var payLater = document.getElementById('pay-later-link');
  if (payLater) payLater.style.pointerEvents = 'none';

  var ref = window.currentBookingRef;
  var email = window.currentBookingEmail;

  // If ref or email missing, show fallback UI without calling API
  if (!ref || !email) {
    console.warn('[Payment] Missing ref/email, showing fallback without API call');
    showPaymentFallbackUI(false);
    return;
  }

  // Call backend to record pay-later and notify admin
  fetch('/api/payment/pay-later', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({
      reference: ref,
      email: email,
      reason: reason
    })
  })
  .then(function(response) {
    if (!response.ok) {
      console.warn('[Payment] Fallback API returned', response.status);
    }
    return response.json();
  })
  .then(function(data) {
    console.log('[Payment] Fallback response:', data);
    showPaymentFallbackUI(data.success !== false);
  })
  .catch(function(error) {
    console.error('[Payment] Fallback API call failed:', error);
    // Still show fallback UI - don't trap the user
    showPaymentFallbackUI(false);
  });
}

// Show the fallback confirmation UI in the modal
function showPaymentFallbackUI(apiSucceeded) {
  // Prevent duplicate insertion
  if (document.querySelector('.payment-fallback-notice')) return;

  // Hide payment options
  var paymentOptions = document.querySelector('.payment-options-compact');
  if (paymentOptions) paymentOptions.style.display = 'none';

  // Hide payment button
  var payBtn = document.getElementById('proceed-to-payment-btn');
  if (payBtn) payBtn.style.display = 'none';

  // Hide pay-later link
  var payLater = document.getElementById('pay-later-link');
  if (payLater) payLater.style.display = 'none';

  // Hide trust footer
  var trustFooter = document.querySelector('.trust-footer-compact');
  if (trustFooter) trustFooter.style.display = 'none';

  // Build fallback message
  var contactNote = '';
  if (!apiSucceeded) {
    contactNote = '<p style="margin:8px 0 0;color:#9ca3af;font-size:12px;">' +
      'If you don\'t hear from us within 24 hours, please message us on ' +
      '<a href="https://wa.me/998915550808" style="color:#25d366;">WhatsApp</a>.' +
      '</p>';
  }

  var fallbackHTML =
    '<div class="payment-fallback-notice" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:20px;text-align:center;margin-top:12px;">' +
      '<div style="font-size:28px;margin-bottom:8px;">&#9989;</div>' +
      '<h4 style="margin:0 0 8px;color:#15803d;font-size:16px;font-weight:600;">Booking Request Received!</h4>' +
      '<p style="margin:0 0 14px;color:#4b5563;font-size:13px;line-height:1.6;">' +
        'We\'ll contact you within 24 hours with payment options.' +
      '</p>' +
      '<div style="display:flex;flex-direction:column;gap:8px;text-align:left;font-size:13px;color:#374151;padding:0 8px;">' +
        '<div>&#128179; <strong>Pay by card</strong> &mdash; we\'ll send a secure payment link</div>' +
        '<div>&#128181; <strong>Pay cash</strong> &mdash; on your departure day</div>' +
        '<div>&#9989; <strong>Confirmation</strong> &mdash; check your email for booking details</div>' +
      '</div>' +
      contactNote +
    '</div>';

  // Insert fallback message into modal body
  var modalBody = document.querySelector('.modal-body-scrollable');
  if (modalBody) {
    modalBody.insertAdjacentHTML('beforeend', fallbackHTML);
  }

  // Replace sticky footer with close button
  var stickyFooter = document.querySelector('.modal-footer-sticky');
  if (stickyFooter) {
    stickyFooter.innerHTML =
      '<button class="btn-payment-primary" type="button" onclick="document.getElementById(\'booking-confirmation-modal\').style.display=\'none\'">' +
        '<span>Got It, Thanks!</span>' +
      '</button>';
  }
}

// Make functions globally available
window.fetchPricePreview = fetchPricePreview;
window.initiatePayment = initiatePayment;
window.handlePaymentFallback = handlePaymentFallback;
window.showPaymentFallbackUI = showPaymentFallbackUI;
