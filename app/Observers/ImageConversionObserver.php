<?php

namespace App\Observers;

use App\Jobs\ConvertImageToWebP;
use App\Jobs\ConvertGalleryImagesToWebP;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ImageConversionObserver
{
    protected array $config;
    protected static bool $enabled = true;

    public function __construct()
    {
        $this->config = config('image-conversion');
    }

    /**
     * Temporarily disable the observer
     */
    public static function disable(): void
    {
        static::$enabled = false;
    }

    /**
     * Re-enable the observer
     */
    public static function enable(): void
    {
        static::$enabled = true;
    }

    /**
     * Handle the Model "saved" event.
     * Dispatches image conversion jobs when images are uploaded
     */
    public function saved(Model $model): void
    {
        // Check if observer is enabled
        if (!static::$enabled) {
            return;
        }

        if (!$this->config['enabled']) {
            return;
        }

        // Get the model class name
        $modelClass = get_class($model);

        // Check if this model is configured for image conversion
        if (!isset($this->config['models'][$modelClass])) {
            return;
        }

        // Get the image fields to process for this model
        $imageFields = $this->config['models'][$modelClass];

        foreach ($imageFields as $fieldName) {
            // Check if the field has a value
            if (!$model->{$fieldName}) {
                continue;
            }

            // Determine if we should dispatch conversion job
            $shouldConvert = false;
            $reason = '';

            // Check if image field was modified (new upload)
            if ($model->isDirty($fieldName)) {
                $shouldConvert = true;
                $reason = 'field_modified';
            }
            // ONLY dispatch if status is explicitly 'pending' (not 'processing' or 'completed')
            elseif (isset($model->image_processing_status) &&
                    $model->image_processing_status === 'pending') {
                $shouldConvert = true;
                $reason = 'status_pending';
            }

            if ($shouldConvert) {
                $fieldValue = $model->{$fieldName};
                
                // Check if this is an array field (like gallery_images)
                if (is_array($fieldValue)) {
                    Log::info("Dispatching WebP conversion for gallery {}::{$fieldName}", [
                        'model_id' => $model->id,
                        'field' => $fieldName,
                        'image_count' => count($fieldValue),
                        'reason' => $reason,
                    ]);
                    
                    // Dispatch the gallery conversion job
                    ConvertGalleryImagesToWebP::dispatch($model, $fieldName);
                } else {
                    Log::info("Dispatching WebP conversion for {}::{$fieldName}", [
                        'model_id' => $model->id,
                        'field' => $fieldName,
                        'path' => $fieldValue,
                        'reason' => $reason,
                    ]);

                    // Dispatch the single image conversion job
                    ConvertImageToWebP::dispatch($model, $fieldName);
                }
            }
        }
    }
}
