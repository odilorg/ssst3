<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogComment extends Model
{
    protected $fillable = [
        'blog_post_id',
        'parent_id',
        'author_name',
        'author_email',
        'author_website',
        'author_ip',
        'author_user_agent',
        'comment',
        'status',
        'spam_score',
        'approved_at',
        'approved_by',
        'flag_count',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'spam_score' => 'integer',
        'flag_count' => 'integer',
    ];

    /**
     * Get the blog post this comment belongs to
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }

    /**
     * Get the parent comment (for replies)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(BlogComment::class, 'parent_id');
    }

    /**
     * Get all replies to this comment
     */
    public function replies(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'parent_id')
            ->where('status', 'approved')
            ->oldest();
    }

    /**
     * Get the user who approved this comment
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: Only approved comments
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Only pending comments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Only spam comments
     */
    public function scopeSpam($query)
    {
        return $query->where('status', 'spam');
    }

    /**
     * Scope: Only top-level comments (not replies)
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Check if comment is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if comment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if comment is spam
     */
    public function isSpam(): bool
    {
        return $this->status === 'spam';
    }

    /**
     * Get Gravatar URL for comment author
     */
    public function getGravatarUrlAttribute(): string
    {
        $hash = md5(strtolower(trim($this->author_email)));
        return "https://www.gravatar.com/avatar/{$hash}?s=80&d=mp";
    }

    /**
     * Approve the comment
     */
    public function approve(?int $approvedBy = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approvedBy,
        ]);
    }

    /**
     * Mark as spam
     */
    public function markAsSpam(): void
    {
        $this->update(['status' => 'spam']);
    }

    /**
     * Move to trash
     */
    public function trash(): void
    {
        $this->update(['status' => 'trash']);
    }

    /**
     * Increment flag count
     */
    public function incrementFlagCount(): void
    {
        $this->increment('flag_count');

        // Auto-mark as spam if too many flags
        if ($this->flag_count >= 3) {
            $this->markAsSpam();
        }
    }
}
