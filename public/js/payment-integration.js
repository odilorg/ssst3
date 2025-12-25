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
      console.error('[Payment] Payment initialization failed:', data);
      alert('Payment initialization failed. Please contact support.');
      if (paymentBtn) {
        paymentBtn.disabled = false;
        paymentBtn.textContent = 'Proceed to Payment';
      }
    }
  })
  .catch(error => {
    console.error('[Payment] Payment error:', error);
    alert('Payment error. Please try again or contact support.');
    if (paymentBtn) {
      paymentBtn.disabled = false;
      paymentBtn.textContent = 'Proceed to Payment';
    }
  });
}

// Make functions globally available
window.fetchPricePreview = fetchPricePreview;
window.initiatePayment = initiatePayment;
