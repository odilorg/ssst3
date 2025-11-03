# 🎨 Comprehensive Website Design System Guide
## Modern, Clean, Consistent Branding - 2024/2025 Edition

---

**Purpose**: This guide provides AI coders and developers with authoritative design principles and best practices for creating modern, clean, and consistent websites.

**Sources**: Material Design 3 (Google), Apple Human Interface Guidelines, Nielsen Norman Group, WCAG, Design Systems Community

**Last Updated**: October 2025

---

## Table of Contents

1. [Color System](#1-color-system)
2. [Spacing & Layout](#2-spacing--layout)
3. [Typography](#3-typography)
4. [Components & Patterns](#4-components--patterns)
5. [Responsive Design](#5-responsive-design)
6. [Accessibility](#6-accessibility)
7. [Modern Design Trends](#7-modern-design-trends)
8. [Quick Reference](#8-quick-reference)

---

# 1. Color System

## 1.1 Foundation Principles

### Core Concept (Material Design 3)
**Source**: https://m3.material.io/styles/color/roles

> Modern color systems use **luminance** rather than hue for contrast. This ensures accessibility regardless of chosen palette.

### The 60-30-10 Rule
- **60%** - Primary/Neutral color (backgrounds, main surfaces)
- **30%** - Secondary color (supporting elements)
- **10%** - Accent color (CTAs, highlights, important elements)

---

## 1.2 Color Palette Structure

### Minimum Required Colors

```
Primary Colors:
├── Primary         (Main brand color)
├── Primary Light   (+20% lighter)
├── Primary Dark    (-20% darker)
├── On-Primary      (Text on primary background)

Secondary Colors:
├── Secondary       (Supporting brand color)
├── Secondary Light (+20% lighter)
├── Secondary Dark  (-20% darker)
├── On-Secondary    (Text on secondary background)

Neutral Colors:
├── Background      (Page background - usually white/near-white)
├── Surface         (Card backgrounds)
├── Surface Variant (Alternate surfaces)
├── On-Background   (Text on background)
├── On-Surface      (Text on surfaces)

Semantic Colors:
├── Success         (#10B981 - Green)
├── Error           (#EF4444 - Red)
├── Warning         (#F59E0B - Amber/Orange)
├── Info            (#3B82F6 - Blue)
```

---

## 1.3 Practical Color Scales

### Gray Scale (Essential for Modern Design)
```css
--gray-50:  #F9FAFB;  /* Lightest - subtle backgrounds */
--gray-100: #F3F4F6;  /* Very light - hover states */
--gray-200: #E5E7EB;  /* Light - borders */
--gray-300: #D1D5DB;  /* Medium light - disabled states */
--gray-400: #9CA3AF;  /* Medium - placeholders */
--gray-500: #6B7280;  /* Base - secondary text */
--gray-600: #4B5563;  /* Medium dark - body text */
--gray-700: #374151;  /* Dark - headings */
--gray-800: #1F2937;  /* Darker - emphasis */
--gray-900: #111827;  /* Darkest - high emphasis */
```

### Example Primary Scale (Blue)
```css
--primary-50:  #EFF6FF;
--primary-100: #DBEAFE;
--primary-200: #BFDBFE;
--primary-300: #93C5FD;
--primary-400: #60A5FA;
--primary-500: #3B82F6;  /* Base primary */
--primary-600: #2563EB;  /* Hover state */
--primary-700: #1D4ED8;  /* Active state */
--primary-800: #1E40AF;
--primary-900: #1E3A8A;
```

---

## 1.4 Color Usage Rules

### Contrast Ratios (WCAG 2.1 AA)
**Source**: https://www.w3.org/WAI/WCAG21/Understanding/contrast-minimum.html

```
Normal Text (< 18px):     4.5:1 minimum
Large Text (≥ 18px):      3:1 minimum
UI Components:            3:1 minimum
Logos/Decorative:         No requirement
```

### Best Practices

✅ **DO:**
- Use primary color sparingly (CTAs, links, important UI)
- Keep backgrounds neutral (white, gray-50, gray-100)
- Use semantic colors for their purpose only
- Test all color combinations for accessibility
- Provide dark mode alternatives

❌ **DON'T:**
- Use more than 3 brand colors in one view
- Put light text on light backgrounds
- Use pure black (#000000) - use gray-900 instead
- Rely on color alone to convey meaning

---

## 1.5 Recommended Color Palettes

### Option 1: Professional/Corporate
```css
Primary:    #2563EB (Blue 600)
Secondary:  #64748B (Slate 500)
Accent:     #F59E0B (Amber 500)
Background: #FFFFFF
Text:       #1E293B (Slate 800)
```

### Option 2: Modern/Tech
```css
Primary:    #8B5CF6 (Violet 500)
Secondary:  #06B6D4 (Cyan 500)
Accent:     #F43F5E (Rose 500)
Background: #FAFAFA (Gray 50)
Text:       #0F172A (Slate 900)
```

### Option 3: Clean/Minimalist
```css
Primary:    #000000 (Black)
Secondary:  #6B7280 (Gray 500)
Accent:     #10B981 (Emerald 500)
Background: #FFFFFF
Text:       #111827 (Gray 900)
```

---

# 2. Spacing & Layout

## 2.1 The 8px Grid System

### Why 8px? (Industry Standard)
**Sources**: Material Design, iOS Human Interface Guidelines, Bootstrap

- **Perfect scaling**: 8px scales evenly across all devices
- **Developer-friendly**: 1rem = 16px = 2 × 8px
- **Recommended by**: Apple, Google, Microsoft
- **Math-friendly**: Divisible by 2, 4, 8 for easy calculations

---

## 2.2 Spacing Scale

### Base Scale (Use These Values)
```css
--space-0:   0px;    /* None */
--space-1:   4px;    /* Tiny - icon padding */
--space-2:   8px;    /* XS - tight spacing */
--space-3:   12px;   /* SM - compact elements */
--space-4:   16px;   /* Base - default spacing */
--space-5:   20px;   /* MD - comfortable spacing */
--space-6:   24px;   /* LG - section padding */
--space-8:   32px;   /* XL - large gaps */
--space-10:  40px;   /* 2XL - major sections */
--space-12:  48px;   /* 3XL - page sections */
--space-16:  64px;   /* 4XL - hero sections */
--space-20:  80px;   /* 5XL - major dividers */
--space-24:  96px;   /* 6XL - huge sections */
```

### Tailwind CSS Equivalent
```
p-1  = 4px
p-2  = 8px
p-3  = 12px
p-4  = 16px
p-5  = 20px
p-6  = 24px
p-8  = 32px
p-10 = 40px
p-12 = 48px
p-16 = 64px
```

---

## 2.3 Spacing Usage Guidelines

### Internal ≤ External Rule
**Source**: https://cieden.com/book/sub-atomic/spacing/spacing-best-practices

> Internal spacing should NEVER be greater than external spacing.

```
Example Card:
┌─────────────────────────────────────┐
│ Padding: 16px (internal)            │
│                                     │
│  ┌─────────────────────────────┐   │
│  │ Content                     │   │
│  │ Gap between items: 8px      │   │
│  │ (internal)                  │   │
│  └─────────────────────────────┘   │
│                                     │
└─────────────────────────────────────┘
Margin bottom: 24px (external)

✅ 8px (internal gap) < 16px (padding) < 24px (margin)
```

### Proximity Principle
- **Related items**: 8px - 12px gap
- **Different sections**: 24px - 32px gap
- **Major sections**: 48px - 64px gap
- **Page sections**: 64px - 96px gap

---

## 2.4 Layout Grid

### Desktop Grid (1440px+)
```
Columns:  12
Gutter:   24px
Margin:   64px
Max-width: 1280px
```

### Tablet Grid (768px - 1439px)
```
Columns:  8
Gutter:   16px
Margin:   32px
```

### Mobile Grid (< 768px)
```
Columns:  4
Gutter:   16px
Margin:   16px
```

### Container Widths
```css
--container-sm:  640px;   /* Small devices */
--container-md:  768px;   /* Tablets */
--container-lg:  1024px;  /* Laptops */
--container-xl:  1280px;  /* Desktops */
--container-2xl: 1536px;  /* Large screens */
```

---

## 2.5 Common Layout Patterns

### Content Sections
```css
.section {
  padding-top: 64px;      /* Large breathing room */
  padding-bottom: 64px;
}

.container {
  max-width: 1280px;
  padding-left: 16px;     /* Mobile-friendly */
  padding-right: 16px;
  margin: 0 auto;
}
```

### Cards/Components
```css
.card {
  padding: 24px;          /* Generous internal space */
  margin-bottom: 24px;    /* Clear separation */
  border-radius: 8px;     /* Subtle rounding */
  background: white;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
```

### Grid Layouts
```css
.grid {
  display: grid;
  gap: 24px;              /* Consistent spacing */
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
}
```

---

# 3. Typography

## 3.1 Type Scale

### Modular Scale Ratios
**Source**: https://www.modularscale.com/

Common ratios:
- **1.125** (Major Second) - Subtle, dense layouts
- **1.200** (Minor Third) - Default, balanced
- **1.250** (Major Third) - Clear hierarchy
- **1.333** (Perfect Fourth) - Distinct levels
- **1.414** (Augmented Fourth) - Dramatic
- **1.618** (Golden Ratio) - High-end designs

### Recommended Type Scale (1.250 - Major Third)
```css
--text-xs:   0.64rem;   /* 10.24px */
--text-sm:   0.80rem;   /* 12.8px */
--text-base: 1.00rem;   /* 16px - Base size */
--text-lg:   1.25rem;   /* 20px */
--text-xl:   1.563rem;  /* 25px */
--text-2xl:  1.953rem;  /* 31.25px */
--text-3xl:  2.441rem;  /* 39px */
--text-4xl:  3.052rem;  /* 48.83px */
--text-5xl:  3.815rem;  /* 61px */
```

---

## 3.2 Font Selection

### System Font Stack (Fastest, Free)
```css
font-family: -apple-system, BlinkMacSystemFont, "Segoe UI",
             Roboto, "Helvetica Neue", Arial, sans-serif;
```

### Modern Font Pairings

**Option 1: Google Fonts - Professional**
```css
Headings: 'Inter', sans-serif;
Body:     'Inter', sans-serif;
/* Single font, multiple weights */
```

**Option 2: Google Fonts - Editorial**
```css
Headings: 'Playfair Display', serif;
Body:     'Source Sans Pro', sans-serif;
/* Classic contrast */
```

**Option 3: Google Fonts - Modern**
```css
Headings: 'Poppins', sans-serif;
Body:     'Open Sans', sans-serif;
/* Clean and friendly */
```

**Option 4: Google Fonts - Tech/Startup**
```css
Headings: 'Space Grotesk', sans-serif;
Body:     'Inter', sans-serif;
/* Contemporary, geometric */
```

---

## 3.3 Typography Settings

### Base Sizes (2024/2025)
**Source**: https://designshack.net/articles/typography/guide-to-responsive-typography-sizing-and-scales/

```css
/* Desktop */
body {
  font-size: 16px;        /* Industry standard */
  line-height: 1.6;       /* 25.6px - readable */
}

/* Content-heavy sites (blogs, news) */
body {
  font-size: 18px;        /* Better readability */
  line-height: 1.7;       /* 30.6px */
}

/* Mobile */
@media (max-width: 768px) {
  body {
    font-size: 14px;      /* Smaller screens */
    line-height: 1.5;
  }
}
```

### Line Height Rules
```css
Headings:   1.2 - 1.3  /* Tighter */
Body:       1.5 - 1.7  /* Comfortable */
Small text: 1.4 - 1.5  /* Slightly tighter */
```

### Letter Spacing (Tracking)
```css
Headings:   -0.02em to -0.03em  /* Slightly tighter */
Body:       0 (normal)
Uppercase:  0.05em to 0.1em     /* Looser for readability */
Small:      0.01em              /* Slightly open */
```

### Font Weights
```css
--font-light:   300;  /* Subtle text, large sizes */
--font-normal:  400;  /* Body text */
--font-medium:  500;  /* Emphasis, labels */
--font-semibold: 600; /* Subheadings */
--font-bold:    700;  /* Headings */
--font-black:   900;  /* Display, hero text */
```

---

## 3.4 Practical Typography Examples

### Headings
```css
h1 {
  font-size: 3.052rem;    /* ~49px */
  font-weight: 700;
  line-height: 1.2;
  letter-spacing: -0.02em;
  margin-bottom: 24px;
}

h2 {
  font-size: 2.441rem;    /* ~39px */
  font-weight: 600;
  line-height: 1.2;
  margin-bottom: 16px;
}

h3 {
  font-size: 1.953rem;    /* ~31px */
  font-weight: 600;
  line-height: 1.3;
  margin-bottom: 12px;
}

h4 {
  font-size: 1.563rem;    /* ~25px */
  font-weight: 500;
  line-height: 1.3;
}
```

### Body Text
```css
p {
  font-size: 1rem;        /* 16px */
  line-height: 1.6;
  margin-bottom: 16px;
  color: var(--gray-700);
}

.lead {
  font-size: 1.25rem;     /* 20px - intro text */
  line-height: 1.7;
  color: var(--gray-600);
}

small {
  font-size: 0.875rem;    /* 14px */
  line-height: 1.5;
  color: var(--gray-500);
}
```

---

## 3.5 Responsive Typography

### Using CSS Clamp() (Modern Approach)
```css
h1 {
  font-size: clamp(2rem, 5vw, 3.052rem);
  /* Min: 32px, Scales with viewport, Max: ~49px */
}

h2 {
  font-size: clamp(1.5rem, 4vw, 2.441rem);
}

p {
  font-size: clamp(0.875rem, 2vw, 1rem);
}
```

### Media Query Approach
```css
h1 {
  font-size: 2rem;        /* Mobile */
}

@media (min-width: 768px) {
  h1 {
    font-size: 2.5rem;    /* Tablet */
  }
}

@media (min-width: 1024px) {
  h1 {
    font-size: 3.052rem;  /* Desktop */
  }
}
```

---

# 4. Components & Patterns

## 4.1 Buttons

### Button Sizes
```css
.btn-sm {
  padding: 8px 16px;      /* 2 × 8px */
  font-size: 0.875rem;
  border-radius: 6px;
}

.btn-md {
  padding: 12px 24px;     /* Default */
  font-size: 1rem;
  border-radius: 8px;
}

.btn-lg {
  padding: 16px 32px;
  font-size: 1.125rem;
  border-radius: 8px;
}
```

### Button Variants
```css
/* Primary - Main CTA */
.btn-primary {
  background: var(--primary-600);
  color: white;
  border: none;
}
.btn-primary:hover {
  background: var(--primary-700);
}

/* Secondary - Less emphasis */
.btn-secondary {
  background: transparent;
  color: var(--primary-600);
  border: 1px solid var(--primary-600);
}

/* Ghost - Minimal */
.btn-ghost {
  background: transparent;
  color: var(--gray-700);
  border: none;
}
.btn-ghost:hover {
  background: var(--gray-100);
}
```

---

## 4.2 Forms

### Input Fields
```css
input, textarea {
  width: 100%;
  padding: 12px 16px;     /* Comfortable tap target */
  font-size: 1rem;
  line-height: 1.5;
  color: var(--gray-900);
  background: white;
  border: 1px solid var(--gray-300);
  border-radius: 8px;
  transition: all 0.2s;
}

input:focus {
  outline: none;
  border-color: var(--primary-500);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

input:disabled {
  background: var(--gray-100);
  color: var(--gray-500);
  cursor: not-allowed;
}
```

### Labels
```css
label {
  display: block;
  margin-bottom: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--gray-700);
}
```

### Error States
```css
.input-error {
  border-color: var(--error);
}

.error-message {
  margin-top: 4px;
  font-size: 0.875rem;
  color: var(--error);
}
```

---

## 4.3 Cards

### Standard Card
```css
.card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1),
              0 1px 2px rgba(0, 0, 0, 0.06);
  transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1),
              0 4px 10px rgba(0, 0, 0, 0.06);
}
```

---

## 4.4 Navigation

### Header
```css
header {
  height: 64px;           /* Standard header height */
  padding: 0 24px;
  background: white;
  border-bottom: 1px solid var(--gray-200);
  position: sticky;
  top: 0;
  z-index: 100;
}
```

### Navigation Links
```css
.nav-link {
  padding: 8px 16px;
  font-size: 1rem;
  font-weight: 500;
  color: var(--gray-700);
  text-decoration: none;
  border-radius: 6px;
  transition: all 0.2s;
}

.nav-link:hover {
  color: var(--primary-600);
  background: var(--gray-100);
}

.nav-link.active {
  color: var(--primary-600);
  background: var(--primary-50);
}
```

---

## 4.5 Shadows (Elevation)

### Shadow Scale
```css
--shadow-sm:  0 1px 2px rgba(0, 0, 0, 0.05);
--shadow-md:  0 4px 6px rgba(0, 0, 0, 0.07),
              0 2px 4px rgba(0, 0, 0, 0.05);
--shadow-lg:  0 10px 15px rgba(0, 0, 0, 0.1),
              0 4px 6px rgba(0, 0, 0, 0.05);
--shadow-xl:  0 20px 25px rgba(0, 0, 0, 0.1),
              0 10px 10px rgba(0, 0, 0, 0.04);
--shadow-2xl: 0 25px 50px rgba(0, 0, 0, 0.15);
```

### Usage
- **sm**: Subtle elements, inputs
- **md**: Cards, dropdowns
- **lg**: Modals, popovers
- **xl**: Overlays, important modals
- **2xl**: Full-screen overlays

---

# 5. Responsive Design

## 5.1 Breakpoints

### Standard Breakpoints
```css
/* Mobile first approach */
--breakpoint-sm:  640px;   /* Small tablets */
--breakpoint-md:  768px;   /* Tablets */
--breakpoint-lg:  1024px;  /* Small laptops */
--breakpoint-xl:  1280px;  /* Desktops */
--breakpoint-2xl: 1536px;  /* Large screens */
```

### Usage
```css
/* Mobile (default) */
.container {
  padding: 16px;
}

/* Tablet and up */
@media (min-width: 768px) {
  .container {
    padding: 32px;
  }
}

/* Desktop and up */
@media (min-width: 1024px) {
  .container {
    padding: 64px;
  }
}
```

---

## 5.2 Mobile-First Approach

**Source**: Every major design system (Material, Bootstrap, Tailwind)

```css
/* ✅ CORRECT - Mobile First */
.element {
  font-size: 14px;        /* Mobile */
}

@media (min-width: 768px) {
  .element {
    font-size: 16px;      /* Tablet+ */
  }
}

/* ❌ WRONG - Desktop First */
.element {
  font-size: 16px;
}

@media (max-width: 767px) {
  .element {
    font-size: 14px;
  }
}
```

---

## 5.3 Touch Targets

### Minimum Sizes
**Source**: Apple HIG, Material Design

```
Minimum touch target: 44px × 44px (iOS)
Recommended:         48px × 48px (Material)
```

```css
.touch-target {
  min-height: 48px;
  min-width: 48px;
  padding: 12px 16px;
}
```

---

# 6. Accessibility

## 6.1 WCAG 2.1 AA Compliance

### Color Contrast
```
Normal text (< 18px):     4.5:1 minimum
Large text (≥ 18px/14px bold): 3:1 minimum
UI components:            3:1 minimum
```

### Tools to Check
- https://webaim.org/resources/contrastchecker/
- Chrome DevTools Lighthouse
- WAVE Browser Extension

---

## 6.2 Focus States

```css
/* Never remove focus outline */
*:focus {
  outline: 2px solid var(--primary-500);
  outline-offset: 2px;
}

/* Better custom focus */
button:focus-visible {
  outline: 2px solid var(--primary-500);
  outline-offset: 2px;
  border-radius: 4px;
}
```

---

## 6.3 Semantic HTML

```html
✅ CORRECT
<nav>
  <ul>
    <li><a href="/">Home</a></li>
  </ul>
</nav>

<main>
  <article>
    <h1>Title</h1>
    <p>Content</p>
  </article>
</main>

<footer>...</footer>

❌ WRONG
<div class="nav">
  <div class="link">Home</div>
</div>
```

---

# 7. Modern Design Trends (2024/2025)

## 7.1 Minimalism

**Key Principles:**
- Clean layouts with ample white space
- Limited color palette (2-3 colors max)
- Focus on essential content only
- Intuitive navigation
- Reduced cognitive load

```css
/* Example minimal section */
.minimal-section {
  padding: 96px 24px;     /* Generous spacing */
  background: white;
}

.minimal-section h2 {
  font-size: 3rem;
  font-weight: 300;       /* Light weight */
  letter-spacing: -0.02em;
  margin-bottom: 48px;
}
```

---

## 7.2 Dark Mode

### Implementation
```css
:root {
  --bg: #FFFFFF;
  --text: #111827;
  --surface: #F9FAFB;
}

@media (prefers-color-scheme: dark) {
  :root {
    --bg: #111827;
    --text: #F9FAFB;
    --surface: #1F2937;
  }
}

body {
  background: var(--bg);
  color: var(--text);
}
```

### Dark Mode Best Practices
- Use gray-900 (#111827) instead of pure black
- Reduce shadow opacity (shadows work differently in dark mode)
- Increase contrast for colored elements
- Test all states (hover, active, disabled)

---

## 7.3 Micro-interactions

### Smooth Transitions
```css
/* Global transition */
* {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Button hover */
.button {
  transform: translateY(0);
  box-shadow: var(--shadow-md);
}

.button:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

/* Loading states */
.loading {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
```

---

## 7.4 Glassmorphism (Trending)

```css
.glass {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

/* Dark mode version */
@media (prefers-color-scheme: dark) {
  .glass {
    background: rgba(17, 24, 39, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
}
```

---

## 7.5 Subtle Animations

```css
/* Fade in on scroll */
.fade-in {
  opacity: 0;
  transform: translateY(20px);
  animation: fadeInUp 0.6s ease forwards;
}

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Hover grow */
.hover-grow {
  transition: transform 0.2s;
}

.hover-grow:hover {
  transform: scale(1.05);
}
```

---

# 8. Quick Reference

## 8.1 Design System Checklist

### Colors
- [ ] Primary color (with 5-9 shades)
- [ ] Secondary color (optional)
- [ ] Gray scale (9 shades: 50-900)
- [ ] Semantic colors (success, error, warning, info)
- [ ] All combinations pass WCAG AA (4.5:1)
- [ ] Dark mode alternatives

### Spacing
- [ ] 8px base unit
- [ ] Spacing scale defined (4px to 96px)
- [ ] Internal ≤ External rule followed
- [ ] Consistent padding/margins across components

### Typography
- [ ] Type scale defined (1.2 - 1.333 ratio)
- [ ] Base font size: 16px desktop, 14px mobile
- [ ] Font family chosen
- [ ] Line heights set (1.2-1.3 headings, 1.5-1.7 body)
- [ ] Font weights defined (400, 500, 600, 700)

### Components
- [ ] Buttons (primary, secondary, ghost)
- [ ] Forms (inputs, labels, error states)
- [ ] Cards
- [ ] Navigation
- [ ] All have hover/active/disabled states

### Responsive
- [ ] Mobile-first approach
- [ ] Breakpoints defined
- [ ] Touch targets ≥ 48px
- [ ] Tested on mobile, tablet, desktop

### Accessibility
- [ ] All text passes contrast
- [ ] Focus states visible
- [ ] Semantic HTML used
- [ ] ARIA labels where needed
- [ ] Keyboard navigation works

---

## 8.2 Common Values Quick Copy

### Spacing
```
4px  8px  12px  16px  20px  24px  32px  40px  48px  64px  96px
```

### Border Radius
```
4px (subtle)  6px (default)  8px (cards)  12px (large)  9999px (pill)
```

### Font Sizes
```
12px  14px  16px  18px  20px  24px  32px  40px  48px
```

### Font Weights
```
300 (light)  400 (normal)  500 (medium)  600 (semibold)  700 (bold)
```

### Shadows
```
sm:  0 1px 2px rgba(0,0,0,0.05)
md:  0 4px 6px rgba(0,0,0,0.07)
lg:  0 10px 15px rgba(0,0,0,0.1)
xl:  0 20px 25px rgba(0,0,0,0.1)
```

---

## 8.3 CSS Variables Template

```css
:root {
  /* Colors */
  --primary: #3B82F6;
  --secondary: #64748B;
  --success: #10B981;
  --error: #EF4444;
  --warning: #F59E0B;

  /* Grays */
  --gray-50: #F9FAFB;
  --gray-100: #F3F4F6;
  --gray-200: #E5E7EB;
  --gray-300: #D1D5DB;
  --gray-400: #9CA3AF;
  --gray-500: #6B7280;
  --gray-600: #4B5563;
  --gray-700: #374151;
  --gray-800: #1F2937;
  --gray-900: #111827;

  /* Spacing */
  --space-1: 4px;
  --space-2: 8px;
  --space-3: 12px;
  --space-4: 16px;
  --space-6: 24px;
  --space-8: 32px;
  --space-12: 48px;
  --space-16: 64px;

  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  --text-xs: 0.75rem;
  --text-sm: 0.875rem;
  --text-base: 1rem;
  --text-lg: 1.125rem;
  --text-xl: 1.25rem;
  --text-2xl: 1.5rem;
  --text-3xl: 1.875rem;
  --text-4xl: 2.25rem;

  /* Shadows */
  --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
  --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);

  /* Border radius */
  --radius-sm: 4px;
  --radius-md: 6px;
  --radius-lg: 8px;
  --radius-xl: 12px;
  --radius-full: 9999px;

  /* Transitions */
  --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
```

---

## 8.4 Resources & Tools

### Design Tools
- **Figma**: https://figma.com (Design system creation)
- **Coolors**: https://coolors.co (Color palette generator)
- **Type Scale**: https://typescale.com (Typography scale calculator)
- **Contrast Checker**: https://webaim.org/resources/contrastchecker/

### Icon Libraries
- **Heroicons**: https://heroicons.com (Free, by Tailwind)
- **Lucide**: https://lucide.dev (Modern, open source)
- **Feather**: https://feathericons.com (Minimal)

### Font Libraries
- **Google Fonts**: https://fonts.google.com
- **Font Pair**: https://fontpair.co (Font pairing suggestions)

### CSS Frameworks (Pre-built Design Systems)
- **Tailwind CSS**: https://tailwindcss.com
- **Material UI**: https://mui.com
- **Shadcn/ui**: https://ui.shadcn.com (Copy-paste components)

### Learning Resources
- **Material Design 3**: https://m3.material.io
- **Apple HIG**: https://developer.apple.com/design/human-interface-guidelines/
- **Refactoring UI**: https://refactoringui.com (Paid book, worth it)
- **Laws of UX**: https://lawsofux.com

---

## 8.5 AI Coding Prompts

### For Component Creation
```
Create a [component] using:
- 8px spacing scale
- Primary color: #3B82F6
- Gray-700 text (#374151)
- Border radius: 8px
- Hover state with shadow-md
- Mobile-first responsive
- WCAG AA compliant
```

### For Layout
```
Create a responsive layout using:
- 12-column grid on desktop
- 4-column grid on mobile
- 24px gutters
- 64px section padding
- Max-width: 1280px
- Mobile-first breakpoints
```

### For Typography
```
Set up typography with:
- Base size: 16px
- Scale ratio: 1.250 (Major Third)
- Line height: 1.6 for body, 1.2 for headings
- Font: Inter or system font stack
- Responsive (14px mobile, 16px desktop)
```

---

## Conclusion

This guide provides a solid foundation for creating modern, clean, and consistent websites. Remember:

1. **Consistency** is key - use your defined scale everywhere
2. **Less is more** - minimalism is trending for a reason
3. **Accessibility** is non-negotiable - always test contrast and keyboard navigation
4. **Mobile-first** - most users are on mobile devices
5. **Test everything** - across devices, browsers, and user scenarios

**Keep this guide handy** and reference it when building any website component. These are industry-standard best practices from Google, Apple, and the design community.

---

**Version**: 1.0
**Last Updated**: October 2025
**Maintained for**: AI Coders & Web Developers
