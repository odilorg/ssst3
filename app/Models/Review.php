<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'tour_id',
        'booking_id',
        'reviewer_name',
        'reviewer_email',
        'reviewer_location',
        'rating',
        'title',
        'content',
        'avatar_url',
        'source',
        'is_verified',
        'is_approved',
        'spam_score',
        'review_ip',
        'review_user_agent',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'spam_score' => 'integer',
    ];

    /**
     * Get the tour this review belongs to
     */
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Get the booking this review is linked to (if verified)
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Scope: Only approved reviews (for public display)
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope: Only verified reviews (linked to booking)
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope: By rating
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope: Only pending reviews (awaiting moderation)
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope: High spam score reviews
     */
    public function scopeSpam($query)
    {
        return $query->where('spam_score', '>=', 70);
    }

    /**
     * Check if review is approved
     */
    public function isApproved(): bool
    {
        return $this->is_approved === true;
    }

    /**
     * Check if review is pending
     */
    public function isPending(): bool
    {
        return $this->is_approved === false;
    }

    /**
     * Check if review is likely spam (high score)
     */
    public function isSpam(): bool
    {
        return $this->spam_score >= 70;
    }

    /**
     * Approve the review
     */
    public function approve(): void
    {
        $this->update([
            'is_approved' => true,
        ]);

        // Clear review cache for this tour
        $this->clearReviewCache();
    }

    /**
     * Mark as spam
     */
    public function markAsSpam(): void
    {
        $this->update([
            'is_approved' => false,
            'spam_score' => 100,
        ]);

        // Clear review cache for this tour
        $this->clearReviewCache();
    }

    /**
     * Calculate spam score based on content analysis
     * Returns a score from 0-100 (higher = more likely spam)
     */
    public static function calculateSpamScore(string $title, string $content, string $email, ?string $website = null): int
    {
        $score = 0;

        // Check for spam keywords
        $spamKeywords = [
            'viagra', 'cialis', 'casino', 'lottery', 'poker', 'pills',
            'weight loss', 'forex', 'binary options', 'crypto', 'bitcoin',
            'click here', 'buy now', 'limited time', 'act now', 'free money',
            'work from home', 'make money fast', 'no experience needed',
        ];

        $combinedText = strtolower($title . ' ' . $content . ' ' . $website);
        foreach ($spamKeywords as $keyword) {
            if (str_contains($combinedText, $keyword)) {
                $score += 15;
            }
        }

        // Check for excessive links in content
        $linkCount = preg_match_all('/(https?:\/\/|www\.)/i', $content);
        if ($linkCount > 2) {
            $score += 20;
        } elseif ($linkCount > 0) {
            $score += 10;
        }

        // Check for excessive caps
        $capsCount = preg_match_all('/[A-Z]/', $content);
        $totalChars = strlen($content);
        if ($totalChars > 0) {
            $capsRatio = $capsCount / $totalChars;
            if ($capsRatio > 0.5) {
                $score += 20;
            } elseif ($capsRatio > 0.3) {
                $score += 10;
            }
        }

        // Check for suspicious email patterns
        if (preg_match('/[0-9]{5,}/', $email)) {
            $score += 10; // Email with long number sequences
        }

        // Check for very short content (likely not genuine review)
        if (strlen($content) < 20) {
            $score += 15;
        }

        // Check for excessive punctuation
        $punctuationCount = preg_match_all('/[!?]{2,}/', $content);
        if ($punctuationCount > 3) {
            $score += 10;
        }

        // Check for promotional website domains
        $promoTLDs = ['.xyz', '.top', '.win', '.loan', '.trade', '.click'];
        foreach ($promoTLDs as $tld) {
            if ($website && str_ends_with(strtolower($website), $tld)) {
                $score += 15;
                break;
            }
        }

        // Cap at 100
        return min($score, 100);
    }

    /**
     * Clear review cache for this tour
     * Called when review status changes to ensure frontend shows updated data
     */
    protected function clearReviewCache(): void
    {
        if ($this->tour) {
            $slug = $this->tour->slug;
            \Cache::forget("tour.{$slug}.reviews.data");
            \Cache::forget("tour.{$slug}.reviews.count");
            \Cache::forget("tour.{$slug}.reviews.avg_rating");
        }
    }

    /**
     * Boot method - Update tour rating cache when review is saved
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($review) {
            if ($review->tour) {
                $review->tour->updateRatingCache();
                $review->clearReviewCache();
            }
        });

        static::deleted(function ($review) {
            if ($review->tour) {
                $review->tour->updateRatingCache();
                $review->clearReviewCache();
            }
        });
    }
}
