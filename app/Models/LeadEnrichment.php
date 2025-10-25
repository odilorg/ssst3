<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadEnrichment extends Model
{
    protected $table = 'lead_enrichments';

    protected $fillable = [
        'lead_id',
        'user_id',
        'action_id',
        'fields_before',
        'fields_after',
        'fields_changed',
        'ai_insights',
        'source',
    ];

    protected $casts = [
        'fields_before' => 'array',
        'fields_after' => 'array',
        'fields_changed' => 'array',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(LeadAIAction::class, 'action_id');
    }

    /**
     * Get enrichment history for a lead
     */
    public static function getHistory(int $leadId)
    {
        return self::where('lead_id', $leadId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
