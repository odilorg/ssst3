<?php

namespace App\Services;

use App\Models\Tour;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TourImageAssignmentService
{
    /**
     * Assign selected images to a tour
     *
     * @param Tour $tour
     * @param array $selectedImages
     * @return bool
     */
    public function assignImagesToTour(Tour $tour, array $selectedImages): bool
    {
        try {
            // Prepare hero image path
            $heroImage = $selectedImages['hero']['relative_path'];

            // Prepare gallery images array
            $galleryImages = [];
            foreach ($selectedImages['gallery'] as $index => $image) {
                $galleryImages[] = [
                    'path' => $image['relative_path'],
                    'alt' => $this->generateAltText($tour, $image, $index)
                ];
            }

            // Update tour
            $tour->update([
                'hero_image' => $heroImage,
                'gallery_images' => $galleryImages
            ]);

            Log::info('Tour images assigned successfully', [
                'tour_id' => $tour->id,
                'tour_title' => $tour->title,
                'hero_image' => $heroImage,
                'gallery_count' => count($galleryImages)
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to assign images to tour', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Generate SEO-friendly alt text for an image
     *
     * @param Tour $tour
     * @param array $image
     * @param int $index
     * @return string
     */
    private function generateAltText(Tour $tour, array $image, int $index): string
    {
        $tourTitle = $tour->title;

        // Extract meaningful name from filename
        $filename = pathinfo($image['filename'], PATHINFO_FILENAME);

        // Remove common patterns
        $filename = preg_replace('/^hero-?/i', '', $filename);
        $filename = preg_replace('/-\d+$/', '', $filename); // Remove trailing numbers

        // Convert to readable format
        $imageName = str_replace(['-', '_'], ' ', $filename);
        $imageName = Str::title($imageName);

        // Build alt text
        if (!empty($imageName) && $imageName !== 'Hero') {
            return "{$tourTitle} - {$imageName}";
        }

        // Fallback to generic description
        $cityName = $tour->city?->name ?? 'Uzbekistan';
        return "{$tourTitle} - {$cityName} Tour Photo " . ($index + 1);
    }

    /**
     * Clear images from a tour
     *
     * @param Tour $tour
     * @return bool
     */
    public function clearTourImages(Tour $tour): bool
    {
        try {
            $tour->update([
                'hero_image' => null,
                'gallery_images' => null
            ]);

            Log::info('Tour images cleared', [
                'tour_id' => $tour->id,
                'tour_title' => $tour->title
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to clear tour images', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get tour image statistics
     *
     * @return array
     */
    public function getTourImageStatistics(): array
    {
        $totalTours = Tour::count();
        $toursWithHero = Tour::whereNotNull('hero_image')->count();
        $toursWithGallery = Tour::whereNotNull('gallery_images')
            ->where('gallery_images', '!=', '[]')
            ->count();
        $toursWithoutImages = Tour::whereNull('hero_image')
            ->where(function ($query) {
                $query->whereNull('gallery_images')
                    ->orWhere('gallery_images', '[]');
            })
            ->count();

        return [
            'total_tours' => $totalTours,
            'tours_with_hero' => $toursWithHero,
            'tours_with_gallery' => $toursWithGallery,
            'tours_without_images' => $toursWithoutImages,
            'completion_percentage' => $totalTours > 0
                ? round(($toursWithHero / $totalTours) * 100, 2)
                : 0
        ];
    }

    /**
     * Validate selected images
     *
     * @param array $selectedImages
     * @return array Validation result
     */
    public function validateSelectedImages(array $selectedImages): array
    {
        $errors = [];

        // Check hero image exists
        if (!isset($selectedImages['hero'])) {
            $errors[] = 'Hero image not selected';
        } else {
            if (!isset($selectedImages['hero']['relative_path'])) {
                $errors[] = 'Hero image missing path';
            }
            if (!isset($selectedImages['hero']['filename'])) {
                $errors[] = 'Hero image missing filename';
            }
        }

        // Check gallery images
        if (!isset($selectedImages['gallery'])) {
            $errors[] = 'Gallery images not provided';
        } elseif (count($selectedImages['gallery']) !== 4) {
            $errors[] = 'Gallery must have exactly 4 images, found ' . count($selectedImages['gallery']);
        } else {
            foreach ($selectedImages['gallery'] as $index => $image) {
                if (!isset($image['relative_path'])) {
                    $errors[] = "Gallery image #{$index} missing path";
                }
                if (!isset($image['filename'])) {
                    $errors[] = "Gallery image #{$index} missing filename";
                }
            }
        }

        // Check for duplicates
        if (isset($selectedImages['hero']) && isset($selectedImages['gallery'])) {
            $allPaths = [$selectedImages['hero']['relative_path'] ?? ''];
            foreach ($selectedImages['gallery'] as $image) {
                $allPaths[] = $image['relative_path'] ?? '';
            }

            $uniquePaths = array_unique($allPaths);
            if (count($allPaths) !== count($uniquePaths)) {
                $errors[] = 'Duplicate images detected (hero and gallery must be unique)';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Preview image assignment without saving
     *
     * @param Tour $tour
     * @param array $selectedImages
     * @return array
     */
    public function previewAssignment(Tour $tour, array $selectedImages): array
    {
        return [
            'tour_id' => $tour->id,
            'tour_title' => $tour->title,
            'current_hero' => $tour->hero_image,
            'current_gallery_count' => is_array($tour->gallery_images) ? count($tour->gallery_images) : 0,
            'new_hero' => $selectedImages['hero']['relative_path'] ?? null,
            'new_hero_filename' => $selectedImages['hero']['filename'] ?? null,
            'new_gallery' => array_map(function ($img, $idx) use ($tour) {
                return [
                    'path' => $img['relative_path'],
                    'filename' => $img['filename'],
                    'alt' => $this->generateAltText($tour, $img, $idx)
                ];
            }, $selectedImages['gallery'], array_keys($selectedImages['gallery']))
        ];
    }
}
