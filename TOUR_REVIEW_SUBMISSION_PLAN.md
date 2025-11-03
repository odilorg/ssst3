# Tour Review Submission System - Implementation Plan

## Overview
Add a review submission form to tour detail pages, allowing customers to leave ratings and reviews. System includes spam detection, moderation workflow, and optional booking verification.

## Current State Analysis
- ✅ Review model exists (`app/Models/Review.php`)
- ✅ Reviews Filament resource exists (`admin/reviews`)
- ✅ Review display partial exists (`resources/views/partials/tours/show/reviews.blade.php`)
- ✅ 15 reviews in database (all approved)
- ❌ No review submission form
- ❌ No review submission controller
- ❌ No spam detection for reviews
- ❌ No cache clearing on approval

---

## Phase 1: Database & Model Updates (30 minutes)

### 1.1 Review Migration Updates
Check if `reviews` table has all needed fields:
```sql
- tour_id (exists)
- booking_id (exists - for verification)
- reviewer_name (exists)
- reviewer_email (exists)
- reviewer_location (exists)
- rating (exists - 1-5 stars)
- title (exists - review headline)
- content (exists - review body)
- avatar_url (exists)
- source (exists - 'manual', 'booking', 'import')
- is_verified (exists)
- is_approved (exists)
- spam_score (NEEDS ADDING - 0-100)
- review_ip (NEEDS ADDING)
- review_user_agent (NEEDS ADDING)
- created_at, updated_at (exists)
```

**Action**: Create migration to add missing fields if needed

### 1.2 Model Enhancements
Update `app/Models/Review.php`:
- ✅ Add `spam_score`, `review_ip`, `review_user_agent` to fillable
- ✅ Add scopes: `scopePending()`, `scopeSpam()`
- ✅ Add methods: `approve()`, `markAsSpam()`, `calculateSpamScore()`
- ✅ Add `clearReviewCache()` method
- ✅ Update boot method to clear cache on save/delete

---

## Phase 2: Backend - Review Submission Controller (45 minutes)

### 2.1 Create ReviewController
Location: `app/Http/Controllers/ReviewController.php`

**Endpoints**:
1. `POST /tours/{slug}/reviews` - Submit review
2. `POST /reviews/{review}/helpful` - Mark review helpful (future)

### 2.2 Validation Rules
```php
'booking_reference' => 'nullable|string|exists:bookings,booking_reference',
'reviewer_name' => 'required|string|max:100',
'reviewer_email' => 'required|email|max:150',
'reviewer_location' => 'nullable|string|max:100',
'rating' => 'required|integer|min:1|max:5',
'title' => 'required|string|min:5|max:150',
'content' => 'required|string|min:20|max:2000',
'honeypot' => 'nullable|size:0',
```

### 2.3 Spam Detection Algorithm
Similar to blog comments, check for:
- Multiple links (5+ = suspicious)
- Spam keywords ('viagra', 'casino', etc.)
- ALL CAPS text
- Repeated characters
- Email/phone patterns
- Suspicious domains

**Scoring**:
- 0-29: Auto-approve (if no history)
- 30-69: Manual review
- 70+: Auto-spam

### 2.4 Rate Limiting
- 2 reviews per IP per 10 minutes
- 5 reviews per email per day

### 2.5 Booking Verification (Optional)
If `booking_reference` provided:
- Verify booking exists
- Check tour matches
- Check booking status (completed)
- Mark review as `is_verified = true`
- Auto-approve if verified booking

---

## Phase 3: Frontend - Review Form UI (1 hour)

### 3.1 Create Review Form Component
Location: `resources/views/partials/tours/reviews/review-form.blade.php`

**Form Fields**:
1. **Rating** (required) - 5-star selector
2. **Review Title** (required) - Headline
3. **Review Text** (required) - Textarea with character count
4. **Your Name** (required)
5. **Email** (required)
6. **Location** (optional) - City/Country
7. **Booking Reference** (optional) - For verification
8. **Honeypot** (hidden)

**Features**:
- Live character counter (20-2000 chars)
- Star rating visual selector
- "Verified Booking" badge if reference provided
- Loading state on submit
- Success/error messages
- Form reset after success

### 3.2 Update Reviews Display Partial
Update: `resources/views/partials/tours/show/reviews.blade.php`
- Add review form at the top
- Keep existing review list below
- Add "Write a Review" CTA button
- Add review count header

### 3.3 JavaScript Handler
Create: `public/js/tour-reviews.js`

**Functions**:
- `handleReviewSubmit()` - AJAX form submission
- `validateReviewForm()` - Client-side validation
- `updateStarRating()` - Interactive star selector
- `updateCharacterCount()` - Live counter
- `reloadReviews()` - HTMX refresh after submit
- `showSuccessMessage()` - Success feedback

---

## Phase 4: Admin Panel Updates (30 minutes)

### 4.1 Update Review Filament Resource
Location: `app/Filament/Resources/Reviews/`

**Table Updates**:
- Add spam_score column with badge
- Add source column (manual/booking/import)
- Add IP address column (hidden by default)
- Add filters: status, rating, verified
- Add quick approve/spam actions

**Form Updates**:
- Make spam_score readonly
- Show IP and user agent (readonly)
- Add verification status indicator

**Bulk Actions**:
- Bulk approve
- Bulk mark as spam
- Bulk delete

### 4.2 Navigation Badge
Show pending review count in sidebar

---

## Phase 5: Routes & Integration (15 minutes)

### 5.1 Add Routes
`routes/web.php`:
```php
// Review submission
Route::post('/tours/{slug}/reviews', [ReviewController::class, 'store'])
    ->name('tours.reviews.store');

// Helpful/unhelpful (future)
Route::post('/reviews/{review}/helpful', [ReviewController::class, 'markHelpful'])
    ->name('reviews.helpful');
```

### 5.2 Update Tour Controller Partial
`app/Http/Controllers/Partials/TourController.php`:
- Clear review cache in `reviews()` method
- Reduce cache time for reviews (10 min → 5 min)

---

## Phase 6: Styling & UX (30 minutes)

### 6.1 CSS Updates
Create: `public/css/tour-reviews.css` or add to existing

**Components to style**:
- `.review-form` - Form container
- `.star-rating-input` - Interactive stars
- `.review-form-field` - Input styling
- `.review-submit-button` - CTA button
- `.review-success-message` - Success state
- `.review-error-message` - Error state
- `.verified-badge` - Booking verified indicator

### 6.2 Responsive Design
- Mobile-friendly form layout
- Touch-friendly star selector
- Readable text sizes
- Proper spacing

---

## Phase 7: Email Notifications (Optional) (20 minutes)

### 7.1 Admin Notification
Send email when new review is submitted:
- Notification: `ReviewSubmitted`
- Recipient: Admin email
- Content: Review details + approve link

### 7.2 Customer Confirmation
Send email to reviewer:
- Thank you message
- "Under review" notice
- Link to tour page

---

## Phase 8: Testing & Validation (30 minutes)

### 8.1 Test Cases
- ✅ Submit valid review
- ✅ Submit with booking reference
- ✅ Submit with spam content
- ✅ Submit with honeypot filled
- ✅ Test rate limiting
- ✅ Test validation errors
- ✅ Test cache clearing
- ✅ Test admin approval workflow
- ✅ Test HTMX reload

### 8.2 Edge Cases
- Empty form submission
- Duplicate reviews from same email
- XSS injection attempts
- SQL injection attempts
- Invalid booking reference
- Tour that doesn't exist

---

## Phase 9: Cache Strategy (15 minutes)

### 9.1 Cache Keys
```php
"tour.{slug}.reviews.data" - Review list
"tour.{slug}.reviews.count" - Count
"tour.{slug}.reviews.summary" - Rating stats
"tour.{id}.rating" - Average rating (Tour model)
```

### 9.2 Cache Clearing Triggers
- Review approved → Clear tour review cache
- Review created → Update pending count
- Review deleted → Clear tour review cache
- Review marked spam → Clear tour review cache

---

## Phase 10: Security Measures (20 minutes)

### 10.1 CSRF Protection
- Laravel CSRF token in form
- Validate on submission

### 10.2 Input Sanitization
- Strip HTML tags
- Escape output
- Validate email format
- Check URL schemes

### 10.3 Rate Limiting
- IP-based throttling
- Email-based throttling
- Session-based throttling

### 10.4 Spam Prevention
- Honeypot field
- Time-based submission check
- Duplicate content detection
- Blacklisted words/domains

---

## Implementation Order

### Recommended Sequence:
1. **Phase 1**: Database & Model (foundation)
2. **Phase 2**: Backend Controller (logic)
3. **Phase 3**: Frontend Form (UI)
4. **Phase 5**: Routes (connection)
5. **Phase 6**: Styling (polish)
6. **Phase 4**: Admin Updates (moderation)
7. **Phase 9**: Cache Strategy (performance)
8. **Phase 10**: Security (hardening)
9. **Phase 8**: Testing (validation)
10. **Phase 7**: Notifications (optional)

---

## Time Estimate

**Total Time**: ~4-5 hours

**Breakdown**:
- Core Implementation (Phases 1-6): 3 hours
- Admin & Polish (Phases 4, 9, 10): 1 hour
- Testing (Phase 8): 30 minutes
- Email Notifications (Phase 7 - Optional): 30 minutes

---

## Success Criteria

✅ Users can submit reviews with ratings
✅ Spam detection works automatically
✅ Admin can approve/reject reviews
✅ Cache clears on status change
✅ HTMX reloads reviews after submission
✅ Rate limiting prevents abuse
✅ Verified bookings auto-approve
✅ Mobile-friendly UI
✅ No security vulnerabilities

---

## Future Enhancements (Post-MVP)

- Review photos/images
- Helpful/unhelpful voting
- Sort reviews (recent, highest rated, etc.)
- Filter reviews by rating
- Review response from tour operator
- Email follow-up to customers for reviews
- Review reminder X days after tour
- Review statistics dashboard
- Export reviews to CSV
- Import reviews from other platforms

---

## Notes

- Keep similar to blog comment system for consistency
- Focus on trust signals (verified bookings)
- Make it easy but prevent spam
- Mobile-first design
- Fast and responsive
- Clear admin workflow
