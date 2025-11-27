<?php

namespace App\Jobs;

use App\Services\ImageConversionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ConvertGalleryImagesToWebP implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries;
    public int $timeout;
    public int $retryAfter;

    /**
     * Create a new job instance.
     *
     * @param mixed $model The Eloquent model instance
     * @param string $fieldName Name of the gallery field (e.g., 'gallery_images')
     * @param string $disk Storage disk (default: 'public')
     */
    public function __construct(
        public $model,
        public string $fieldName,
        public string $disk = 'public'
    ) {
        // Get queue configuration
        $queueConfig = config('image-conversion.queue');

        $this->tries = $queueConfig['tries'];
        $this->timeout = $queueConfig['timeout'];
        $this->retryAfter = $queueConfig['retry_after'];

        // Set the queue name
        $this->onQueue($queueConfig['queue']);
    }

    /**
     * Execute the job.
     */
    public function handle(ImageConversionService $conversionService): void
    {
        try {
            Log::info("Starting gallery image conversion job", [
                'model' => get_class($this->model),
                'model_id' => $this->model->id,
                'field' => $this->fieldName,
            ]);

            // Mark as processing
            $conversionService->markAsProcessing($this->model);

            // Get the gallery images array from the model
            $galleryImages = $this->model->{$this->fieldName};

            if (empty($galleryImages) || !is_array($galleryImages)) {
                throw new \Exception("No gallery images found in field {$this->fieldName}");
            }

            $convertedImages = [];
            $hasErrors = false;
            $errorMessages = [];

            foreach ($galleryImages as $index => $imageData) {
                try {
                    // Handle both array format [{path, alt}] and simple string array
                    $originalPath = is_array($imageData) ? ($imageData['path'] ?? null) : $imageData;
                    $altText = is_array($imageData) ? ($imageData['alt'] ?? '') : '';

                    if (empty($originalPath)) {
                        Log::warning("Empty path in gallery image at index {$index}", [
                            'model_id' => $this->model->id,
                        ]);
                        continue;
                    }

                    // Check if already a WebP image
                    if (str_ends_with(strtolower($originalPath), '.webp')) {
                        Log::info("Gallery image already WebP, skipping", [
                            'path' => $originalPath,
                            'index' => $index,
                        ]);
                        $convertedImages[] = [
                            'path' => $originalPath,
                            'alt' => $altText,
                        ];
                        continue;
                    }

                    // Convert the image to WebP
                    $result = $conversionService->convertToWebP($originalPath, $this->disk);

                    $convertedImages[] = [
                        'path' => $result['webp_path'],
                        'alt' => $altText,
                        'sizes' => $result['sizes'],
                    ];

                    Log::info("Converted gallery image", [
                        'original' => $originalPath,
                        'webp' => $result['webp_path'],
                        'index' => $index,
                    ]);

                } catch (\Exception $e) {
                    $hasErrors = true;
                    $errorMessages[] = "Image {$index}: " . $e->getMessage();
                    
                    Log::error("Failed to convert gallery image", [
                        'index' => $index,
                        'path' => $originalPath ?? 'unknown',
                        'error' => $e->getMessage(),
                    ]);

                    // Keep original image data if conversion fails
                    $convertedImages[] = is_array($imageData) ? $imageData : ['path' => $imageData, 'alt' => ''];
                }
            }

            // Update the model with converted images
            $this->updateModelGallery($convertedImages, $conversionService, $hasErrors, $errorMessages);

            Log::info("Gallery image conversion job completed", [
                'model' => get_class($this->model),
                'model_id' => $this->model->id,
                'total_images' => count($galleryImages),
                'converted' => count($convertedImages),
                'has_errors' => $hasErrors,
            ]);

        } catch (\Exception $e) {
            Log::error("Gallery image conversion job failed", [
                'model' => get_class($this->model),
                'model_id' => $this->model->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Mark as failed in the database
            $conversionService->markAsFailed($this->model, $e->getMessage());

            // Re-throw to let queue handle retries
            throw $e;
        }
    }

    /**
     * Update the model with converted gallery images
     */
    protected function updateModelGallery(array $convertedImages, ImageConversionService $conversionService, bool $hasErrors, array $errorMessages): void
    {
        // Disable observer to prevent infinite loop
        \App\Observers\ImageConversionObserver::disable();

        $status = $hasErrors ? 'partial' : 'completed';
        $errorMessage = $hasErrors ? implode('; ', $errorMessages) : null;

        $this->model->forceFill([
            $this->fieldName => $convertedImages,
            'image_processing_status' => $status,
            'image_processing_error' => $errorMessage,
        ])->save();

        // Re-enable observer
        \App\Observers\ImageConversionObserver::enable();

        Log::info("Updated model with converted gallery images", [
            'model' => get_class($this->model),
            'id' => $this->model->id,
            'status' => $status,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Gallery image conversion job failed permanently", [
            'model' => get_class($this->model),
            'model_id' => $this->model->id,
            'error' => $exception->getMessage(),
        ]);

        // Mark as failed in the database
        $conversionService = app(ImageConversionService::class);
        $conversionService->markAsFailed($this->model, $exception->getMessage());
    }
}
