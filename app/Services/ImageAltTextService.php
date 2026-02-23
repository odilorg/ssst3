<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageAltTextService
{
    /**
     * Generate SEO-friendly alt text for an image using Moonshot vision.
     *
     * @param  string       $imageUrl   Full URL or storage path of the image
     * @param  string|null  $context    Tour context (e.g. "Samarkand Day Tour in Samarkand")
     * @return string                   Generated alt text, or empty string on failure
     */
    public function generate(string $imageUrl, ?string $context = null): string
    {
        if (! config('services.ai_alt_text.enabled', false)) {
            return '';
        }

        $apiKey = config('services.ai_alt_text.api_key');
        if (! $apiKey) {
            Log::warning('ImageAltText: API key not configured');
            return '';
        }

        // Resolve storage paths to public URLs
        $fetchUrl = $this->resolveUrl($imageUrl);
        if (! $fetchUrl) {
            return '';
        }

        // Use thumb variant for repo images (cheaper, faster, smaller download)
        $fetchUrl = $this->useThumbVariant($fetchUrl);

        // Cache by stable key (storage path or URL path without query string)
        $cacheKey = 'alt_text:' . md5($this->stableCacheIdentifier($imageUrl));

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($fetchUrl, $context, $apiKey) {
            return $this->callVisionApi($fetchUrl, $context, $apiKey);
        });
    }

    /**
     * Call Moonshot vision API with base64-encoded image.
     */
    private function callVisionApi(string $imageUrl, ?string $context, string $apiKey): string
    {
        // Download the image and base64 encode it
        $imageData = $this->downloadAndEncode($imageUrl);
        if (! $imageData) {
            return '';
        }

        $contextLine = $context
            ? "This image is from \"{$context}\". Use the location name in your description. "
            : '';

        $prompt = $contextLine
            . "Write concise HTML alt text for this image. "
            . "Name the specific landmark, monument, or place if you can identify it from the context. "
            . "If you cannot identify it, describe the scene with the city/location name from context. "
            . "Max 125 characters, plain text only, no quotes.";

        $payload = [
            'model' => 'moonshot-v1-8k-vision-preview',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $prompt],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $imageData,
                            ],
                        ],
                    ],
                ],
            ],
            'max_tokens' => 100,
            'temperature' => 0.3,
        ];

        $maxAttempts = 2;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = Http::timeout(15)
                    ->withHeaders([
                        'Authorization' => "Bearer {$apiKey}",
                        'Content-Type' => 'application/json',
                    ])
                    ->post('https://api.moonshot.ai/v1/chat/completions', $payload);

                if ($response->status() === 429 || $response->status() === 503) {
                    if ($attempt < $maxAttempts) {
                        Log::info("ImageAltText: {$response->status()} on attempt {$attempt}, retrying...");
                        usleep(500_000);
                        continue;
                    }
                    Log::warning("ImageAltText: {$response->status()} after {$maxAttempts} attempts");
                    return '';
                }

                if ($response->failed()) {
                    Log::warning('ImageAltText: API error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    return '';
                }

                $text = $response->json('choices.0.message.content', '');

                return $this->sanitize($text);

            } catch (\Throwable $e) {
                Log::warning('ImageAltText: exception', ['message' => $e->getMessage()]);
                return '';
            }
        }

        return '';
    }

    /**
     * Download an image and return as base64 data URI.
     */
    private function downloadAndEncode(string $imageUrl): ?string
    {
        try {
            // For local storage paths, read directly from disk
            if (! str_starts_with($imageUrl, 'http://') && ! str_starts_with($imageUrl, 'https://')) {
                $disk = Storage::disk('public');
                if ($disk->exists($imageUrl)) {
                    $contents = $disk->get($imageUrl);
                    $mime = $disk->mimeType($imageUrl) ?: 'image/webp';
                    return "data:{$mime};base64," . base64_encode($contents);
                }
                return null;
            }

            // For URLs, download the image
            $response = Http::timeout(10)->get($imageUrl);
            if ($response->failed()) {
                Log::warning('ImageAltText: failed to download image', ['url' => $imageUrl, 'status' => $response->status()]);
                return null;
            }

            $contents = $response->body();
            $contentType = $response->header('Content-Type') ?: 'image/webp';

            // Limit to 5MB to avoid excessive memory/token usage
            if (strlen($contents) > 5 * 1024 * 1024) {
                Log::warning('ImageAltText: image too large', ['url' => $imageUrl, 'size' => strlen($contents)]);
                return null;
            }

            return "data:{$contentType};base64," . base64_encode($contents);

        } catch (\Throwable $e) {
            Log::warning('ImageAltText: download failed', ['url' => $imageUrl, 'message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Sanitize the AI output to clean, plain alt text.
     */
    private function sanitize(string $text): string
    {
        $text = trim($text, " \t\n\r\0\x0B\"'");
        $text = preg_replace('/\s+/', ' ', $text);
        $text = preg_replace('/[\x{1F600}-\x{1F64F}|\x{1F300}-\x{1F5FF}|\x{1F680}-\x{1F6FF}|\x{1F1E0}-\x{1F1FF}|\x{2600}-\x{26FF}|\x{2700}-\x{27BF}|\x{FE00}-\x{FE0F}|\x{1F900}-\x{1F9FF}|\x{200D}|\x{20E3}|\x{FE0F}]/u', '', $text);

        $text = trim($text);
        if (mb_strlen($text) > 125) {
            $text = mb_substr($text, 0, 122) . '...';
        }

        if (mb_strlen($text) < 5) {
            return '';
        }

        return $text;
    }

    /**
     * Resolve a storage path or URL to a fetchable URL.
     */
    private function resolveUrl(string $imageUrl): ?string
    {
        if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) {
            return $imageUrl;
        }

        // For storage paths, check existence but return path (downloadAndEncode reads directly)
        try {
            $disk = Storage::disk('public');
            if ($disk->exists($imageUrl)) {
                return $imageUrl;
            }
        } catch (\Throwable $e) {
            Log::warning('ImageAltText: cannot resolve storage path', ['path' => $imageUrl]);
        }

        return null;
    }

    /**
     * For repo images, swap large.webp → thumb.webp to reduce download size.
     */
    private function useThumbVariant(string $url): string
    {
        if (str_contains($url, 'images.staging-dev.uz')) {
            return preg_replace('/\/large\.webp$/', '/thumb.webp', $url);
        }

        return $url;
    }

    /**
     * Get a stable cache identifier (storage path or URL path without query string).
     */
    private function stableCacheIdentifier(string $imageUrl): string
    {
        if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) {
            return strtok($imageUrl, '?') ?: $imageUrl;
        }

        return $imageUrl;
    }
}
