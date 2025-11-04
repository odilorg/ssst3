<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Tour;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    /**
     * Store a new review
     */
    public function store(Request $request, string $slug)
    {
        // Rate limiting: 2 reviews per 10 minutes per IP
        $key = 'review-submit:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 2)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            throw ValidationException::withMessages([
                'rate_limit' => "Too many review submissions. Please wait {$minutes} minute(s) before submitting again."
            ]);
        }

        // Daily rate limiting: 5 reviews per day per IP
        $dailyKey = 'review-submit-daily:' . $request->ip();

        if (RateLimiter::tooManyAttempts($dailyKey, 5)) {
            throw ValidationException::withMessages([
                'rate_limit' => "You've reached the daily review submission limit. Please try again tomorrow."
            ]);
        }

        // Validate input
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|min:5|max:150',
            'content' => 'required|string|min:20|max:2000',
            'reviewer_name' => 'required|string|max:100',
            'reviewer_email' => 'required|email|max:150',
            'reviewer_location' => 'nullable|string|max:100',
            'booking_reference' => 'nullable|string|max:50',
            'honeypot' => 'nullable|size:0', // Honeypot trap for bots
        ]);

        // Check honeypot (if filled, it's a bot)
        if (!empty($request->input('honeypot'))) {
            return response()->json(['message' => 'Review submitted successfully'], 200);
        }

        // Get tour
        $tour = Tour::where('slug', $slug)->firstOrFail();

        // Check for duplicate review from same email
        $existingReview = Review::where('tour_id', $tour->id)
            ->where('reviewer_email', $validated['reviewer_email'])
            ->where('created_at', '>', now()->subDays(30))
            ->first();

        if ($existingReview) {
            throw ValidationException::withMessages([
                'reviewer_email' => 'You have already submitted a review for this tour recently.'
            ]);
        }

        // Check booking verification
        $bookingId = null;
        $isVerified = false;

        if (!empty($validated['booking_reference'])) {
            $booking = Booking::where('reference', $validated['booking_reference'])
                ->where('tour_id', $tour->id)
                ->whereHas('customer', function ($query) use ($validated) {
                    $query->where('email', $validated['reviewer_email']);
                })
                ->whereIn('status', ['completed', 'confirmed'])
                ->first();

            if ($booking) {
                $bookingId = $booking->id;
                $isVerified = true;
            }
        }

        // Calculate spam score using Review model method
        $spamScore = Review::calculateSpamScore(
            $validated['title'],
            $validated['content'],
            $validated['reviewer_email'],
            null // No website field for reviews
        );

        // Auto-approve logic
        $isApproved = false;

        if ($isVerified) {
            // Always approve verified bookings (low spam score)
            if ($spamScore < 50) {
                $isApproved = true;
            }
        } elseif ($spamScore < 30) {
            // Check if this reviewer has previous approved reviews
            $previousApproved = Review::where('reviewer_email', $validated['reviewer_email'])
                ->where('is_approved', true)
                ->exists();

            if ($previousApproved) {
                $isApproved = true;
            }
        }

        // Create review
        $review = Review::create([
            'tour_id' => $tour->id,
            'booking_id' => $bookingId,
            'reviewer_name' => $validated['reviewer_name'],
            'reviewer_email' => $validated['reviewer_email'],
            'reviewer_location' => $validated['reviewer_location'] ?? null,
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'source' => 'website',
            'is_verified' => $isVerified,
            'is_approved' => $isApproved,
            'spam_score' => $spamScore,
            'review_ip' => $request->ip(),
            'review_user_agent' => $request->userAgent(),
        ]);

        // Clear review cache for this tour (all pages)
        for ($page = 1; $page <= 10; $page++) {
            Cache::forget("tour.{$slug}.reviews.page.{$page}");
        }

        // Increment rate limiters
        RateLimiter::hit($key, 600); // 10 minutes
        RateLimiter::hit($dailyKey, 86400); // 24 hours

        // Return appropriate response
        if ($isApproved) {
            return response()->json([
                'message' => $isVerified
                    ? 'Thank you for your verified review! It has been published.'
                    : 'Thank you for your review! It has been published.',
                'status' => 'approved',
                'review_id' => $review->id,
                'is_verified' => $isVerified,
            ], 201);
        } else {
            return response()->json([
                'message' => 'Thank you for your review! It is awaiting moderation and will be published soon.',
                'status' => 'pending',
            ], 201);
        }
    }

    /**
     * Flag a review as inappropriate
     */
    public function flag(Request $request, Review $review)
    {
        // Rate limiting: 5 flags per hour per IP
        $key = 'review-flag:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many flag attempts. Please try again later.'
            ], 429);
        }

        // Check if review is already flagged by this IP recently
        $flagKey = "review-flag:{$review->id}:{$request->ip()}";

        if (Cache::has($flagKey)) {
            return response()->json([
                'message' => 'You have already flagged this review.'
            ], 400);
        }

        // Mark as flagged (store in cache for 30 days)
        Cache::put($flagKey, true, now()->addDays(30));

        // If review gets multiple unique flags, mark as spam
        $flagCount = Cache::get("review-flags-count:{$review->id}", 0) + 1;
        Cache::put("review-flags-count:{$review->id}", $flagCount, now()->addDays(30));

        if ($flagCount >= 3) {
            $review->markAsSpam();
        }

        // Increment rate limiter
        RateLimiter::hit($key, 3600); // 1 hour

        return response()->json([
            'message' => 'Review has been flagged for moderation. Thank you for your feedback.',
        ], 200);
    }
}
