<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = [
        'old_path',
        'new_path',
        'status_code',
        'is_active',
        'hits',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'hits' => 'integer',
        'status_code' => 'integer',
    ];

    /**
     * Increment hit counter
     */
    public function recordHit(): void
    {
        $this->increment('hits');
    }

    /**
     * Normalize path (remove trailing slash, lowercase)
     */
    public static function normalizePath(string $path): string
    {
        // Remove trailing slash
        $path = rtrim($path, '/');

        // Ensure leading slash
        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        return strtolower($path);
    }

    /**
     * Find active redirect by old path
     */
    public static function findByPath(string $path): ?self
    {
        $normalizedPath = self::normalizePath($path);

        return self::where('old_path', $normalizedPath)
            ->where('is_active', true)
            ->first();
    }
}
