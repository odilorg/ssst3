<?php

namespace App\Services;

use App\Observers\ImageConversionObserver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\WebpEncoder;

class ImageConversionService
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('image-conversion');
    }

    /**
     * Convert an image to WebP format and generate responsive sizes
     *
     * @param string $originalPath Path to the original image (relative to storage/app/public)
     * @param string $disk Storage disk (default: 'public')
     * @return array Array with 'webp_path' and 'sizes' keys
     * @throws \Exception
     */
    public function convertToWebP(string $originalPath, string $disk = 'public'): array
    {
        // Check if conversion is enabled
        if (!$this->config['enabled']) {
            throw new \Exception('Image conversion is disabled in configuration');
        }

        // Build full path to original image
        // Handle images in public folder vs storage folder
        if (str_starts_with($originalPath, 'images/')) {
            // Image is in public/images folder
            $fullPath = public_path($originalPath);
        } else {
            // Image is in storage/app/public folder
            $fullPath = Storage::disk($disk)->path($originalPath);
        }

        if (!file_exists($fullPath)) {
            throw new \Exception("Original image not found: {$fullPath}");
        }

        // Load the image
        $image = Image::read($fullPath);

        // Get original dimensions
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        Log::info("Converting image to WebP", [
            'original_path' => $originalPath,
            'original_width' => $originalWidth,
            'original_height' => $originalHeight,
        ]);

        // Generate a unique filename for WebP
        $pathInfo = pathinfo($originalPath);
        $directory = $pathInfo['dirname'] === '.' ? '' : $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $uniqueId = uniqid();

        // Create WebP directory if it doesn't exist
        $webpDir = 'images/webp/' . ($directory ? $directory . '/' : '');
        Storage::disk($disk)->makeDirectory($webpDir);

        // Generate responsive sizes
        $sizes = [];
        $configSizes = $this->config['sizes'];

        foreach ($configSizes as $sizeName => $maxWidth) {
            // Only generate size if original is larger than target size
            if ($originalWidth > $maxWidth || !$this->config['optimization']['upscale']) {
                $resizedImage = clone $image;

                // Calculate height to maintain aspect ratio
                if ($this->config['optimization']['maintain_aspect_ratio']) {
                    $resizedImage->scale(width: $maxWidth);
                } else {
                    $resizedImage->resize($maxWidth, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }

                // Strip metadata if configured
                if ($this->config['optimization']['strip_metadata']) {
                    $resizedImage->exif(null);
                }

                // Encode to WebP
                $encoded = $resizedImage->encode(new WebpEncoder(quality: $this->config['quality']));

                // Save the resized WebP image
                $sizeFilename = "{$filename}_{$sizeName}_{$uniqueId}.webp";
                $sizePath = $webpDir . $sizeFilename;

                Storage::disk($disk)->put($sizePath, (string) $encoded);

                $sizes[$sizeName] = $sizePath;

                Log::info("Generated responsive size", [
                    'size_name' => $sizeName,
                    'path' => $sizePath,
                    'width' => $resizedImage->width(),
                    'height' => $resizedImage->height(),
                ]);
            } else {
                // Original is smaller or equal, use original size
                $encoded = $image->encode(new WebpEncoder(quality: $this->config['quality']));

                $sizeFilename = "{$filename}_{$sizeName}_{$uniqueId}.webp";
                $sizePath = $webpDir . $sizeFilename;

                Storage::disk($disk)->put($sizePath, (string) $encoded);

                $sizes[$sizeName] = $sizePath;

                Log::info("Generated size (no resize needed)", [
                    'size_name' => $sizeName,
                    'path' => $sizePath,
                ]);
            }
        }

        // Also create a main WebP version (largest size)
        $mainEncoded = $image->encode(new WebpEncoder(quality: $this->config['quality']));
        $mainFilename = "{$filename}_main_{$uniqueId}.webp";
        $mainPath = $webpDir . $mainFilename;

        Storage::disk($disk)->put($mainPath, (string) $mainEncoded);

        Log::info("Generated main WebP image", [
            'path' => $mainPath,
        ]);

        // Delete original if configured
        if (!$this->config['keep_original']) {
            Storage::disk($disk)->delete($originalPath);
            Log::info("Deleted original image", ['path' => $originalPath]);
        }

        return [
            'webp_path' => $mainPath,
            'sizes' => $sizes,
        ];
    }

    /**
     * Update model with WebP paths
     *
     * @param mixed $model The Eloquent model to update
     * @param string $fieldName Name of the image field
     * @param string $webpPath Path to the main WebP image
     * @param array $sizes Array of responsive size paths
     * @return void
     */
    public function updateModelPaths($model, string $fieldName, string $webpPath, array $sizes): void
    {
        // Disable observer to prevent infinite loop
        ImageConversionObserver::disable();

        $webpFieldName = $fieldName . '_webp';
        $sizesFieldName = $fieldName . '_sizes';

        $model->forceFill([
            $webpFieldName => $webpPath,
            $sizesFieldName => json_encode($sizes),
            'image_processing_status' => 'completed',
            'image_processing_error' => null,
        ])->save();

        // Re-enable observer
        ImageConversionObserver::enable();

        Log::info("Updated model with WebP paths", [
            'model' => get_class($model),
            'id' => $model->id,
            'webp_path' => $webpPath,
        ]);
    }

    /**
     * Mark model conversion as failed
     *
     * @param mixed $model The Eloquent model to update
     * @param string $errorMessage Error message to store
     * @return void
     */
    public function markAsFailed($model, string $errorMessage): void
    {
        // Disable observer to prevent infinite loop
        ImageConversionObserver::disable();

        $model->forceFill([
            'image_processing_status' => 'failed',
            'image_processing_error' => $errorMessage,
        ])->save();

        // Re-enable observer
        ImageConversionObserver::enable();

        Log::error("Image conversion failed", [
            'model' => get_class($model),
            'id' => $model->id,
            'error' => $errorMessage,
        ]);
    }

    /**
     * Mark model conversion as processing
     *
     * @param mixed $model The Eloquent model to update
     * @return void
     */
    public function markAsProcessing($model): void
    {
        // Disable observer to prevent infinite loop
        ImageConversionObserver::disable();

        $model->forceFill([
            'image_processing_status' => 'processing',
            'image_processing_error' => null,
        ])->save();

        // Re-enable observer
        ImageConversionObserver::enable();
    }

    /**
     * Get the responsive image path for a specific size
     *
     * @param array $sizes JSON-decoded sizes array
     * @param string $sizeName Size name (thumb, medium, large, xlarge)
     * @return string|null
     */
    public function getSizePath(array $sizes, string $sizeName): ?string
    {
        return $sizes[$sizeName] ?? null;
    }

    /**
     * Check if image needs conversion
     *
     * @param mixed $model The Eloquent model to check
     * @return bool
     */
    public function needsConversion($model): bool
    {
        return $model->image_processing_status === 'pending'
            || $model->image_processing_status === 'failed';
    }
}