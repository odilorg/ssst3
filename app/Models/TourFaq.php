<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TourFaq extends Model
{
    use HasTranslations;

    protected $fillable = [
        'tour_id',
        'question',
        'answer',
        'sort_order',
    ];

    public array $translatable = [
        'question',
        'answer',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Get the tour this FAQ belongs to
     */
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
