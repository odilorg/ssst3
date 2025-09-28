<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpokenLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // Relationships
    public function guides()
    {
        return $this->belongsToMany(Guide::class, 'guide_spoken_language');
    }
}
