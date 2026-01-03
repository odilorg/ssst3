{{--
    Mobile Section Navigation Tabs (Fixed Position) - HYBRID Icon + Label

    Displays a FIXED horizontal scrollable tab bar on mobile devices only.
    Always visible at the top of the viewport while scrolling.
    Each tab shows an icon + label for better UX.
    Active tab is highlighted based on scroll position.

    Props:
    - $tour: Tour model (used to conditionally show tabs)
--}}

@php
    // Build tabs array with icons
    $tabs = [];

    // Overview - always present
    $tabs[] = ['id' => 'overview', 'label' => 'Overview', 'icon' => 'info'];

    // Highlights - always present
    $tabs[] = ['id' => 'highlights', 'label' => 'Highlights', 'icon' => 'star'];

    // Itinerary - check if tour has itinerary
    if ($tour->itinerary && count($tour->itinerary) > 0) {
        $tabs[] = ['id' => 'itinerary', 'label' => 'Itinerary', 'icon' => 'route'];
    }

    // Included/Excluded - always present
    $tabs[] = ['id' => 'includes', 'label' => 'Included', 'icon' => 'check'];

    // Meeting Point - always present
    $tabs[] = ['id' => 'meeting-point', 'label' => 'Meeting', 'icon' => 'map-pin'];

    // FAQ - check if tour has FAQs
    if ($tour->faqs && count($tour->faqs) > 0) {
        $tabs[] = ['id' => 'faq', 'label' => 'FAQ', 'icon' => 'question'];
    }

    // Reviews - check if tour has reviews
    if ($tour->review_count > 0) {
        $tabs[] = ['id' => 'reviews', 'label' => 'Reviews', 'icon' => 'chat'];
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
               aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
               aria-label="{{ $tab['label'] }}">
                <span class="mobile-section-tabs__icon">
                    @switch($tab['icon'])
                        @case('info')
                            {{-- Info/Document icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                            </svg>
                            @break
                        @case('star')
                            {{-- Star icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                            </svg>
                            @break
                        @case('route')
                            {{-- Route/List icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 4.75A.75.75 0 016.75 4h10.5a.75.75 0 010 1.5H6.75A.75.75 0 016 4.75zM6 10a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H6.75A.75.75 0 016 10zm0 5.25a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H6.75a.75.75 0 01-.75-.75zM1.99 4.75a1 1 0 011-1h.01a1 1 0 010 2h-.01a1 1 0 01-1-1zm1 5.25a1 1 0 100 2h.01a1 1 0 100-2h-.01zm0 5.25a1 1 0 100 2h.01a1 1 0 100-2h-.01z" clip-rule="evenodd" />
                            </svg>
                            @break
                        @case('check')
                            {{-- Check circle icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                            @break
                        @case('map-pin')
                            {{-- Map pin icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.976.544l.062.029.018.008.006.003zM10 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z" clip-rule="evenodd" />
                            </svg>
                            @break
                        @case('question')
                            {{-- Question mark icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM8.94 6.94a.75.75 0 11-1.061-1.061 3 3 0 112.871 5.026v.345a.75.75 0 01-1.5 0v-.5c0-.72.57-1.172 1.081-1.287A1.5 1.5 0 108.94 6.94zM10 15a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                            @break
                        @case('chat')
                            {{-- Chat bubble icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 2c-2.236 0-4.43.18-6.57.524C1.993 2.755 1 4.014 1 5.426v5.148c0 1.413.993 2.67 2.43 2.902.848.137 1.705.248 2.57.331v3.443a.75.75 0 001.28.53l3.58-3.579a.78.78 0 01.527-.224 41.202 41.202 0 005.183-.5c1.437-.232 2.43-1.49 2.43-2.903V5.426c0-1.413-.993-2.67-2.43-2.902A41.289 41.289 0 0010 2zm0 7a1 1 0 100-2 1 1 0 000 2zM8 8a1 1 0 11-2 0 1 1 0 012 0zm5 1a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                            @break
                        @default
                            {{-- Default circle icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16z" clip-rule="evenodd" />
                            </svg>
                    @endswitch
                </span>
                <span class="mobile-section-tabs__label">{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>
    {{-- Right-edge fade gradient to indicate scrollable content --}}
    <div class="mobile-section-tabs__scroll-hint" aria-hidden="true"></div>
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
    gap: 5px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 500;
    color: #64748b;
    background: #f1f5f9;
    border-radius: 18px;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    min-height: 32px;
}

.mobile-section-tabs__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.mobile-section-tabs__icon svg {
    width: 14px;
    height: 14px;
}

.mobile-section-tabs__label {
    line-height: 1;
}

.mobile-section-tabs__tab:hover,
.mobile-section-tabs__tab:active {
    color: #0D4C92;
    background: #e0f2fe;
}

.mobile-section-tabs__tab:focus {
    outline: 2px solid #0D4C92;
    outline-offset: 2px;
}

.mobile-section-tabs__tab.is-active {
    color: #ffffff;
    background: #0D4C92;
    font-weight: 600;
    border-color: #0D4C92;
}

.mobile-section-tabs__tab.is-active .mobile-section-tabs__icon svg {
    color: #ffffff;
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

/* Scroll hint - right-edge fade gradient */
.mobile-section-tabs__scroll-hint {
    display: none; /* Hidden by default (desktop) */
}

@media (max-width: 767px) {
    .mobile-section-tabs__scroll-hint {
        display: block;
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        width: 32px;
        background: linear-gradient(to right, transparent 0%, rgba(255, 255, 255, 0.9) 60%, #ffffff 100%);
        pointer-events: none;
        opacity: 1;
        transition: opacity 0.2s ease;
        z-index: 2;
    }

    /* Hide scroll hint when scrolled to end */
    .mobile-section-tabs__scroll-hint.is-hidden {
        opacity: 0;
    }

    /* Ensure nav has relative positioning for the absolute overlay */
    .mobile-section-tabs {
        position: fixed; /* Already fixed, just ensure it */
    }
}

/* First-load nudge animation */
@keyframes scrollNudge {
    0% { transform: translateX(0); }
    40% { transform: translateX(-10px); }
    100% { transform: translateX(0); }
}

.mobile-section-tabs__container.nudge-animate {
    animation: scrollNudge 0.4s ease-out;
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
    var container = tabsNav.querySelector('.mobile-section-tabs__container');
    var scrollHint = tabsNav.querySelector('.mobile-section-tabs__scroll-hint');
    var sections = [];
    var isScrolling = false;
    var scrollTimeout;

    // ============================================
    // SCROLL HINT: Show/hide right-edge fade
    // ============================================
    function updateScrollHint() {
        if (!container || !scrollHint) return;

        // Check if container is scrolled to the end (with small tolerance)
        var isAtEnd = container.scrollLeft + container.clientWidth >= container.scrollWidth - 5;

        // Check if container actually overflows
        var hasOverflow = container.scrollWidth > container.clientWidth;

        if (!hasOverflow || isAtEnd) {
            scrollHint.classList.add('is-hidden');
        } else {
            scrollHint.classList.remove('is-hidden');
        }
    }

    // Update hint on container horizontal scroll
    if (container) {
        container.addEventListener('scroll', updateScrollHint, { passive: true });
        // Initial check
        updateScrollHint();
    }

    // ============================================
    // FIRST-LOAD NUDGE: Subtle animation on load
    // ============================================
    function playNudgeAnimation() {
        if (!container) return;

        // Only play if container has overflow (scrollable)
        if (container.scrollWidth <= container.clientWidth) return;

        // Add animation class
        container.classList.add('nudge-animate');

        // Remove class after animation completes
        setTimeout(function() {
            container.classList.remove('nudge-animate');
        }, 450);
    }

    // Play nudge after short delay (allows page to settle)
    setTimeout(playNudgeAnimation, 600);

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
            tab.removeAttribute('aria-current');
        });
        activeTab.classList.add('is-active');
        activeTab.setAttribute('aria-selected', 'true');
        activeTab.setAttribute('aria-current', 'true');
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
