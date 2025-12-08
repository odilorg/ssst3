<?php

namespace App\Services;

use App\Models\Tour;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImageDiscoveryService
{
    /**
     * Discover all images in the tours directory
     *
     * @return array
     */
    public function discoverAllImages(): array
    {
        $toursPath = public_path('images/tours');

        if (!File::exists($toursPath)) {
            Log::warning('Tours directory not found', ['path' => $toursPath]);
            return [];
        }

        $images = [];
        $extensions = ['jpg', 'jpeg', 'png', 'webp'];

        // Recursively scan all subdirectories
        $files = File::allFiles($toursPath);

        foreach ($files as $file) {
            $extension = strtolower($file->getExtension());

            if (in_array($extension, $extensions)) {
                $relativePath = str_replace(public_path() . '/', '', $file->getPathname());
                $relativePath = str_replace('\\', '/', $relativePath); // Windows compatibility

                $images[] = [
                    'full_path' => $file->getPathname(),
                    'relative_path' => $relativePath,
                    'filename' => $file->getFilename(),
                    'directory' => $file->getPath(),
                    'extension' => $extension,
                    'size' => $file->getSize(),
                ];
            }
        }

        Log::info('Discovered images', ['count' => count($images)]);

        return $images;
    }

    /**
     * Discover candidate images for a specific tour
     *
     * @param Tour $tour
     * @return array
     */
    public function discoverImagesForTour(Tour $tour): array
    {
        // Strategy 1: Look in tour's dedicated directory
        $tourSlug = $tour->slug;
        $tourPath = public_path("images/tours/{$tourSlug}");

        $images = [];

        if (File::exists($tourPath)) {
            $images = $this->scanDirectory($tourPath, $tourSlug);
        }

        // Strategy 2: If not enough images, look for similar tour directories
        if (count($images) < 5) {
            Log::info("Tour {$tour->id} has only " . count($images) . " images in dedicated directory");

            // Try to find similar directories by partial slug match
            $similarImages = $this->findSimilarTourImages($tour);
            $images = array_merge($images, $similarImages);
        }

        // Strategy 3: Fallback to city-based images
        if (count($images) < 5 && $tour->city) {
            Log::info("Looking for city-based images for tour {$tour->id}");
            $cityImages = $this->findCityImages($tour->city->slug);
            $images = array_merge($images, $cityImages);
        }

        // Strategy 4: Fallback to general image pool
        if (count($images) < 5) {
            Log::info("Looking in general image pool for tour {$tour->id}");
            $poolImages = $this->scanImagePool();
            $images = array_merge($images, $poolImages);
        }

        // Remove duplicates based on filename
        $images = collect($images)->unique('relative_path')->values()->all();

        return [
            'count' => count($images),
            'images' => $images,
            'tour_slug' => $tourSlug,
        ];
    }

    /**
     * Scan a directory for images
     *
     * @param string $path
     * @param string $context
     * @return array
     */
    private function scanDirectory(string $path, string $context = ''): array
    {
        $images = [];
        $extensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (!File::exists($path)) {
            return $images;
        }

        $files = File::files($path);

        foreach ($files as $file) {
            $extension = strtolower($file->getExtension());

            if (in_array($extension, $extensions)) {
                $relativePath = str_replace(public_path() . '/', '', $file->getPathname());
                $relativePath = str_replace('\\', '/', $relativePath);

                $images[] = [
                    'full_path' => $file->getPathname(),
                    'relative_path' => $relativePath,
                    'filename' => $file->getFilename(),
                    'directory' => $file->getPath(),
                    'extension' => $extension,
                    'size' => $file->getSize(),
                    'context' => $context,
                ];
            }
        }

        return $images;
    }

    /**
     * Find images from similar tour directories
     *
     * @param Tour $tour
     * @return array
     */
    private function findSimilarTourImages(Tour $tour): array
    {
        $images = [];
        $toursPath = public_path('images/tours');

        if (!File::exists($toursPath)) {
            return $images;
        }

        $directories = File::directories($toursPath);
        $tourSlug = $tour->slug;

        // Extract keywords from tour slug
        $keywords = explode('-', $tourSlug);

        foreach ($directories as $directory) {
            $dirName = basename($directory);

            // Skip if it's the tour's own directory (already scanned)
            if ($dirName === $tourSlug) {
                continue;
            }

            // Check if directory name contains any of the tour keywords
            $matches = false;
            foreach ($keywords as $keyword) {
                if (strlen($keyword) > 3 && str_contains($dirName, $keyword)) {
                    $matches = true;
                    break;
                }
            }

            if ($matches) {
                $similarImages = $this->scanDirectory($directory, "similar:{$dirName}");
                $images = array_merge($images, $similarImages);
            }
        }

        return $images;
    }

    /**
     * Find images related to a city
     *
     * @param string $citySlug
     * @return array
     */
    private function findCityImages(string $citySlug): array
    {
        $images = [];
        $toursPath = public_path('images/tours');

        if (!File::exists($toursPath)) {
            return $images;
        }

        $directories = File::directories($toursPath);

        foreach ($directories as $directory) {
            $dirName = basename($directory);

            // Check if directory name contains the city name
            if (str_contains($dirName, $citySlug)) {
                $cityImages = $this->scanDirectory($directory, "city:{$citySlug}");
                $images = array_merge($images, $cityImages);
            }
        }

        return $images;
    }

    /**
     * Scan the general image pool
     *
     * @return array
     */
    private function scanImagePool(): array
    {
        $images = [];
        $poolPath = public_path('images/tours/pool');

        if (!File::exists($poolPath)) {
            return $images;
        }

        // Scan all subdirectories in pool (architecture, landmarks, nature, etc.)
        $categories = File::directories($poolPath);

        foreach ($categories as $categoryPath) {
            $categoryName = basename($categoryPath);
            $categoryImages = $this->scanDirectory($categoryPath, "pool:{$categoryName}");
            $images = array_merge($images, $categoryImages);
        }

        Log::info('Scanned image pool', [
            'categories' => count($categories),
            'total_images' => count($images)
        ]);

        return $images;
    }

    /**
     * Get image dimensions
     *
     * @param string $path
     * @return array|null
     */
    public function getImageDimensions(string $path): ?array
    {
        try {
            $size = getimagesize($path);
            if ($size) {
                return [
                    'width' => $size[0],
                    'height' => $size[1],
                    'ratio' => round($size[0] / $size[1], 2),
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get image dimensions', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Encode image to base64 for AI processing
     *
     * @param string $path
     * @return string|null
     */
    public function encodeImageToBase64(string $path): ?string
    {
        try {
            $imageData = File::get($path);
            return base64_encode($imageData);
        } catch (\Exception $e) {
            Log::error('Failed to encode image', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
