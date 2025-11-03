# Blog Listing Page - Phase 2: Frontend HTML/CSS Implementation

**Status**: Ready to implement
**Duration**: ~4-5 hours
**Prerequisites**: Phase 1 (Backend) completed ✅

---

## Overview

Phase 2 focuses on creating the complete CSS styling for the blog listing page, ensuring it's responsive, accessible, and matches the design aesthetic of the existing homepage blog section.

**Key Goals**:
- Create modular, maintainable CSS
- Ensure full responsive design (mobile, tablet, desktop)
- Match existing site design language
- Optimize for performance (minimal CSS, no unused styles)
- Ensure accessibility (focus states, ARIA support)

---

## Step 2.1: Create Blog Listing CSS File (45 min)

### File Location
```
public/blog-listing.css
```

### Implementation

Create the main CSS file for blog listing page with these sections:

```css
/* ============================================
   BLOG LISTING PAGE STYLES
   ============================================ */

/* TABLE OF CONTENTS
   1. CSS Variables
   2. Blog Hero Section
   3. Search & Filter Controls
   4. Blog Grid & Cards
   5. Pagination
   6. Newsletter CTA
   7. Empty States
   8. Loading States
   9. Responsive Design
   ============================================ */

/* ============================================
   1. CSS VARIABLES
   ============================================ */
:root {
    /* Colors - Match existing site palette */
    --blog-primary: #FF6B35;
    --blog-primary-dark: #E55A2B;
    --blog-text: #2C3E50;
    --blog-text-light: #7F8C8D;
    --blog-bg: #FFFFFF;
    --blog-bg-alt: #F8F9FA;
    --blog-border: #E1E8ED;
    --blog-shadow: rgba(0, 0, 0, 0.08);

    /* Typography */
    --font-primary: 'Poppins', sans-serif;
    --font-secondary: 'Inter', sans-serif;
    --font-heading: 'Playfair Display', serif;

    /* Spacing */
    --spacing-xs: 0.5rem;
    --spacing-sm: 1rem;
    --spacing-md: 1.5rem;
    --spacing-lg: 2rem;
    --spacing-xl: 3rem;
    --spacing-2xl: 4rem;

    /* Layout */
    --container-max: 1200px;
    --container-padding: 1.5rem;
    --grid-gap: 2rem;

    /* Border Radius */
    --radius-sm: 6px;
    --radius-md: 12px;
    --radius-lg: 16px;

    /* Transitions */
    --transition-fast: 0.2s ease;
    --transition-base: 0.3s ease;
    --transition-slow: 0.4s ease;
}

/* ============================================
   2. BLOG HERO SECTION
   ============================================ */
.blog-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: var(--spacing-2xl) 0;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
}

.blog-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('/images/patterns/dots.svg') repeat;
    opacity: 0.1;
    pointer-events: none;
}

.blog-hero__eyebrow {
    font-size: 0.875rem;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: var(--spacing-sm);
}

.blog-hero__title {
    font-family: var(--font-heading);
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: var(--spacing-md);
}

.blog-hero__subtitle {
    font-size: clamp(1rem, 2vw, 1.25rem);
    color: rgba(255, 255, 255, 0.95);
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

/* ============================================
   3. SEARCH & FILTER CONTROLS
   ============================================ */
.blog-filters {
    background: var(--blog-bg);
    border-bottom: 1px solid var(--blog-border);
    padding: var(--spacing-lg) 0;
    position: sticky;
    top: 0;
    z-index: 100;
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
}

.blog-filters .container {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-md);
    align-items: center;
}

/* Search Form */
.blog-search {
    position: relative;
    flex: 1 1 300px;
    min-width: 200px;
}

.blog-search input[type="search"] {
    width: 100%;
    padding: 0.75rem 3rem 0.75rem 1rem;
    font-size: 1rem;
    border: 2px solid var(--blog-border);
    border-radius: var(--radius-md);
    background: var(--blog-bg);
    color: var(--blog-text);
    transition: all var(--transition-base);
}

.blog-search input[type="search"]:focus {
    outline: none;
    border-color: var(--blog-primary);
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.blog-search button {
    position: absolute;
    right: 0.5rem;
    top: 50%;
    transform: translateY(-50%);
    background: var(--blog-primary);
    color: white;
    border: none;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: var(--radius-sm);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background var(--transition-fast);
}

.blog-search button:hover {
    background: var(--blog-primary-dark);
}

/* Category Pills */
.blog-categories {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-sm);
    flex: 1 1 auto;
}

.blog-category-btn {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--blog-text);
    background: var(--blog-bg-alt);
    border: 2px solid transparent;
    border-radius: 50px;
    text-decoration: none;
    transition: all var(--transition-fast);
    white-space: nowrap;
}

.blog-category-btn:hover {
    background: white;
    border-color: var(--blog-primary);
    color: var(--blog-primary);
}

.blog-category-btn.active {
    background: var(--blog-primary);
    color: white;
    border-color: var(--blog-primary);
}

/* Sort Dropdown */
.blog-sort {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.blog-sort label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--blog-text-light);
}

.blog-sort select {
    padding: 0.5rem 2rem 0.5rem 0.75rem;
    font-size: 0.875rem;
    border: 2px solid var(--blog-border);
    border-radius: var(--radius-sm);
    background: var(--blog-bg);
    color: var(--blog-text);
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%232C3E50' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.5rem center;
}

/* ============================================
   4. BLOG GRID & CARDS
   ============================================ */
.blog-listing {
    padding: var(--spacing-2xl) 0;
    background: var(--blog-bg-alt);
}

.blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: var(--grid-gap);
    margin-bottom: var(--spacing-xl);
}

/* Blog Card */
.blog-card {
    background: var(--blog-bg);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: 0 2px 8px var(--blog-shadow);
    transition: all var(--transition-base);
    display: flex;
    flex-direction: column;
}

.blog-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
}

.blog-card__link {
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* Card Media */
.blog-card__media {
    position: relative;
    aspect-ratio: 16 / 9;
    overflow: hidden;
    background: var(--blog-bg-alt);
}

.blog-card__media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--transition-slow);
}

.blog-card:hover .blog-card__media img {
    transform: scale(1.05);
}

.blog-card__category {
    position: absolute;
    top: 1rem;
    left: 1rem;
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: var(--blog-primary);
    color: white;
    border-radius: var(--radius-sm);
}

/* Card Content */
.blog-card__content {
    padding: var(--spacing-md);
    flex: 1;
    display: flex;
    flex-direction: column;
}

.blog-card__title {
    font-size: 1.25rem;
    font-weight: 600;
    line-height: 1.4;
    margin-bottom: var(--spacing-sm);
    color: var(--blog-text);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.blog-card:hover .blog-card__title {
    color: var(--blog-primary);
}

.blog-card__excerpt {
    font-size: 0.9375rem;
    line-height: 1.6;
    color: var(--blog-text-light);
    margin-bottom: var(--spacing-md);
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Card Meta */
.blog-card__meta {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    font-size: 0.875rem;
    color: var(--blog-text-light);
    padding-top: var(--spacing-sm);
    border-top: 1px solid var(--blog-border);
}

.blog-card__date,
.blog-card__reading-time {
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

/* ============================================
   5. PAGINATION
   ============================================ */
.blog-pagination {
    display: flex;
    justify-content: center;
    margin-top: var(--spacing-xl);
}

.blog-pagination nav {
    display: flex;
    gap: 0.5rem;
}

.blog-pagination a,
.blog-pagination span {
    padding: 0.5rem 0.875rem;
    font-size: 0.9375rem;
    font-weight: 500;
    border: 2px solid var(--blog-border);
    border-radius: var(--radius-sm);
    background: var(--blog-bg);
    color: var(--blog-text);
    text-decoration: none;
    transition: all var(--transition-fast);
}

.blog-pagination a:hover {
    border-color: var(--blog-primary);
    color: var(--blog-primary);
}

.blog-pagination .active {
    background: var(--blog-primary);
    border-color: var(--blog-primary);
    color: white;
}

/* ============================================
   6. NEWSLETTER CTA
   ============================================ */
.blog-newsletter {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: var(--spacing-2xl) 0;
    text-align: center;
    color: white;
}

.blog-newsletter h2 {
    font-family: var(--font-heading);
    font-size: clamp(1.75rem, 4vw, 2.5rem);
    margin-bottom: var(--spacing-sm);
}

.blog-newsletter p {
    font-size: 1.125rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: var(--spacing-lg);
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.newsletter-form {
    display: flex;
    gap: var(--spacing-sm);
    max-width: 500px;
    margin: 0 auto;
}

.newsletter-form input {
    flex: 1;
    padding: 0.875rem 1.25rem;
    font-size: 1rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: var(--radius-md);
    background: rgba(255, 255, 255, 0.1);
    color: white;
    backdrop-filter: blur(10px);
}

.newsletter-form input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.newsletter-form input:focus {
    outline: none;
    border-color: white;
    background: rgba(255, 255, 255, 0.15);
}

.newsletter-form button {
    padding: 0.875rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    background: white;
    color: #667eea;
    border: none;
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all var(--transition-base);
}

.newsletter-form button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* ============================================
   7. EMPTY STATES
   ============================================ */
.blog-empty {
    text-align: center;
    padding: var(--spacing-2xl) var(--spacing-md);
}

.blog-empty i {
    font-size: 4rem;
    color: var(--blog-text-light);
    margin-bottom: var(--spacing-md);
    opacity: 0.5;
}

.blog-empty h2 {
    font-size: 1.5rem;
    color: var(--blog-text);
    margin-bottom: var(--spacing-sm);
}

.blog-empty p {
    font-size: 1rem;
    color: var(--blog-text-light);
    margin-bottom: var(--spacing-lg);
}

/* ============================================
   8. LOADING STATES (for HTMX)
   ============================================ */
.htmx-indicator {
    display: none;
    text-align: center;
    padding: var(--spacing-md);
    color: var(--blog-text-light);
}

.htmx-request .htmx-indicator {
    display: block;
}

.htmx-indicator i {
    margin-right: var(--spacing-xs);
}

/* Skeleton Loading for Cards */
.blog-card.skeleton {
    pointer-events: none;
}

.blog-card.skeleton .blog-card__media,
.blog-card.skeleton .blog-card__title,
.blog-card.skeleton .blog-card__excerpt {
    background: linear-gradient(
        90deg,
        var(--blog-bg-alt) 25%,
        #e0e0e0 50%,
        var(--blog-bg-alt) 75%
    );
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* ============================================
   9. RESPONSIVE DESIGN
   ============================================ */

/* Tablet (768px and up) */
@media (min-width: 768px) {
    .blog-hero {
        padding: var(--spacing-2xl) 0 var(--spacing-xl);
    }

    .blog-filters .container {
        flex-wrap: nowrap;
    }

    .blog-search {
        flex: 0 1 350px;
    }

    .blog-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .newsletter-form {
        max-width: 600px;
    }
}

/* Desktop (1024px and up) */
@media (min-width: 1024px) {
    .blog-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .blog-card__title {
        font-size: 1.375rem;
    }
}

/* Mobile (below 768px) */
@media (max-width: 767px) {
    .blog-filters {
        position: static;
    }

    .blog-filters .container {
        flex-direction: column;
        align-items: stretch;
    }

    .blog-search {
        order: -1;
    }

    .blog-categories {
        overflow-x: auto;
        flex-wrap: nowrap;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .blog-categories::-webkit-scrollbar {
        display: none;
    }

    .blog-sort {
        justify-content: space-between;
    }

    .newsletter-form {
        flex-direction: column;
    }
}

/* Print Styles */
@media print {
    .blog-hero,
    .blog-filters,
    .blog-newsletter,
    .blog-pagination {
        display: none;
    }

    .blog-card {
        break-inside: avoid;
        page-break-inside: avoid;
    }
}
```

### CSS File Organization

The CSS is organized into logical sections:
1. **CSS Variables** - Centralized design tokens
2. **Blog Hero** - Header section styling
3. **Search & Filters** - Interactive controls
4. **Blog Grid & Cards** - Main content area
5. **Pagination** - Navigation between pages
6. **Newsletter CTA** - Call-to-action section
7. **Empty States** - No results messaging
8. **Loading States** - HTMX loading indicators
9. **Responsive Design** - Mobile/tablet/desktop breakpoints

---

## Step 2.2: Add Missing Utility Styles (15 min)

### File Location
Add to existing `public/style.css` or create `public/utilities.css`

### Implementation

```css
/* ============================================
   UTILITY CLASSES FOR BLOG
   ============================================ */

/* Container */
.container {
    max-width: var(--container-max, 1200px);
    margin: 0 auto;
    padding: 0 var(--container-padding, 1.5rem);
}

/* Buttons */
.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    border-radius: var(--radius-md, 12px);
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    font-family: inherit;
}

.btn--primary {
    background: var(--blog-primary, #FF6B35);
    color: white;
}

.btn--primary:hover {
    background: var(--blog-primary-dark, #E55A2B);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.btn--outline {
    background: transparent;
    color: var(--blog-primary, #FF6B35);
    border: 2px solid var(--blog-primary, #FF6B35);
}

.btn--outline:hover {
    background: var(--blog-primary, #FF6B35);
    color: white;
}

.btn--accent {
    background: #667eea;
    color: white;
}

.btn--accent:hover {
    background: #5568d3;
}

/* Accessibility */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Focus Visible */
:focus-visible {
    outline: 3px solid var(--blog-primary, #FF6B35);
    outline-offset: 2px;
}

/* Smooth Scroll */
html {
    scroll-behavior: smooth;
}

@media (prefers-reduced-motion: reduce) {
    html {
        scroll-behavior: auto;
    }

    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
```

---

## Step 2.3: Create Responsive Navigation Styles (20 min)

### File Location
`public/navigation.css` (if separate) or add to `style.css`

### Implementation

```css
/* ============================================
   SITE HEADER & NAVIGATION
   ============================================ */

.site-header {
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.nav {
    padding: 1rem 0;
}

.nav .container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
}

.nav__logo {
    text-decoration: none;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--blog-text, #2C3E50);
}

.nav__logo strong {
    color: var(--blog-primary, #FF6B35);
}

.nav__menu {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 2rem;
}

.nav__menu a {
    text-decoration: none;
    color: var(--blog-text, #2C3E50);
    font-weight: 500;
    font-size: 1rem;
    transition: color 0.2s ease;
    position: relative;
}

.nav__menu a:hover,
.nav__menu a.active {
    color: var(--blog-primary, #FF6B35);
}

.nav__menu a.active::after {
    content: '';
    position: absolute;
    bottom: -0.5rem;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--blog-primary, #FF6B35);
}

.nav__cta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    font-size: 0.9375rem;
}

.nav__toggle {
    display: none;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
}

.nav__toggle-icon {
    display: block;
    width: 24px;
    height: 2px;
    background: var(--blog-text, #2C3E50);
    position: relative;
}

.nav__toggle-icon::before,
.nav__toggle-icon::after {
    content: '';
    position: absolute;
    width: 24px;
    height: 2px;
    background: var(--blog-text, #2C3E50);
    transition: all 0.3s ease;
}

.nav__toggle-icon::before {
    top: -8px;
}

.nav__toggle-icon::after {
    bottom: -8px;
}

/* Mobile Navigation */
@media (max-width: 1023px) {
    .nav__menu {
        position: fixed;
        top: 0;
        right: -100%;
        width: 80%;
        max-width: 400px;
        height: 100vh;
        background: white;
        flex-direction: column;
        padding: 5rem 2rem 2rem;
        box-shadow: -4px 0 12px rgba(0, 0, 0, 0.1);
        transition: right 0.3s ease;
    }

    .nav__menu.active {
        right: 0;
    }

    .nav__menu a::after {
        display: none;
    }

    .nav__toggle {
        display: block;
        z-index: 1001;
    }

    .nav__cta {
        order: -1;
    }
}

@media (max-width: 767px) {
    .nav .container {
        gap: 1rem;
    }

    .nav__logo {
        font-size: 1.25rem;
    }

    .nav__cta {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }

    .nav__cta svg {
        display: none;
    }
}
```

---

## Step 2.4: Create Footer Styles (20 min)

### File Location
Add to `public/style.css`

### Implementation

```css
/* ============================================
   SITE FOOTER
   ============================================ */

.site-footer {
    background: #2C3E50;
    color: rgba(255, 255, 255, 0.8);
    padding: 3rem 0 1.5rem;
}

.footer-main {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-brand {
    grid-column: span 1;
}

.footer-brand__link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    color: white;
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.footer-brand__logo {
    font-size: 1.5rem;
    color: var(--blog-primary, #FF6B35);
}

.footer-brand__tagline {
    font-size: 0.9375rem;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.footer-brand__contact {
    font-style: normal;
    font-size: 0.875rem;
    line-height: 1.8;
}

.footer-brand__contact a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: color 0.2s ease;
}

.footer-brand__contact a:hover {
    color: var(--blog-primary, #FF6B35);
}

.footer-brand__location {
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.footer-nav__title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: white;
    margin-bottom: 1rem;
}

.footer-nav__list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-nav__list li {
    margin-bottom: 0.75rem;
}

.footer-nav__list a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-size: 0.875rem;
    transition: color 0.2s ease;
}

.footer-nav__list a:hover {
    color: var(--blog-primary, #FF6B35);
}

.footer-social__title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: white;
    margin-bottom: 1rem;
}

.footer-social__list {
    display: flex;
    gap: 0.75rem;
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-social__list a {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: white;
    text-decoration: none;
    transition: all 0.2s ease;
}

.footer-social__list a:hover {
    background: var(--blog-primary, #FF6B35);
    transform: translateY(-2px);
}

.footer-bottom {
    padding-top: 1.5rem;
}

.footer-bottom__wrap {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.875rem;
}

.footer-bottom__legal a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
}

.footer-bottom__legal a:hover {
    color: white;
}

/* Responsive Footer */
@media (max-width: 1023px) {
    .footer-main {
        grid-template-columns: repeat(2, 1fr);
    }

    .footer-brand {
        grid-column: span 2;
    }
}

@media (max-width: 767px) {
    .footer-main {
        grid-template-columns: 1fr;
    }

    .footer-brand {
        grid-column: span 1;
    }

    .footer-bottom__wrap {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}
```

---

## Step 2.5: Add Custom Pagination Styles (15 min)

Laravel's default pagination needs custom styling to match our design.

### File Location
Add to `public/blog-listing.css`

### Implementation

```css
/* ============================================
   LARAVEL PAGINATION STYLES
   ============================================ */

/* Override default Laravel pagination */
.pagination {
    display: flex;
    justify-content: center;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 0.5rem;
}

.pagination li {
    margin: 0;
}

.pagination a,
.pagination span {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 0.75rem;
    font-size: 0.9375rem;
    font-weight: 500;
    border: 2px solid var(--blog-border);
    border-radius: var(--radius-sm);
    background: var(--blog-bg);
    color: var(--blog-text);
    text-decoration: none;
    transition: all var(--transition-fast);
}

.pagination a:hover {
    border-color: var(--blog-primary);
    color: var(--blog-primary);
    background: rgba(255, 107, 53, 0.05);
}

.pagination .active span {
    background: var(--blog-primary);
    border-color: var(--blog-primary);
    color: white;
}

.pagination .disabled span {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination .disabled span:hover {
    border-color: var(--blog-border);
    color: var(--blog-text);
    background: var(--blog-bg);
}

/* Previous/Next buttons */
.pagination li:first-child a,
.pagination li:last-child a {
    font-weight: 600;
}

/* Mobile Pagination */
@media (max-width: 767px) {
    .pagination {
        flex-wrap: wrap;
    }

    .pagination a,
    .pagination span {
        min-width: 36px;
        height: 36px;
        padding: 0 0.5rem;
        font-size: 0.875rem;
    }
}
```

---

## Step 2.6: Add Dark Mode Support (Optional - 20 min)

### File Location
Add to `public/blog-listing.css`

### Implementation

```css
/* ============================================
   DARK MODE SUPPORT (Optional)
   ============================================ */

@media (prefers-color-scheme: dark) {
    :root {
        --blog-text: #E5E7EB;
        --blog-text-light: #9CA3AF;
        --blog-bg: #1F2937;
        --blog-bg-alt: #111827;
        --blog-border: #374151;
        --blog-shadow: rgba(0, 0, 0, 0.3);
    }

    .blog-hero {
        background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
    }

    .blog-filters {
        background: rgba(31, 41, 55, 0.95);
    }

    .blog-search input[type="search"],
    .blog-sort select {
        background: var(--blog-bg-alt);
        color: var(--blog-text);
    }

    .blog-category-btn {
        background: var(--blog-bg-alt);
        color: var(--blog-text);
    }

    .blog-card {
        background: var(--blog-bg);
    }

    .site-header {
        background: var(--blog-bg);
    }
}
```

---

## Step 2.7: Performance Optimization (15 min)

### Create Minified CSS

**Option 1: Manual concatenation**
```bash
# Concatenate and minify CSS files
cat public/style.css public/blog-listing.css public/navigation.css > public/blog-all.css

# Use online tool or cssnano to minify:
# https://cssnano.co/playground/
```

**Option 2: Laravel Mix (if using)**
```javascript
// webpack.mix.js
mix.styles([
    'public/style.css',
    'public/blog-listing.css',
    'public/navigation.css'
], 'public/css/blog-all.min.css');
```

### Add Preload for Critical CSS

Update `blog/index.blade.php`:
```blade
<head>
    <!-- Preload critical CSS -->
    <link rel="preload" href="{{ asset('blog-listing.css') }}" as="style">

    <!-- Existing stylesheets -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="{{ asset('blog-listing.css') }}">
</head>
```

---

## Step 2.8: Testing Checklist (30 min)

### Visual Testing

Test the following on **Chrome, Firefox, Safari**:

**Desktop (1920px)**
- [ ] Blog hero section displays correctly
- [ ] Search bar and filters align properly
- [ ] Blog grid shows 3 columns
- [ ] Cards have proper spacing and hover effects
- [ ] Pagination displays centered
- [ ] Newsletter CTA looks good
- [ ] Header and footer render correctly

**Tablet (768px)**
- [ ] Blog grid shows 2 columns
- [ ] Filters wrap appropriately
- [ ] Cards maintain proper proportions
- [ ] Navigation adapts to smaller screen

**Mobile (375px)**
- [ ] Blog grid shows 1 column
- [ ] Category pills scroll horizontally
- [ ] Search and filters stack vertically
- [ ] Newsletter form stacks
- [ ] Mobile navigation menu works
- [ ] Touch targets are at least 44x44px

### Accessibility Testing

- [ ] All interactive elements have focus states
- [ ] Color contrast meets WCAG AA standards
- [ ] Keyboard navigation works for all controls
- [ ] Screen reader can access all content
- [ ] Form labels are properly associated

### Performance Testing

- [ ] CSS file size < 50KB (unminified)
- [ ] No layout shifts (CLS score)
- [ ] Smooth animations (60fps)
- [ ] Images lazy load properly

---

## Implementation Timeline

| Step | Task | Duration | Status |
|------|------|----------|--------|
| 2.1 | Create blog-listing.css | 45 min | ⏳ Pending |
| 2.2 | Add utility styles | 15 min | ⏳ Pending |
| 2.3 | Create navigation styles | 20 min | ⏳ Pending |
| 2.4 | Create footer styles | 20 min | ⏳ Pending |
| 2.5 | Add pagination styles | 15 min | ⏳ Pending |
| 2.6 | Dark mode support (optional) | 20 min | ⏳ Pending |
| 2.7 | Performance optimization | 15 min | ⏳ Pending |
| 2.8 | Testing & QA | 30 min | ⏳ Pending |
| **Total** | | **~3 hours** | |

---

## Expected Outcome

After completing Phase 2, you will have:

1. ✅ **Fully styled blog listing page** matching the design aesthetic
2. ✅ **Responsive design** working on all devices (mobile, tablet, desktop)
3. ✅ **Accessible UI** with proper focus states and keyboard navigation
4. ✅ **Optimized CSS** with minimal file size and fast load times
5. ✅ **Consistent branding** matching the existing site design
6. ✅ **Professional UI/UX** with smooth transitions and hover effects

---

## Next Steps

After Phase 2 completion, proceed to:
- **Phase 3**: JavaScript enhancements (HTMX interactions, infinite scroll)
- **Phase 4**: SEO & performance optimization
- **Phase 5**: Testing & deployment

---

## Notes

- All color values should match existing site palette
- Use CSS custom properties for easy theming
- Ensure all styles are mobile-first
- Test on real devices, not just browser DevTools
- Consider using a CSS linter (Stylelint) for code quality

---

**Last Updated**: November 1, 2025
**Status**: Ready for implementation
**Estimated Completion**: ~3-4 hours
