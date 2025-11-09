/* ======================================================
   CATEGORY LANDING PAGE - DYNAMIC DATA LOADER
   ====================================================== */

(function() {
    'use strict';

    console.log('[Category Landing] Initializing...');

    // ========================================
    // Extract Category Slug from URL
    // ========================================

    /**
     * Get category slug from URL path
     * Expected URL format: /tours/category/{slug}
     */
    function getCategorySlug() {
        const pathParts = window.location.pathname.split('/').filter(Boolean);

        // pathParts should be: ['tours', 'category', 'slug-name']
        if (pathParts[0] === 'tours' && pathParts[1] === 'category' && pathParts[2]) {
            return pathParts[2];
        }

        console.error('[Category Landing] Could not extract category slug from URL');
        return null;
    }

    const categorySlug = getCategorySlug();

    if (!categorySlug) {
        console.error('[Category Landing] No category slug found. Aborting initialization.');
        return;
    }

    console.log('[Category Landing] Category slug:', categorySlug);

    // ========================================
    // Fetch Category Data from Partials Endpoint
    // ========================================

    fetch(`/partials/categories/${categorySlug}/data`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('[Category Landing] Category data loaded:', data);
            updatePageWithCategoryData(data);
        })
        .catch(error => {
            console.error('[Category Landing] Error loading category data:', error);
            showErrorState();
        });

    // ========================================
    // Update Page Elements with Category Data
    // ========================================

    function updatePageWithCategoryData(category) {
        // Update hero section
        updateHeroSection(category);

        // Update breadcrumb
        updateBreadcrumb(category);

        // Update hidden form field
        document.getElementById('category-slug').value = categorySlug;

        // Update HTMX endpoints
        updateHTMXEndpoints(categorySlug);

        // Update related categories endpoint
        updateRelatedCategoriesEndpoint(categorySlug);

        console.log('[Category Landing] Page updated successfully');
    }

    /**
     * Update hero section with category data
     */
    function updateHeroSection(category) {
        // Update background image if available
        const heroSection = document.getElementById('category-hero');
        if (category.hero_image) {
            heroSection.style.backgroundImage = `url('${category.hero_image}')`;
        } else {
            // Use gradient fallback
            heroSection.style.background = 'linear-gradient(135deg, #1a5490 0%, #2c7abf 100%)';
        }

        // Update icon
        const iconElement = document.querySelector('#category-icon i');
        if (category.icon && iconElement) {
            iconElement.className = category.icon;
        }

        // Update category name
        const nameElement = document.getElementById('category-name');
        if (nameElement) {
            nameElement.textContent = category.name || 'Category';
        }

        // Update description
        const descriptionElement = document.getElementById('category-description');
        if (descriptionElement) {
            descriptionElement.textContent = category.description || '';
        }

        // Update tour count badge
        const countElement = document.getElementById('tour-count-badge');
        if (countElement) {
            const count = category.tour_count || 0;
            const plural = count === 1 ? 'tour' : 'tours';
            countElement.textContent = `${count} ${plural} available`;
        }
    }

    /**
     * Update breadcrumb with category name
     */
    function updateBreadcrumb(category) {
        const breadcrumbElement = document.getElementById('category-breadcrumb');
        if (breadcrumbElement) {
            breadcrumbElement.textContent = category.name || 'Category';
        }
    }

    /**
     * Update HTMX endpoints to include category filter
     */
    function updateHTMXEndpoints(slug) {
        // Update tour results endpoint
        const tourResults = document.getElementById('tour-results');
        if (tourResults) {
            const newEndpoint = `/partials/tours/search?category=${slug}&per_page=12`;
            tourResults.setAttribute('hx-get', newEndpoint);

            // Re-process HTMX to trigger the load
            htmx.process(tourResults);
            htmx.trigger(tourResults, 'load');
        }

        // Update form endpoint (already has category hidden field, but update for consistency)
        const form = document.getElementById('tour-filters');
        if (form) {
            const currentAction = form.getAttribute('hx-get');
            // Form already submits category via hidden field, no change needed
        }
    }

    /**
     * Update related categories HTMX endpoint
     */
    function updateRelatedCategoriesEndpoint(slug) {
        const relatedSection = document.getElementById('related-categories');
        if (relatedSection) {
            const newEndpoint = `/partials/categories/related?current=${slug}&limit=5`;
            relatedSection.setAttribute('hx-get', newEndpoint);
            htmx.process(relatedSection);
            htmx.trigger(relatedSection, 'load');
        }
    }

    /**
     * Show error state if category data fails to load
     */
    function showErrorState() {
        const nameElement = document.getElementById('category-name');
        if (nameElement) {
            nameElement.textContent = 'Category Not Found';
        }

        const descriptionElement = document.getElementById('category-description');
        if (descriptionElement) {
            descriptionElement.textContent = 'The category you are looking for could not be found.';
        }

        const tourResults = document.getElementById('tour-results');
        if (tourResults) {
            tourResults.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                    <h3>Error Loading Category</h3>
                    <p>We couldn't load the category data. Please try again later.</p>
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

            // Keep category slug in hidden field
            document.getElementById('category-slug').value = categorySlug;

            // Trigger HTMX reload with category but no other filters
            htmx.ajax('GET', `/partials/tours/search?category=${categorySlug}&per_page=12`, {
                target: '#tour-results',
                swap: 'innerHTML'
            });
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
                                <p>Tours in this category are coming soon! Check out our other categories or browse all tours.</p>
                                <div class="empty-state__actions">
                                    <a href="/tours" class="btn btn--primary">Browse All Tours</a>
                                    <a href="/#categories" class="btn btn--secondary">View Categories</a>
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

    console.log('[Category Landing] Initialization complete');

})();
