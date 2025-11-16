/**
 * Blog Listing Page JavaScript
 * Handles interactions on the blog listing page
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        // Initialize components
        initCategoryFilters();
        initSearchEnhancements();
        initCardAnimations();
        initLoadingStates();
    }

    /**
     * Add loading indicators
     */
    function initLoadingStates() {
        const searchForm = document.querySelector('.blog-search');
        const sortForm = document.querySelector('.blog-sort');
        const categoryButtons = document.querySelectorAll('.blog-category-btn');

        // Add loading on search form submit
        if (searchForm) {
            searchForm.addEventListener('submit', function() {
                showLoadingOverlay();
            });
        }

        // Add loading on sort change
        if (sortForm) {
            sortForm.addEventListener('submit', function() {
                showLoadingOverlay();
            });
        }

        // Add loading on category click
        categoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                showLoadingOverlay();
            });
        });

        // Add loading on pagination click
        document.querySelectorAll('.blog-pagination a').forEach(link => {
            link.addEventListener('click', function() {
                showLoadingOverlay();
            });
        });
    }

    /**
     * Show loading overlay
     */
    function showLoadingOverlay() {
        // Create overlay if it doesn't exist
        let overlay = document.querySelector('.blog-loading-overlay');
        
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'blog-loading-overlay';
            overlay.innerHTML = `
                <div class="blog-loading-spinner">
                    <div class="spinner"></div>
                    <p>Loading...</p>
                </div>
            `;
            document.body.appendChild(overlay);

            // Add CSS dynamically
            const style = document.createElement('style');
            style.textContent = `
                .blog-loading-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(255, 255, 255, 0.9);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                    opacity: 0;
                    transition: opacity 0.3s ease;
                }
                .blog-loading-overlay.active {
                    opacity: 1;
                }
                .blog-loading-spinner {
                    text-align: center;
                }
                .blog-loading-spinner .spinner {
                    width: 50px;
                    height: 50px;
                    margin: 0 auto 16px;
                    border: 4px solid #f0f0f0;
                    border-top: 4px solid #1C54B2;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                .blog-loading-spinner p {
                    color: #4A5568;
                    font-size: 16px;
                    font-weight: 500;
                }
            `;
            document.head.appendChild(style);
        }

        // Show overlay
        setTimeout(() => {
            overlay.classList.add('active');
        }, 10);
    }

    /**
     * Category Filter Enhancements
     */
    function initCategoryFilters() {
        const categoryButtons = document.querySelectorAll('.blog-category-btn');

        categoryButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Add visual feedback
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 100);
            });
        });
    }

    /**
     * Search Enhancements
     */
    function initSearchEnhancements() {
        const searchInput = document.querySelector('.blog-search input[type="search"]');

        if (!searchInput) return;

        // Clear search button
        searchInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                this.setAttribute('aria-label', `Search for: ${this.value}`);
            }
        });

        // Handle Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    }

    /**
     * Card Animations
     */
    function initCardAnimations() {
        const blogCards = document.querySelectorAll('.blog-card');

        if (!blogCards.length) return;

        // Add staggered fade-in animation
        blogCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';

            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 50);
        });

        // Track card clicks for analytics (if gtag is available)
        blogCards.forEach(card => {
            card.addEventListener('click', function() {
                const postId = this.getAttribute('data-post-id');
                const postTitle = this.querySelector('.blog-card__title')?.textContent;

                if (typeof gtag !== 'undefined') {
                    gtag('event', 'blog_card_click', {
                        'event_category': 'Blog',
                        'event_label': postTitle,
                        'value': postId
                    });
                }
            });
        });
    }

    /**
     * Scroll to top on pagination
     */
    function scrollToResults() {
        const blogGrid = document.querySelector('.blog-grid');
        if (blogGrid) {
            const offset = 100; // Offset for sticky header
            const elementPosition = blogGrid.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - offset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    }

    // Expose scrollToResults globally for pagination links
    window.scrollToBlogResults = scrollToResults;

    /**
     * Handle URL parameters on load
     * Scroll to results if coming from pagination or filter
     */
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('page') || urlParams.has('category') || urlParams.has('search')) {
        setTimeout(scrollToResults, 100);
    }

})();
