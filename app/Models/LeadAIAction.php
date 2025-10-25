<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadAIAction extends Model
{
    protected $fillable = [
        'lead_id',
        'user_id',
        'action_type',
        'input_data',
        'output_data',
        'result_summary',
        'status',
        'error_message',
        'tokens_used',
        'cost',
        'completed_at',
    ];

    protected $casts = [
        'input_data' => 'array',
        'output_data' => 'array',
        'completed_at' => 'datetime',
        'cost' => 'decimal:6',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get total cost for a lead
     */
    public static function getTotalCost(int $leadId): float
    {
        return self::where('lead_id', $leadId)
            ->where('status', 'completed')
            ->sum('cost');
    }

    /**
     * Get action count by type
     */
    public static function getActionStats(int $leadId): array
    {
        return self::where('lead_id', $leadId)
            ->where('status', 'completed')
            ->groupBy('action_type')
            ->selectRaw('action_type, count(*) as count, sum(cost) as total_cost')
            ->get()
            ->pluck('count', 'action_type')
            ->toArray();
    }
}
