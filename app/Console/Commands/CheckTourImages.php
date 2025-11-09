<?php

namespace App\Console\Commands;

use App\Models\Tour;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckTourImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check which tour and city images exist and which are missing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking tour and city images...');
        $this->newLine();

        $publicPath = public_path('images');
        $missingImages = [];
        $existingImages = [];

        // Check tour images
        $this->info('Checking Tour Images:');
        $this->line(str_repeat('-', 80));

        $tours = Tour::all();
        foreach ($tours as $tour) {
            $tourName = $tour->title;
            $this->line("Tour: {$tourName}");

            // Check hero image
            if ($tour->hero_image && File::exists(public_path($tour->hero_image))) {
                $this->line("  ✓ Hero image exists: {$tour->hero_image}");
                $existingImages[] = $tour->hero_image;
            } else if ($tour->hero_image) {
                $this->line("  ✗ Hero image missing: {$tour->hero_image}");
                $missingImages[] = $tour->hero_image;
            } else {
                $this->line("  - No hero image set");
            }
            $this->newLine();
        }

        // Check city images
        $this->info('Checking City Images:');
        $this->line(str_repeat('-', 80));

        $cities = \App\Models\City::all();
        foreach ($cities as $city) {
            $cityName = $city->name;
            $this->line("City: {$cityName}");

            // Check featured image
            if ($city->featured_image && File::exists(public_path($city->featured_image))) {
                $this->line("  ✓ Featured image exists: {$city->featured_image}");
                $existingImages[] = $city->featured_image;
            } else if ($city->featured_image) {
                $this->line("  ✗ Featured image missing: {$city->featured_image}");
                $missingImages[] = $city->featured_image;
            } else {
                $this->line("  - No featured image set");
            }
            $this->newLine();
        }

        // Summary
        $this->info('Summary:');
        $this->line(str_repeat('=', 80));
        $this->line("Total existing images: " . count($existingImages));
        $this->line("Total missing images: " . count($missingImages));
        $this->newLine();

        if (count($missingImages) > 0) {
            $this->warn('Missing Images:');
            foreach ($missingImages as $image) {
                $this->line("  - {$image}");
            }
        } else {
            $this->info('✓ All images are present!');
        }

        return 0;
    }
}

