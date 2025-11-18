# Header CSS - Single Source of Truth

## Location
**All header/navigation CSS is in:** `public/style.css` (lines 1388-1597)

## Structure

### HTML Structure (partials/header.blade.php)
```html
<header class="site-header" role="banner">
    <nav class="nav" aria-label="Main navigation">
        <div class="container">
            <a class="nav__logo">
                <span class="nav__logo-text">...</span>
            </a>
            <ul class="nav__menu">
                <li><a href="...">Home</a></li>
                ...
            </ul>
            <button class="nav__toggle">...</button>
        </div>
    </nav>
</header>
```

### CSS Classes

#### Container
- `.site-header` - Semantic header container (minimal styling)

#### Navigation
- `.nav` - Main navigation bar
- `.nav--sticky` - Sticky state when scrolled
- `.nav .container` - Inner container

#### Logo
- `.nav__logo` - Logo link
- `.nav__logo-text` - Logo text
- `.nav__logo-text strong` - "Travel" part in gold

#### Menu
- `.nav__menu` - Navigation list
- `.nav__menu a` - Navigation links
- `.nav__menu a.active` - Active page
- `.nav__menu a:hover` - Hover state
- `.nav__menu a::after` - Underline animation

#### Mobile Toggle
- `.nav__toggle` - Hamburger button
- `.nav__toggle-icon` - Hamburger icon
- `.nav__toggle[aria-expanded="true"]` - Open state

## Rules

### ✅ DO:
- Edit header CSS **ONLY** in `public/style.css`
- Use the existing class names (`.nav__*`)
- Test changes on all pages after editing

### ❌ DON'T:
- Add header CSS to page-specific files (contact.css, blog.css, etc.)
- Add `@push('styles')` with header overrides in Blade templates
- Use `!important` to override header styles
- Create duplicate header CSS

## Color Scheme

### Default (Transparent overlay on hero images)
- Background: `rgba(0, 0, 0, 0.25)` with backdrop blur
- Logo text: White (`#ffffff`)
- Menu links: White (`#ffffff`)
- Hover: Gold (`#ffd65c`)

### Sticky (When scrolled)
- Background: `rgba(255, 255, 255, 0.95)` with backdrop blur
- Logo text: Primary blue (`var(--color-primary)`)
- Menu links: Dark text (`var(--color-text)`)
- Hover: Gold (`#ffd65c`)

## Mobile Breakpoints
- Desktop: Above 768px
- Mobile: Below 768px (hamburger menu)

## Recent Changes (2025-01-17)
- ✅ Removed duplicate header CSS from `tour-details.css` (50 lines)
- ✅ Added `.site-header` base styles to `style.css`
- ✅ Consolidated all header CSS into single location
- ✅ Eliminated CSS conflicts between pages

## Troubleshooting

**Problem:** Header looks different on different pages
**Solution:** Check for:
1. Page-specific CSS files overriding header
2. Inline `<style>` tags in Blade templates
3. Browser cache (hard refresh: Ctrl+F5)

**Problem:** Changes to header CSS don't apply
**Solution:**
1. Ensure you're editing `public/style.css` (NOT resources/css/app.css)
2. Clear browser cache
3. Check for `!important` rules in page-specific CSS

## Files Touched
- ✅ `public/style.css` - Single source of truth
- ✅ `public/tour-details.css` - Removed duplicate header CSS
- ❌ `public/contact.css` - No header CSS (only print media query for hiding)
- ❌ `public/blog-listing.css` - No header CSS
- ❌ All Blade templates - No inline header CSS
