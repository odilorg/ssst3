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
        'question' => 'array',
        'answer' => 'array',
    ];

    /**
     * Get the question in the current locale
     */
    public function getQuestionTextAttribute(): string
    {
        $question = $this->question;

        if (is_string($question)) {
            return $question;
        }

        if (is_array($question)) {
            $locale = app()->getLocale();
            return $question[$locale] ?? $question['en'] ?? '';
        }

        return '';
    }

    /**
     * Get the answer in the current locale
     */
    public function getAnswerTextAttribute(): string
    {
        $answer = $this->answer;

        if (is_string($answer)) {
            return $answer;
        }

        if (is_array($answer)) {
            $locale = app()->getLocale();
            return $answer[$locale] ?? $answer['en'] ?? '';
        }

        return '';
    }

    /**
     * Get the tour this FAQ belongs to
     */
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
