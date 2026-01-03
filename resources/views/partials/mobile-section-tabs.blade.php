{{--
    Mobile Section Navigation Tabs (Fixed Position)

    Displays a FIXED horizontal scrollable tab bar on mobile devices only.
    Always visible at the top of the viewport while scrolling.
    Each tab scrolls to its corresponding section on the page.
    Active tab is highlighted based on scroll position.

    Props:
    - $tour: Tour model (used to conditionally show tabs)
--}}

@php
    // Build tabs array based on available sections
    $tabs = [];

    // Overview - always present
    $tabs[] = ['id' => 'overview', 'label' => 'Overview'];

    // Highlights - always present
    $tabs[] = ['id' => 'highlights', 'label' => 'Highlights'];

    // Itinerary - check if tour has itinerary
    if ($tour->itinerary && count($tour->itinerary) > 0) {
        $tabs[] = ['id' => 'itinerary', 'label' => 'Itinerary'];
    }

    // Included/Excluded - always present
    $tabs[] = ['id' => 'includes', 'label' => 'Included'];

    // Meeting Point - always present
    $tabs[] = ['id' => 'meeting-point', 'label' => 'Meeting'];

    // FAQ - check if tour has FAQs
    if ($tour->faqs && count($tour->faqs) > 0) {
        $tabs[] = ['id' => 'faq', 'label' => 'FAQ'];
    }

    // Reviews - check if tour has reviews
    if ($tour->review_count > 0) {
        $tabs[] = ['id' => 'reviews', 'label' => 'Reviews'];
    }
@endphp

{{-- Only render if we have more than 1 tab --}}
@if(count($tabs) > 1)
<nav class="mobile-section-tabs" id="mobile-section-tabs" aria-label="Page sections">
    <div class="mobile-section-tabs__container">
        @foreach($tabs as $index => $tab)
            <a href="#{{ $tab['id'] }}"
               class="mobile-section-tabs__tab{{ $index === 0 ? ' is-active' : '' }}"
               data-section-id="{{ $tab['id'] }}"
               role="tab"
               aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>
</nav>

<style>
/* CSS Variable for bar height */
:root {
    --mobile-tabs-height: 52px;
}

/* Mobile Section Tabs - FIXED position, always visible on mobile */
.mobile-section-tabs {
    display: none; /* Hidden by default (desktop) */
}

/* Show only on mobile (less than 768px) */
@media (max-width: 767px) {
    .mobile-section-tabs {
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 50;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        height: var(--mobile-tabs-height);
    }

    /* Add padding to body/main content to prevent overlap */
    body {
        padding-top: var(--mobile-tabs-height);
    }

    /* Add scroll margin to sections for proper anchor scrolling */
    #overview,
    #highlights,
    #itinerary,
    #includes,
    #cancellation,
    #meeting-point,
    #know-before,
    #faq,
    #reviews {
        scroll-margin-top: calc(var(--mobile-tabs-height) + 16px);
    }

    /* Ensure tour header sections also have proper offset */
    .tour-header,
    .tour-hero {
        scroll-margin-top: var(--mobile-tabs-height);
    }
}

.mobile-section-tabs__container {
    display: flex;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE/Edge */
    gap: 6px;
    padding: 10px 12px;
    height: 100%;
    align-items: center;
}

/* Hide scrollbar for Chrome/Safari */
.mobile-section-tabs__container::-webkit-scrollbar {
    display: none;
}

.mobile-section-tabs__tab {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 500;
    color: #6b7280;
    background: #f3f4f6;
    border-radius: 20px;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.mobile-section-tabs__tab:hover,
.mobile-section-tabs__tab:active {
    color: #0D4C92;
    background: #EBF5FF;
}

.mobile-section-tabs__tab.is-active {
    color: #ffffff;
    background: #0D4C92;
    font-weight: 600;
    border-color: #0D4C92;
}

/* iOS safe area support */
@supports (padding-top: env(safe-area-inset-top)) {
    @media (max-width: 767px) {
        .mobile-section-tabs {
            padding-top: env(safe-area-inset-top);
            height: calc(var(--mobile-tabs-height) + env(safe-area-inset-top));
        }

        body {
            padding-top: calc(var(--mobile-tabs-height) + env(safe-area-inset-top));
        }

        #overview,
        #highlights,
        #itinerary,
        #includes,
        #cancellation,
        #meeting-point,
        #know-before,
        #faq,
        #reviews {
            scroll-margin-top: calc(var(--mobile-tabs-height) + env(safe-area-inset-top) + 16px);
        }
    }
}
</style>

<script>
(function() {
    'use strict';

    var tabsNav = document.getElementById('mobile-section-tabs');
    if (!tabsNav) return;

    // Get bar height from CSS variable
    var barHeight = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--mobile-tabs-height')) || 52;

    var tabs = tabsNav.querySelectorAll('.mobile-section-tabs__tab');
    var sections = [];
    var isScrolling = false;
    var scrollTimeout;

    // Collect section elements
    tabs.forEach(function(tab) {
        var sectionId = tab.getAttribute('data-section-id');
        var section = document.getElementById(sectionId);
        if (section) {
            sections.push({
                id: sectionId,
                element: section,
                tab: tab
            });
        }
    });

    // Smooth scroll to section on tab click
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            e.preventDefault();

            var targetId = this.getAttribute('data-section-id');
            var targetSection = document.getElementById(targetId);

            if (targetSection) {
                // Set flag to prevent scroll observer from interfering
                isScrolling = true;

                // Update active tab immediately
                setActiveTab(this);

                // Calculate scroll position accounting for fixed header
                var sectionTop = targetSection.getBoundingClientRect().top + window.pageYOffset;
                var offsetPosition = sectionTop - barHeight - 16; // 16px extra padding

                // Smooth scroll to calculated position
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                // Reset flag after scroll completes
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(function() {
                    isScrolling = false;
                }, 800);

                // Scroll the tab into view within the horizontal container
                scrollTabIntoView(this);
            }
        });
    });

    // Set active tab
    function setActiveTab(activeTab) {
        tabs.forEach(function(tab) {
            tab.classList.remove('is-active');
            tab.setAttribute('aria-selected', 'false');
        });
        activeTab.classList.add('is-active');
        activeTab.setAttribute('aria-selected', 'true');
    }

    // Scroll tab into view within horizontal container
    function scrollTabIntoView(tab) {
        var container = tabsNav.querySelector('.mobile-section-tabs__container');
        if (!container) return;

        var tabRect = tab.getBoundingClientRect();
        var containerRect = container.getBoundingClientRect();

        // Check if tab is out of view
        if (tabRect.left < containerRect.left) {
            container.scrollLeft -= (containerRect.left - tabRect.left + 20);
        } else if (tabRect.right > containerRect.right) {
            container.scrollLeft += (tabRect.right - containerRect.right + 20);
        }
    }

    // IntersectionObserver to update active tab on scroll
    if ('IntersectionObserver' in window && sections.length > 0) {
        // Calculate rootMargin to account for fixed bar height
        var topMargin = '-' + (barHeight + 20) + 'px';

        var observerOptions = {
            root: null,
            rootMargin: topMargin + ' 0px -55% 0px', // Account for fixed header
            threshold: 0
        };

        var observer = new IntersectionObserver(function(entries) {
            if (isScrolling) return; // Don't update during programmatic scroll

            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    // Find the tab for this section
                    var sectionId = entry.target.id;
                    var matchingSection = sections.find(function(s) {
                        return s.id === sectionId;
                    });

                    if (matchingSection) {
                        setActiveTab(matchingSection.tab);
                        scrollTabIntoView(matchingSection.tab);
                    }
                }
            });
        }, observerOptions);

        sections.forEach(function(section) {
            observer.observe(section.element);
        });
    }
})();
</script>
@endif
