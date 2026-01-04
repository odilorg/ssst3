<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'user_id',
        'source_locale',
        'target_locale',
        'sections_translated',
        'tokens_used',
        'cost_usd',
        'model',
        'status',
        'error_message',
    ];

    protected $casts = [
        'sections_translated' => 'array',
        'tokens_used' => 'integer',
        'cost_usd' => 'decimal:4',
    ];

    /**
     * Get the tour that was translated
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Get the user who triggered the translation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for successful translations
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for failed translations
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Get total cost for a specific time period
     */
    public static function getTotalCost(string $period = 'month'): float
    {
        $query = self::completed();

        switch ($period) {
            case 'day':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->where('created_at', '>=', now()->startOfWeek());
                break;
            case 'month':
            default:
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
        }

        return (float) $query->sum('cost_usd');
    }

    /**
     * Get total translations count for a specific time period
     */
    public static function getTotalCount(string $period = 'month'): int
    {
        $query = self::completed();

        switch ($period) {
            case 'day':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->where('created_at', '>=', now()->startOfWeek());
                break;
            case 'month':
            default:
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
        }

        return $query->count();
    }
}
