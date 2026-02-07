<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourPlatformMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'external_tour_id',
        'external_tour_name',
        'keywords',
        'match_confidence',
        'tour_id',
        'auto_confirm',
        'default_booking_type',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'auto_confirm' => 'boolean',
        'is_active' => 'boolean',
        'keywords' => 'array',
        'match_confidence' => 'integer',
    ];

    // Platforms
    public const PLATFORM_GYG = 'gyg';
    public const PLATFORM_VIATOR = 'viator';
    public const PLATFORM_KLOOK = 'klook';
    public const PLATFORM_DIRECT = 'direct';

    public static function platforms(): array
    {
        return [
            self::PLATFORM_GYG => 'GetYourGuide',
            self::PLATFORM_VIATOR => 'Viator',
            self::PLATFORM_KLOOK => 'Klook',
        ];
    }

    // Relationships
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Check if this mapping matches the given tour name by keywords
     * Returns confidence score (0-100) or null if no match
     */
    public function matchesByKeywords(string $tourName): ?int
    {
        if (empty($this->keywords)) {
            return null;
        }

        $tourNameLower = strtolower($tourName);
        $matchedKeywords = 0;
        $totalKeywords = count($this->keywords);

        foreach ($this->keywords as $keyword) {
            if (str_contains($tourNameLower, strtolower($keyword))) {
                $matchedKeywords++;
            }
        }

        if ($matchedKeywords === 0) {
            return null;
        }

        // Calculate confidence based on keyword matches
        // More keywords matched = higher confidence
        return (int) round(($matchedKeywords / $totalKeywords) * 100);
    }

    /**
     * Find best mapping by keywords for a given tour name
     * Returns mapping with highest confidence score
     */
    public static function findByKeywords(string $platform, string $tourName): ?array
    {
        $mappings = static::active()->forPlatform($platform)->get();
        $bestMatch = null;
        $bestConfidence = 0;

        foreach ($mappings as $mapping) {
            $confidence = $mapping->matchesByKeywords($tourName);
            if ($confidence !== null && $confidence > $bestConfidence) {
                $bestConfidence = $confidence;
                $bestMatch = $mapping;
            }
        }

        if ($bestMatch && $bestConfidence >= $bestMatch->match_confidence) {
            return [
                'mapping' => $bestMatch,
                'confidence' => $bestConfidence,
                'method' => 'keyword',
            ];
        }

        return null;
    }

    /**
     * Find mapping by external tour - tries exact match first, then keywords
     */
    public static function findByExternalTour(string $platform, ?string $externalTourId = null, ?string $externalTourName = null): ?self
    {
        $query = static::active()->forPlatform($platform);

        // Try exact ID match first
        if ($externalTourId) {
            $mapping = $query->where('external_tour_id', $externalTourId)->first();
            if ($mapping) return $mapping;
        }

        // Try exact name match
        if ($externalTourName) {
            $mapping = static::active()
                ->forPlatform($platform)
                ->where('external_tour_name', $externalTourName)
                ->first();
            if ($mapping) return $mapping;
        }

        // Try keyword match (returns mapping from array if found)
        if ($externalTourName) {
            $result = static::findByKeywords($platform, $externalTourName);
            if ($result) {
                return $result['mapping'];
            }
        }

        return null;
    }

    /**
     * Smart find with full result info (for API)
     */
    public static function smartFind(string $platform, string $tourName): array
    {
        // 1. Try exact name match
        $mapping = static::active()
            ->forPlatform($platform)
            ->where('external_tour_name', $tourName)
            ->first();
        
        if ($mapping) {
            return [
                'found' => true,
                'mapping' => $mapping,
                'tour_id' => $mapping->tour_id,
                'confidence' => 100,
                'method' => 'exact',
            ];
        }

        // 2. Try keyword match
        $keywordResult = static::findByKeywords($platform, $tourName);
        if ($keywordResult) {
            return [
                'found' => true,
                'mapping' => $keywordResult['mapping'],
                'tour_id' => $keywordResult['mapping']->tour_id,
                'confidence' => $keywordResult['confidence'],
                'method' => 'keyword',
            ];
        }

        // 3. No match found
        return [
            'found' => false,
            'mapping' => null,
            'tour_id' => null,
            'confidence' => 0,
            'method' => 'none',
        ];
    }
}
