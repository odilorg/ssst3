<?php

namespace App\Console\Commands;

use App\Models\Tour;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MigrateTourImagesToStorage extends Command
{
    protected $signature = 'tours:migrate-images-to-storage';

    protected $description = 'Migrate tour images from public/images/ to storage/app/public/ and update database paths';

    public function handle()
    {
        $this->info('Starting migration of tour images to Laravel storage...');

        $tours = Tour::whereNotNull('hero_image')
            ->where('hero_image', '!=', '')
            ->get();

        $this->info("Found {$tours->count()} tours with hero images");

        $migrated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($tours as $tour) {
            $oldPath = $tour->hero_image;

            // Check if image path starts with 'images/' (public folder)
            if (str_starts_with($oldPath, 'images/')) {
                $publicFilePath = public_path($oldPath);

                if (!File::exists($publicFilePath)) {
                    $this->warn("Tour #{$tour->id}: Image not found at {$publicFilePath}");
                    $errors++;
                    continue;
                }

                // Extract the filename and create new path in storage
                // Convert: images/tours/bukhara-city-tour/hero.jpg
                // To: tours/heroes/bukhara-city-tour-hero.jpg
                $pathInfo = pathinfo($oldPath);
                $dirName = basename($pathInfo['dirname']); // e.g., 'bukhara-city-tour'
                $fileName = $pathInfo['filename']; // e.g., 'hero'
                $extension = $pathInfo['extension']; // e.g., 'jpg'

                $newFileName = "{$dirName}-{$fileName}.{$extension}";
                $newStoragePath = "tours/heroes/{$newFileName}";

                // Check if file already exists in storage
                if (Storage::disk('public')->exists($newStoragePath)) {
                    $this->info("Tour #{$tour->id}: Already migrated, skipping");
                    $skipped++;
                    continue;
                }

                try {
                    // Copy file to storage
                    $fileContents = File::get($publicFilePath);
                    Storage::disk('public')->put($newStoragePath, $fileContents);

                    // Update tour record
                    $tour->hero_image = $newStoragePath;
                    $tour->image_processing_status = 'pending'; // Trigger WebP conversion
                    $tour->hero_image_webp = null;
                    $tour->hero_image_sizes = null;
                    $tour->save();

                    $this->info("Tour #{$tour->id}: Migrated {$oldPath} → {$newStoragePath}");
                    $migrated++;

                } catch (\Exception $e) {
                    $this->error("Tour #{$tour->id}: Failed to migrate - {$e->getMessage()}");
                    $errors++;
                }
            }
            // Check if image is already in storage (starts with 'tours/')
            else if (str_starts_with($oldPath, 'tours/')) {
                $storageFilePath = Storage::disk('public')->path($oldPath);

                if (File::exists($storageFilePath)) {
                    $this->info("Tour #{$tour->id}: Already in storage, checking WebP status");

                    // Trigger WebP conversion if not completed
                    if ($tour->image_processing_status !== 'completed') {
                        $tour->image_processing_status = 'pending';
                        $tour->save();
                        $this->info("Tour #{$tour->id}: Triggered WebP conversion");
                    }

                    $skipped++;
                } else {
                    $this->warn("Tour #{$tour->id}: Storage file not found at {$storageFilePath}");
                    $errors++;
                }
            }
            else {
                $this->warn("Tour #{$tour->id}: Unknown path format: {$oldPath}");
                $skipped++;
            }
        }

        $this->newLine();
        $this->info('Migration Summary:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Migrated', $migrated],
                ['Skipped', $skipped],
                ['Errors', $errors],
                ['Total', $tours->count()],
            ]
        );

        $this->newLine();
        $this->info('Next steps:');
        $this->line('1. Verify images display correctly in admin panel');
        $this->line('2. Queue worker will process WebP conversions automatically');
        $this->line('3. Check queue: php artisan queue:work');

        return Command::SUCCESS;
    }
}
