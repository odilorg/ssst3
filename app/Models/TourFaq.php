<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourFaq extends Model
{
    protected $fillable = [
        'tour_id',
        'question',
        'answer',
        'sort_order',
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
