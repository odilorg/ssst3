<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageAltTextService
{
    /**
     * Generate SEO-friendly alt text for an image using GPT-4o-mini vision.
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
            Log::warning('ImageAltText: OPENAI_VISION_API_KEY not configured');
            return '';
        }

        // Resolve storage paths to public URLs
        $fetchUrl = $this->resolveUrl($imageUrl);
        if (! $fetchUrl) {
            return '';
        }

        // Use thumb variant for repo images (cheaper, faster)
        $fetchUrl = $this->useThumbVariant($fetchUrl);

        // Cache by stable key (storage path or URL path without query string)
        $cacheKey = 'alt_text:' . md5($this->stableCacheIdentifier($imageUrl));

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($fetchUrl, $context, $apiKey) {
            return $this->callVisionApi($fetchUrl, $context, $apiKey);
        });
    }

    /**
     * Call GPT-4o-mini vision API.
     */
    private function callVisionApi(string $imageUrl, ?string $context, string $apiKey): string
    {
        $contextLine = $context
            ? "Context: this image is from a tour called \"{$context}\". "
            : '';

        $prompt = "Describe what's visible in this image naturally and concisely for use as HTML alt text. "
            . "Include landmarks, architecture, or landscape only if clearly recognizable. "
            . "If unsure about a specific place or landmark name, describe it generally — do not guess. "
            . $contextLine
            . "Max 125 characters, plain text only, no quotes.";

        $payload = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $prompt],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $imageUrl,
                                'detail' => 'low',
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
                $response = Http::timeout(10)
                    ->withHeaders([
                        'Authorization' => "Bearer {$apiKey}",
                        'Content-Type' => 'application/json',
                    ])
                    ->post('https://api.openai.com/v1/chat/completions', $payload);

                if ($response->status() === 429 || $response->status() === 503) {
                    if ($attempt < $maxAttempts) {
                        Log::info("ImageAltText: {$response->status()} on attempt {$attempt}, retrying...");
                        usleep(500_000); // 0.5s backoff
                        continue;
                    }
                    Log::warning("ImageAltText: {$response->status()} after {$maxAttempts} attempts");
                    return '';
                }

                if ($response->failed()) {
                    Log::warning('ImageAltText: API error', [
                        'status' => $response->status(),
                        'reason' => $response->reason(),
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
     * Sanitize the AI output to clean, plain alt text.
     */
    private function sanitize(string $text): string
    {
        // Remove quotes wrapping the text
        $text = trim($text, " \t\n\r\0\x0B\"'");

        // Remove newlines and collapse whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        // Remove emojis (Unicode emoticons, symbols, etc.)
        $text = preg_replace('/[\x{1F600}-\x{1F64F}|\x{1F300}-\x{1F5FF}|\x{1F680}-\x{1F6FF}|\x{1F1E0}-\x{1F1FF}|\x{2600}-\x{26FF}|\x{2700}-\x{27BF}|\x{FE00}-\x{FE0F}|\x{1F900}-\x{1F9FF}|\x{200D}|\x{20E3}|\x{FE0F}]/u', '', $text);

        // Trim and truncate to 125 chars
        $text = trim($text);
        if (mb_strlen($text) > 125) {
            $text = mb_substr($text, 0, 122) . '...';
        }

        // Return empty if result is too short to be useful
        if (mb_strlen($text) < 5) {
            return '';
        }

        return $text;
    }

    /**
     * Resolve a storage path or URL to a publicly accessible URL.
     */
    private function resolveUrl(string $imageUrl): ?string
    {
        // Already a full URL
        if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) {
            return $imageUrl;
        }

        // Local storage path — resolve to public URL
        try {
            $disk = Storage::disk('public');
            if ($disk->exists($imageUrl)) {
                return $disk->url($imageUrl);
            }
        } catch (\Throwable $e) {
            Log::warning('ImageAltText: cannot resolve storage path', ['path' => $imageUrl]);
        }

        return null;
    }

    /**
     * For repo images, swap large.webp → thumb.webp to reduce cost.
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
        // For full URLs, strip query string
        if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) {
            return strtok($imageUrl, '?') ?: $imageUrl;
        }

        // For storage paths, use as-is (already stable)
        return $imageUrl;
    }
}
