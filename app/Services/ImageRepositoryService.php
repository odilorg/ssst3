<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageRepositoryService
{
    protected string $baseUrl;
    protected ?string $pickerSecret;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.image_repo.url', 'https://images.staging-dev.uz'), '/');
        $this->pickerSecret = config('services.image_repo.picker_secret');
    }

    /**
     * Get a read-only picker JWT token.
     * Cached for 10 minutes (token has 15 min TTL).
     */
    public function getPickerToken(): ?string
    {
        return Cache::remember('image_repo_picker_token', 600, function () {
            if (empty($this->pickerSecret)) {
                Log::error('IMAGE_REPO_PICKER_SECRET not configured');
                return null;
            }

            $timestamp = time();
            $signature = hash_hmac('sha256', (string) $timestamp, $this->pickerSecret);

            try {
                $response = Http::timeout(5)
                    ->connectTimeout(3)
                    ->post("{$this->baseUrl}/api/picker/token", [
                        'timestamp' => $timestamp,
                        'signature' => $signature,
                    ]);

                if ($response->successful()) {
                    return $response->json('token');
                }

                Log::error('Failed to get picker token', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            } catch (\Exception $e) {
                Log::error('Picker token request failed', ['error' => $e->getMessage()]);
                return null;
            }
        });
    }

    /**
     * Get the picker URL (no token in URL).
     */
    public function getPickerUrl(): string
    {
        return "{$this->baseUrl}?mode=picker";
    }

    /**
     * Get the base URL for the image repository.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
