<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourAIGeneration extends Model
{
    protected $table = 'tour_ai_generations';

    protected $fillable = [
        'tour_id',
        'user_id',
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
        'cost' => 'decimal:6',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
