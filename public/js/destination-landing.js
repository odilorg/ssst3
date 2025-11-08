/* ======================================================
   DESTINATION LANDING PAGE - DYNAMIC DATA LOADER
   ====================================================== */

(function() {
    'use strict';

    console.log('[Destination Landing] Initializing...');

    // ========================================
    // Extract Destination Slug from URL
    // ========================================

    /**
     * Get destination slug from URL path
     * Expected URL format: /destinations/{slug}
     */
    function getDestinationSlug() {
        const pathParts = window.location.pathname.split('/').filter(Boolean);

        // pathParts should be: ['destinations', 'slug-name']
        if (pathParts[0] === 'destinations' && pathParts[1]) {
            return pathParts[1];
        }

        console.error('[Destination Landing] Could not extract destination slug from URL');
        return null;
    }

    const destinationSlug = getDestinationSlug();

    if (!destinationSlug) {
        console.error('[Destination Landing] No destination slug found. Aborting initialization.');
        return;
    }

    console.log('[Destination Landing] Destination slug:', destinationSlug);

    // ========================================
    // Fetch City Data from Partials Endpoint
    // ========================================

    fetch(`http://127.0.0.1:8000/partials/cities/${destinationSlug}/data`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('[Destination Landing] City data loaded:', data);
            updatePageWithCityData(data);
        })
        .catch(error => {
            console.error('[Destination Landing] Error loading city data:', error);
            showErrorState();
        });

    // ========================================
    // Update Page Elements with City Data
    // ========================================

    function updatePageWithCityData(city) {
        // Update hero section
        updateHeroSection(city);

        // Update breadcrumb
        updateBreadcrumb(city);

        // Update hidden form field (use city ID for filtering)
        const citySlugField = document.getElementById('category-slug');
        if (citySlugField) {
            // Rename it to city-id for clarity
            citySlugField.name = 'city';
            citySlugField.id = 'city-id';
            citySlugField.value = city.id;
        }

        // Update HTMX endpoints
        updateHTMXEndpoints(city.id);

        // Update related categories endpoint
        updateRelatedCitiesEndpoint(city.id);

        console.log('[Destination Landing] Page updated successfully');
    }

    /**
     * Update hero section with city data
     */
    function updateHeroSection(city) {
        // Update background image if available
        const heroSection = document.getElementById('category-hero');
        if (city.hero_image) {
            heroSection.style.backgroundImage = `url('${city.hero_image}')`;
        } else if (city.featured_image) {
            heroSection.style.backgroundImage = `url('${city.featured_image}')`;
        } else {
            // Use gradient fallback
            heroSection.style.background = 'linear-gradient(135deg, #1a5490 0%, #2c7abf 100%)';
        }

        // Update icon (use map-marker-alt for destinations)
        const iconElement = document.querySelector('#category-icon i');
        if (iconElement) {
            iconElement.className = 'fas fa-map-marker-alt';
        }

        // Update city name
        const nameElement = document.getElementById('category-name');
        if (nameElement) {
            nameElement.textContent = city.name || 'Destination';
        }

        // Update description
        const descriptionElement = document.getElementById('category-description');
        if (descriptionElement) {
            descriptionElement.textContent = city.description || city.short_description || '';
        }

        // Update tour count badge
        const countElement = document.getElementById('tour-count-badge');
        if (countElement) {
            const count = city.tour_count || 0;
            const plural = count === 1 ? 'tour' : 'tours';
            countElement.textContent = `${count} ${plural} available`;
        }
    }

    /**
     * Update breadcrumb with city name
     */
    function updateBreadcrumb(city) {
        const breadcrumbElement = document.getElementById('category-breadcrumb');
        if (breadcrumbElement) {
            breadcrumbElement.textContent = city.name || 'Destination';
        }
    }

    /**
     * Update HTMX endpoints to include city filter
     */
    function updateHTMXEndpoints(cityId) {
        // Update tour results endpoint
        const tourResults = document.getElementById('tour-results');
        if (tourResults) {
            const newEndpoint = `http://127.0.0.1:8000/partials/tours/search?city=${cityId}&per_page=12`;
            tourResults.setAttribute('hx-get', newEndpoint);

            // Re-process HTMX to trigger the load
            htmx.process(tourResults);
            htmx.trigger(tourResults, 'load');
        }

        // Update form to submit city instead of category
        const form = document.getElementById('tour-filters');
        if (form) {
            // Form already submits city via hidden field
        }
    }

    /**
     * Update related categories HTMX endpoint to show other cities
     */
    function updateRelatedCitiesEndpoint(cityId) {
        const relatedSection = document.getElementById('related-categories');
        if (relatedSection) {
            const newEndpoint = `http://127.0.0.1:8000/partials/cities/related?current=${cityId}&limit=5`;
            relatedSection.setAttribute('hx-get', newEndpoint);
            htmx.process(relatedSection);
            htmx.trigger(relatedSection, 'load');

            // Update section title
            const sectionTitle = relatedSection.closest('.related-categories').querySelector('.section-header__title');
            if (sectionTitle) {
                sectionTitle.textContent = 'Explore Other Destinations';
            }
            const sectionSubtitle = relatedSection.closest('.related-categories').querySelector('.section-header__subtitle');
            if (sectionSubtitle) {
                sectionSubtitle.textContent = 'Discover more amazing cities in Uzbekistan';
            }
        }
    }

    /**
     * Show error state if city data fails to load
     */
    function showErrorState() {
        const nameElement = document.getElementById('category-name');
        if (nameElement) {
            nameElement.textContent = 'Destination Not Found';
        }

        const descriptionElement = document.getElementById('category-description');
        if (descriptionElement) {
            descriptionElement.textContent = 'The destination you are looking for could not be found.';
        }

        const tourResults = document.getElementById('tour-results');
        if (tourResults) {
            tourResults.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                    <h3>Error Loading Destination</h3>
                    <p>We couldn't load the destination data. Please try again later.</p>
                    <a href="/tours" class="btn btn--primary">Browse All Tours</a>
                </div>
            `;
        }
    }

    // ========================================
    // Mobile Filter Toggle
    // ========================================

    const mobileFilterToggle = document.getElementById('mobile-filter-toggle');
    const filtersSidebar = document.getElementById('filters-sidebar');

    if (mobileFilterToggle && filtersSidebar) {
        mobileFilterToggle.addEventListener('click', function() {
            filtersSidebar.classList.toggle('is-open');
            const icon = this.querySelector('i');
            if (filtersSidebar.classList.contains('is-open')) {
                icon.classList.remove('fa-filter');
                icon.classList.add('fa-times');
            } else {
                icon.classList.add('fa-filter');
                icon.classList.remove('fa-times');
            }
        });
    }

    // ========================================
    // Reset Filters
    // ========================================

    const resetButton = document.getElementById('reset-filters');
    if (resetButton) {
        resetButton.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('tour-filters');
            form.reset();

            // Get city ID from the updated hidden field
            const cityIdField = document.getElementById('city-id');
            const cityId = cityIdField ? cityIdField.value : null;

            if (cityId) {
                // Trigger HTMX reload with city but no other filters
                htmx.ajax('GET', `http://127.0.0.1:8000/partials/tours/search?city=${cityId}&per_page=12`, {
                    target: '#tour-results',
                    swap: 'innerHTML'
                });
            }
        });
    }

    // ========================================
    // Update Results Count after HTMX Swap
    // ========================================

    document.body.addEventListener('htmx:afterSwap', function(evt) {
        if (evt.detail.target.id === 'tour-results') {
            const tourCards = evt.detail.target.querySelectorAll('.tour-card');
            const count = tourCards.length;
            const countElement = document.getElementById('results-count');

            if (countElement) {
                if (count > 0) {
                    countElement.textContent = `${count} ${count === 1 ? 'tour' : 'tours'} found`;
                } else {
                    countElement.textContent = 'No tours found';

                    // Show empty state if no results
                    if (count === 0 && evt.detail.target.querySelector('.tour-card') === null) {
                        evt.detail.target.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-map fa-3x"></i>
                                <h3>No tours available yet</h3>
                                <p>Tours in this destination are coming soon! Check out our other destinations or browse all tours.</p>
                                <div class="empty-state__actions">
                                    <a href="/tours" class="btn btn--primary">Browse All Tours</a>
                                    <a href="/#destinations" class="btn btn--secondary">View Destinations</a>
                                </div>
                            </div>
                        `;
                    }
                }
            }
        }
    });

    // ========================================
    // Close Mobile Filters when Clicking Outside
    // ========================================

    document.addEventListener('click', function(e) {
        if (filtersSidebar && filtersSidebar.classList.contains('is-open')) {
            if (!filtersSidebar.contains(e.target) && !mobileFilterToggle.contains(e.target)) {
                filtersSidebar.classList.remove('is-open');
                const icon = mobileFilterToggle.querySelector('i');
                icon.classList.add('fa-filter');
                icon.classList.remove('fa-times');
            }
        }
    });

    console.log('[Destination Landing] Initialization complete');

})();
