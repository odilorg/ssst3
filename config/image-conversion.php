<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Conversion Enabled
    |--------------------------------------------------------------------------
    |
    | Enable or disable automatic image conversion to WebP format.
    | When disabled, images will be stored in their original format.
    |
    */
    'enabled' => env('IMAGE_CONVERSION_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Output Format
    |--------------------------------------------------------------------------
    |
    | The image format to convert to. Currently supports 'webp'.
    | WebP provides 25-35% better compression than JPEG/PNG.
    |
    */
    'format' => 'webp',

    /*
    |--------------------------------------------------------------------------
    | WebP Quality
    |--------------------------------------------------------------------------
    |
    | Quality level for WebP conversion (1-100).
    | Recommended: 85 for general use, 90 for high-quality images.
    | Lower values = smaller file size but reduced quality.
    |
    */
    'quality' => env('IMAGE_WEBP_QUALITY', 85),

    /*
    |--------------------------------------------------------------------------
    | Responsive Image Sizes
    |--------------------------------------------------------------------------
    |
    | Define the sizes to generate for responsive images.
    | Each size will be created automatically on upload.
    |
    | Format: 'name' => width_in_pixels
    |
    */
    'sizes' => [
        'thumb' => 300,      // Mobile phones
        'medium' => 800,     // Tablets
        'large' => 1920,     // Desktop/laptop
        'xlarge' => 2560,    // Retina/4K displays
    ],

    /*
    |--------------------------------------------------------------------------
    | Keep Original Files
    |--------------------------------------------------------------------------
    |
    | If true, original JPG/PNG files will be kept after conversion.
    | If false, original files will be deleted to save storage space.
    |
    | Recommended: false (save storage)
    |
    */
    'keep_original' => env('IMAGE_KEEP_ORIGINAL', false),

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the queue for processing image conversions.
    | Using a queue ensures uploads don't block the admin UI.
    |
    */
    'queue' => [
        'enabled' => true,
        'connection' => env('QUEUE_CONNECTION', 'database'),
        'queue' => 'image-processing',
        'tries' => 3,           // Number of retry attempts
        'timeout' => 300,       // 5 minutes timeout
        'retry_after' => 600,   // Retry after 10 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Processing Driver
    |--------------------------------------------------------------------------
    |
    | The driver to use for image manipulation.
    | Options: 'imagick' (recommended, faster) or 'gd' (fallback)
    |
    | Imagick requires the PHP imagick extension installed.
    |
    */
    'driver' => env('IMAGE_DRIVER', 'imagick'),

    /*
    |--------------------------------------------------------------------------
    | Directory Paths
    |--------------------------------------------------------------------------
    |
    | Configure where images are stored during and after processing.
    |
    */
    'temp_dir' => storage_path('app/temp/images'),

    'output_dir' => storage_path('app/public/images/webp'),

    /*
    |--------------------------------------------------------------------------
    | Models to Process
    |--------------------------------------------------------------------------
    |
    | List of models and their image fields that should be converted.
    | Format: 'ModelClass' => ['field1', 'field2']
    |
    */
    'models' => [
        \App\Models\Tour::class => [
            'hero_image',
            'gallery_images',
        ],
        \App\Models\BlogPost::class => [
            'featured_image',
        ],
        \App\Models\City::class => [
            'hero_image',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Batch Processing
    |--------------------------------------------------------------------------
    |
    | Configuration for batch converting existing images.
    |
    */
    'batch' => [
        'chunk_size' => 10,      // Process 10 images per batch
        'delay_seconds' => 2,    // Delay between batches to avoid CPU spikes
    ],

    /*
    |--------------------------------------------------------------------------
    | Optimization Settings
    |--------------------------------------------------------------------------
    |
    | Additional optimization settings for image processing.
    |
    */
    'optimization' => [
        'strip_metadata' => true,         // Remove EXIF data to reduce file size
        'progressive' => false,           // Not applicable to WebP
        'maintain_aspect_ratio' => true,  // Always maintain aspect ratio
        'upscale' => false,              // Never upscale images beyond original size
    ],

];
