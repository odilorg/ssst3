<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogAIGeneration extends Model
{
    protected $table = 'blog_ai_generations';
    
    protected $fillable = [
        'user_id',
        'blog_post_id',
        'status',
        'input_parameters',
        'ai_response',
        'error_message',
        'tokens_used',
        'cost',
        'completed_at',
    ];

    protected $casts = [
        'input_parameters' => 'array',
        'ai_response' => 'array',
        'completed_at' => 'datetime',
        'tokens_used' => 'integer',
        'cost' => 'decimal:4',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function blogPost(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class);
    }
}
