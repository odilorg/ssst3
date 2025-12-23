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
        const priceUZS = data.current_tier.formatted_total || '';
        
        priceDisplayEl.innerHTML = '<div class="price-preview">' +
          '<div class="price-usd">$' + priceUSD.toFixed(2) + ' USD</div>' +
          '<div class="price-uzs">' + priceUZS + '</div>' +
          '<div class="price-label">' + (data.current_tier.label || guestCount + ' guest(s)') + '</div>' +
          '</div>';
      }
    })
    .catch(error => console.error('[Payment] Price preview error:', error));
}

// Function to initialize Octobank payment
function initiatePayment(bookingId) {
  console.log('[Payment] Initializing payment for booking:', bookingId);
  
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
