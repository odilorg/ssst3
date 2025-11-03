<?php

namespace App\Http\Controllers;

use App\Models\BlogComment;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    /**
     * Store a new comment
     */
    public function store(Request $request)
    {
        // Rate limiting: 3 comments per 5 minutes per IP
        $key = 'comment-submit:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'rate_limit' => "Too many comments. Please wait {$seconds} seconds before submitting again."
            ]);
        }

        // Validate input
        $validated = $request->validate([
            'blog_post_id' => 'required|exists:blog_posts,id',
            'parent_id' => 'nullable|exists:blog_comments,id',
            'author_name' => 'required|string|max:100',
            'author_email' => 'required|email|max:150',
            'author_website' => 'nullable|url|max:200',
            'comment' => 'required|string|min:3|max:2000',
            'honeypot' => 'nullable|size:0', // Honeypot trap for bots
        ]);

        // Check honeypot (if filled, it's a bot)
        if (!empty($request->input('honeypot'))) {
            return response()->json(['message' => 'Comment submitted successfully'], 200);
        }

        // Get blog post
        $post = BlogPost::findOrFail($validated['blog_post_id']);

        // Calculate spam score
        $spamScore = $this->calculateSpamScore($validated['comment'], $validated['author_website'] ?? null);

        // Auto-approve if low spam score and repeat commenter
        $status = 'pending';
        if ($spamScore < 30) {
            $previousApproved = BlogComment::where('author_email', $validated['author_email'])
                ->where('status', 'approved')
                ->exists();

            if ($previousApproved) {
                $status = 'approved';
            }
        } elseif ($spamScore >= 70) {
            $status = 'spam';
        }

        // Create comment
        $comment = BlogComment::create([
            'blog_post_id' => $validated['blog_post_id'],
            'parent_id' => $validated['parent_id'] ?? null,
            'author_name' => $validated['author_name'],
            'author_email' => $validated['author_email'],
            'author_website' => $validated['author_website'] ?? null,
            'author_ip' => $request->ip(),
            'author_user_agent' => $request->userAgent(),
            'comment' => $validated['comment'],
            'status' => $status,
            'spam_score' => $spamScore,
        ]);

        // Clear post comment cache
        Cache::forget("blog.post.{$post->slug}.comments");
        Cache::forget("blog.exists.{$post->slug}");

        // Increment rate limiter
        RateLimiter::hit($key, 300); // 5 minutes

        // Return appropriate response
        if ($status === 'approved') {
            return response()->json([
                'message' => 'Comment posted successfully!',
                'status' => 'approved',
                'comment_id' => $comment->id,
            ], 201);
        } else {
            return response()->json([
                'message' => 'Comment submitted and is awaiting moderation.',
                'status' => 'pending',
            ], 201);
        }
    }

    /**
     * Flag a comment as inappropriate
     */
    public function flag(Request $request, BlogComment $comment)
    {
        // Rate limiting: 5 flags per hour per IP
        $key = 'comment-flag:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many flag attempts. Please try again later.'
            ], 429);
        }

        // Increment flag count
        $comment->incrementFlagCount();

        // Increment rate limiter
        RateLimiter::hit($key, 3600); // 1 hour

        return response()->json([
            'message' => 'Comment has been flagged for review.',
        ], 200);
    }

    /**
     * Calculate spam score for a comment (0-100)
     */
    private function calculateSpamScore(string $comment, ?string $website): int
    {
        $score = 0;

        // Check for excessive links in comment
        $linkCount = substr_count(strtolower($comment), 'http');
        if ($linkCount > 0) {
            $score += min($linkCount * 25, 50); // Max 50 points for links
        }

        // Check for spam keywords
        $spamKeywords = [
            'viagra', 'cialis', 'casino', 'poker', 'lottery', 'bitcoin',
            'crypto', 'investment', 'earn money', 'click here', 'buy now',
            'limited offer', 'act now', 'free money', 'work from home'
        ];

        $commentLower = strtolower($comment);
        foreach ($spamKeywords as $keyword) {
            if (str_contains($commentLower, $keyword)) {
                $score += 20;
            }
        }

        // Check for excessive uppercase
        $uppercaseRatio = 0;
        $letters = preg_replace('/[^A-Za-z]/', '', $comment);
        if (strlen($letters) > 0) {
            $uppercaseCount = strlen(preg_replace('/[^A-Z]/', '', $comment));
            $uppercaseRatio = $uppercaseCount / strlen($letters);

            if ($uppercaseRatio > 0.5) {
                $score += 30;
            }
        }

        // Check website TLD if provided
        if ($website) {
            $suspiciousTlds = ['.xyz', '.info', '.biz', '.click', '.tk', '.ml'];
            foreach ($suspiciousTlds as $tld) {
                if (str_ends_with(strtolower($website), $tld)) {
                    $score += 15;
                    break;
                }
            }
        }

        // Check comment length (very short or very long can be spam)
        $length = strlen($comment);
        if ($length < 10) {
            $score += 10;
        } elseif ($length > 1500) {
            $score += 15;
        }

        return min($score, 100); // Cap at 100
    }
}
