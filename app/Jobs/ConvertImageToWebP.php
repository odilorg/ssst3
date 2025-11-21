<?php

namespace App\Jobs;

use App\Services\ImageConversionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConvertImageToWebP implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries;
    public int $timeout;
    public int $retryAfter;

    /**
     * Create a new job instance.
     *
     * @param mixed $model The Eloquent model instance
     * @param string $fieldName Name of the image field (e.g., 'hero_image', 'featured_image')
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
            Log::info("Starting image conversion job", [
                'model' => get_class($this->model),
                'model_id' => $this->model->id,
                'field' => $this->fieldName,
            ]);

            // Mark as processing
            $conversionService->markAsProcessing($this->model);

            // Get the original image path from the model
            $originalPath = $this->model->{$this->fieldName};

            if (empty($originalPath)) {
                throw new \Exception("No image found in field {$this->fieldName}");
            }

            // Convert the image to WebP and generate responsive sizes
            $result = $conversionService->convertToWebP($originalPath, $this->disk);

            // Update the model with WebP paths
            $conversionService->updateModelPaths(
                $this->model,
                $this->fieldName,
                $result['webp_path'],
                $result['sizes']
            );

            Log::info("Image conversion job completed successfully", [
                'model' => get_class($this->model),
                'model_id' => $this->model->id,
                'webp_path' => $result['webp_path'],
            ]);
        } catch (\Exception $e) {
            Log::error("Image conversion job failed", [
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
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Image conversion job failed permanently", [
            'model' => get_class($this->model),
            'model_id' => $this->model->id,
            'error' => $exception->getMessage(),
        ]);

        // Mark as failed in the database
        $conversionService = app(ImageConversionService::class);
        $conversionService->markAsFailed($this->model, $exception->getMessage());
    }
}
