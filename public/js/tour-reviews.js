/**
 * Tour Reviews JavaScript
 * Handles review submission, star rating interaction, and form validation
 */

(function() {
    'use strict';

    // Configuration
    const config = {
        formSelector: '#reviewForm',
        charCountSelector: '#charCount',
        contentSelector: '#reviewContent',
        formMessageSelector: '#formMessage',
        submitButtonSelector: '#submitReview',
        ratingValueSelector: '#ratingValue',
        ratingLabelSelector: '#ratingLabel',
        starButtonsSelector: '.star-btn',
        maxChars: 2000,
    };

    // State
    let selectedRating = 0;

    /**
     * Initialize review functionality
     */
    function init() {
        setupReviewForm();
        setupStarRating();
        setupCharacterCounter();
    }

    /**
     * Setup main review form submission
     */
    function setupReviewForm() {
        const form = document.querySelector(config.formSelector);
        if (!form) {
            console.log('[Reviews] Form not found');
            return;
        }

        form.addEventListener('submit', handleReviewSubmit);
        console.log('[Reviews] Form initialized');
    }

    /**
     * Setup star rating interaction
     */
    function setupStarRating() {
        const starButtons = document.querySelectorAll(config.starButtonsSelector);
        const ratingValue = document.querySelector(config.ratingValueSelector);
        const ratingLabel = document.querySelector(config.ratingLabelSelector);

        if (!starButtons.length || !ratingValue || !ratingLabel) {
            console.log('[Reviews] Star rating elements not found');
            return;
        }

        // Click handler for star buttons
        starButtons.forEach((btn, index) => {
            btn.addEventListener('click', () => {
                const rating = parseInt(btn.dataset.rating);
                selectedRating = rating;
                ratingValue.value = rating;

                // Update visual state
                updateStarDisplay(rating);

                // Update label
                const labels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
                ratingLabel.textContent = labels[rating];
                ratingLabel.classList.add('selected');
            });

            // Hover effect
            btn.addEventListener('mouseenter', () => {
                const rating = parseInt(btn.dataset.rating);
                updateStarDisplay(rating);
            });
        });

        // Reset to selected rating on mouse leave
        const starContainer = document.querySelector('#starRatingInput');
        if (starContainer) {
            starContainer.addEventListener('mouseleave', () => {
                updateStarDisplay(selectedRating);
            });
        }

        console.log('[Reviews] Star rating initialized');
    }

    /**
     * Update star display based on rating
     */
    function updateStarDisplay(rating) {
        const starButtons = document.querySelectorAll(config.starButtonsSelector);

        starButtons.forEach((btn, index) => {
            const btnRating = parseInt(btn.dataset.rating);
            if (btnRating <= rating) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    /**
     * Handle review form submission
     */
    async function handleReviewSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const submitBtn = form.querySelector(config.submitButtonSelector);
        const formMessage = document.querySelector(config.formMessageSelector);

        // Clear previous messages
        clearFormErrors(form);
        hideMessage(formMessage);

        // Validate rating is selected
        if (selectedRating === 0) {
            showMessage(formMessage, 'Please select a star rating', 'error');
            return;
        }

        // Show loading state
        setLoadingState(submitBtn, true);

        // Get form data
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Get tour slug from form data attribute
        const tourSlug = form.dataset.tourSlug;

        try {
            const response = await fetch(`/partials/tours/${tourSlug}/reviews`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (response.ok) {
                // Success
                showMessage(formMessage, result.message, 'success');
                form.reset();

                // Reset rating
                selectedRating = 0;
                updateStarDisplay(0);
                const ratingLabel = document.querySelector(config.ratingLabelSelector);
                if (ratingLabel) {
                    ratingLabel.textContent = 'Select a rating';
                    ratingLabel.classList.remove('selected');
                }

                // Reset character count
                const charCount = document.querySelector(config.charCountSelector);
                if (charCount) charCount.textContent = '0';

                // Reload reviews after 2 seconds
                setTimeout(() => {
                    reloadReviews(tourSlug);
                }, 2000);
            } else if (response.status === 422) {
                // Validation errors
                displayValidationErrors(form, result.errors || {});
                showMessage(formMessage, result.message || 'Please fix the errors above.', 'error');
            } else if (response.status === 429) {
                // Rate limiting
                showMessage(formMessage, result.message || 'Too many submissions. Please try again later.', 'error');
            } else {
                // Other errors
                showMessage(formMessage, result.message || 'Failed to submit review. Please try again.', 'error');
            }
        } catch (error) {
            console.error('Review submission error:', error);
            showMessage(formMessage, 'Network error. Please check your connection and try again.', 'error');
        } finally {
            setLoadingState(submitBtn, false);
        }
    }

    /**
     * Setup character counter for review textarea
     */
    function setupCharacterCounter() {
        const content = document.querySelector(config.contentSelector);
        const charCount = document.querySelector(config.charCountSelector);

        if (!content || !charCount) return;

        content.addEventListener('input', () => {
            const count = content.value.length;
            charCount.textContent = count;

            // Change color if approaching limit
            if (count > config.maxChars * 0.9) {
                charCount.style.color = '#DC2626';
            } else if (count > config.maxChars * 0.75) {
                charCount.style.color = '#F59E0B';
            } else {
                charCount.style.color = '#9CA3AF';
            }
        });
    }

    /**
     * Display validation errors in form
     */
    function displayValidationErrors(form, errors) {
        Object.keys(errors).forEach(field => {
            const errorElement = form.querySelector(`#error-${field}`);
            if (errorElement && errors[field][0]) {
                errorElement.textContent = errors[field][0];
                errorElement.style.display = 'block';

                // Also highlight the input
                const input = form.querySelector(`[name="${field}"]`);
                if (input) {
                    input.style.borderColor = '#DC2626';
                }
            }
        });
    }

    /**
     * Clear all form errors
     */
    function clearFormErrors(form) {
        // Clear error messages
        form.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });

        // Reset input borders
        form.querySelectorAll('input, textarea').forEach(input => {
            input.style.borderColor = '';
        });
    }

    /**
     * Show message in form
     */
    function showMessage(element, message, type = 'success') {
        if (!element) return;

        element.textContent = message;
        element.className = `form-message ${type}`;
        element.style.display = 'block';

        // Scroll to message
        element.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /**
     * Hide message
     */
    function hideMessage(element) {
        if (!element) return;
        element.style.display = 'none';
    }

    /**
     * Set loading state on submit button
     */
    function setLoadingState(button, isLoading) {
        if (!button) return;

        const btnText = button.querySelector('.btn-text');
        const btnLoader = button.querySelector('.btn-loader');

        if (isLoading) {
            button.disabled = true;
            if (btnText) btnText.style.display = 'none';
            if (btnLoader) btnLoader.style.display = 'inline-flex';
        } else {
            button.disabled = false;
            if (btnText) btnText.style.display = 'inline';
            if (btnLoader) btnLoader.style.display = 'none';
        }
    }

    /**
     * Reload reviews section
     */
    function reloadReviews(tourSlug) {
        const reviewsSection = document.querySelector('#reviews');

        if (!reviewsSection || !tourSlug) {
            console.warn('[Reviews] Cannot reload - missing slug or reviews section');
            return;
        }

        console.log('[Reviews] Reloading reviews for tour:', tourSlug);

        // Use HTMX if available to reload reviews
        if (window.htmx) {
            const url = `${window.BACKEND_URL || ''}/partials/tours/${tourSlug}/reviews`;
            console.log('[Reviews] HTMX reload URL:', url);

            htmx.ajax('GET', url, {
                target: '#reviews',
                swap: 'outerHTML',
            });
        } else {
            console.log('[Reviews] HTMX not available, reloading page');
            // Fallback: reload page
            window.location.reload();
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Re-initialize after HTMX loads reviews section
    document.body.addEventListener('htmx:afterSwap', function(event) {
        // Check if reviews section was loaded
        const isReviewsSection = event.detail.target.id === 'reviews' ||
                                  event.detail.target.classList.contains('reviews-section') ||
                                  event.detail.target.querySelector('#reviews') ||
                                  event.detail.target.getAttribute('data-section') === 'reviews';

        if (isReviewsSection) {
            console.log('[Reviews] Re-initializing after HTMX load');
            setTimeout(init, 100); // Small delay to ensure DOM is ready
        }
    });

})();
