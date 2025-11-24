# Related Tours Feature - Implementation Plan

## Executive Summary

Add a "Related Tours" section to each tour detail page that displays up to 4 tours from the same category(ies), encouraging users to explore similar offerings.

---

## Current State Analysis

### Database Structure
- **Relationship**: `tours` ↔ `tour_category_tour` (pivot) ↔ `tour_categories`
- **Type**: Many-to-Many (belongsToMany)
- **Pivot Table**: `tour_category_tour`

### Categories Available (6 total)
1. **cultural-historical** (ID: 1)
2. **mountain-adventure** (ID: 2)
3. **family-educational** (ID: 3)
4. **desert-nomadic** (ID: 4)
5. **city-walks** (ID: 5)
6. **food-craft** (ID: 6)

### Tour-Category Coverage
- **18 active tours** total
- **14 tours** have categories assigned (77.8%)
- **4 tours** without categories (IDs: 26, 28, 29, 30)

---

## Implementation Strategy

### Phase 1: Backend Logic
**File**: `app/Http/Controllers/Partials/TourController.php`

**New Method**: `relatedTours(string $slug)`

**Logic**:
1. Get current tour with categories
2. If tour has categories:
   - Find other active tours sharing ANY of these categories
   - Exclude current tour
   - Prioritize tours with most category matches
   - Limit to 4 tours
   - Eager load: city, hero_image
3. If no related tours found:
   - Fallback to newest/popular tours
4. Cache results (1 hour TTL)

**Query Example**:
```php
$relatedTours = Tour::whereHas('categories', function($query) use ($categoryIds) {
    $query->whereIn('tour_categories.id', $categoryIds);
})
->where('id', '!=', $tour->id)
->where('is_active', true)
->with(['city:id,name,slug', 'categories:id,slug'])
->withCount(['categories' => function($query) use ($categoryIds) {
    $query->whereIn('tour_categories.id', $categoryIds);
}])
->orderByDesc('categories_count') // Most category matches first
->orderBy('created_at', 'desc')
->limit(4)
->get();
```

---

### Phase 2: Frontend Display
**File**: `resources/views/partials/tours/show/related-tours.blade.php`

**Design**:
- **Section Title**: "You Might Also Like" or "Related Tours"
- **Layout**: Horizontal scrollable grid (mobile) / 4-column grid (desktop)
- **Card Content**:
  - Tour hero image (optimized WebP)
  - Tour title (truncated if too long)
  - Duration badge
  - Price
  - City name
  - CTA: "View Details" button
  - Category badges (small pills)

**Responsive Behavior**:
- **Mobile**: Horizontal scroll, 1.2 cards visible
- **Tablet**: 2 columns
- **Desktop**: 4 columns

---

### Phase 3: Styling
**File**: `public/tour-details.css`

**CSS Classes**:
```css
.related-tours-section { }
.related-tours-grid { }
.related-tour-card { }
.related-tour-image { }
.related-tour-content { }
.related-tour-title { }
.related-tour-meta { }
.related-tour-price { }
.related-tour-cta { }
.related-tour-badges { }
```

**Key Features**:
- Smooth horizontal scroll on mobile
- Hover effects on cards
- Lazy load images
- Card elevation on hover

---

### Phase 4: Integration
**File**: `resources/views/tours/show.blade.php`

**Placement**: After "Reviews" section, before footer

**HTMX Integration**:
```blade
<div
    hx-get="/partials/tours/{{ $tour->slug }}/related"
    hx-trigger="revealed"
    hx-swap="outerHTML"
    class="related-tours-placeholder">
    <div class="loading-skeleton">...</div>
</div>
```

---

### Phase 5: Routing
**File**: `routes/web.php`

**New Route**:
```php
Route::get('/partials/tours/{slug}/related',
    [Partials\TourController::class, 'relatedTours']
)->name('partials.tours.related');
```

---

## Implementation Checklist

### Backend
- [ ] Add `relatedTours()` method to `TourController`
- [ ] Implement query logic with category matching
- [ ] Add fallback for tours without categories
- [ ] Implement caching (key: `tour.{slug}.related`)
- [ ] Add cache invalidation on tour/category updates

### Frontend
- [ ] Create `related-tours.blade.php` partial
- [ ] Design responsive card layout
- [ ] Add category badges display
- [ ] Implement lazy loading for images
- [ ] Add loading skeleton/placeholder

### Styling
- [ ] Add CSS for `.related-tours-*` classes
- [ ] Mobile horizontal scroll styles
- [ ] Hover effects and transitions
- [ ] Responsive breakpoints
- [ ] Dark/light theme support (if applicable)

### Integration
- [ ] Add route to `web.php`
- [ ] Update `show.blade.php` with HTMX trigger
- [ ] Test HTMX lazy loading
- [ ] Verify cache behavior

### Testing
- [ ] Test with tours having 1 category
- [ ] Test with tours having multiple categories
- [ ] Test with tours having NO categories (fallback)
- [ ] Test when <4 related tours exist
- [ ] Mobile responsiveness
- [ ] Performance (query count, cache hits)

### Deployment
- [ ] Clear all caches
- [ ] Verify WebP images work
- [ ] Check production performance
- [ ] Monitor error logs

---

## Expected Outcomes

### User Experience
- ✅ Discover more tours in same categories
- ✅ Smooth browsing experience
- ✅ Mobile-friendly horizontal scroll
- ✅ Fast loading (lazy + cached)

### Business Value
- ✅ Increased tour discovery
- ✅ Higher page views per session
- ✅ More booking opportunities
- ✅ Better content cross-linking

### Technical Quality
- ✅ N+1 query prevention (eager loading)
- ✅ Efficient caching strategy
- ✅ Responsive design
- ✅ Maintainable code structure

---

## Timeline Estimate

- **Phase 1** (Backend): 30 minutes
- **Phase 2** (Frontend): 45 minutes
- **Phase 3** (Styling): 30 minutes
- **Phase 4** (Integration): 15 minutes
- **Phase 5** (Testing): 20 minutes

**Total**: ~2.5 hours

---

## Notes

### Category Assignment
4 tours currently lack categories (IDs: 26, 28, 29, 30). Consider assigning categories to these tours for better related tour suggestions.

### Future Enhancements
- Add "View all in [Category]" link
- A/B test different section titles
- Track click-through rates
- Personalized recommendations based on user behavior
