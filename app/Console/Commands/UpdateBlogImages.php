<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use Illuminate\Console\Command;

class UpdateBlogImages extends Command
{
    protected $signature = 'blog:update-images';
    protected $description = 'Update all blog post featured images with new downloaded images';

    public function handle()
    {
        $this->info('Updating blog post featured images...');
        $this->info('');

        $updates = [
            1 => 'images/blog/uzbekistan-top-places.jpg',
            2 => 'images/blog/uzbek-cuisine-dishes.jpg',
            3 => 'images/blog/silk-road-caravan.jpg',
            4 => 'images/blog/first-time-visitor.jpg',
            5 => 'images/blog/registan-photography-featured.jpg',
            6 => 'images/blog/aral-sea-ship-cemetery.jpg',
            7 => 'images/blog/uzbekistan-planning-guide.jpg',
            8 => 'images/blog/samarkand-hidden-gems.jpg',
            9 => 'images/blog/yurt-aydarkul-sunset.jpg',
            10 => 'images/blog/traditional-uzbek-plov.jpg',
            11 => 'images/blog/marguzor-seven-lakes.jpg',
            12 => 'images/blog/margilan-silk-weaving.jpg',
            13 => 'images/blog/bukhara-history.jpg',
            14 => 'images/blog/khiva-night.jpg',
            15 => 'images/blog/uzbekistan-transport.jpg',
            16 => 'images/blog/uzbek-cuisine-variety.jpg',
            17 => 'images/blog/craftswomen-artisan.jpg',
            18 => 'images/blog/three-unesco-cities.jpg',
            19 => 'images/blog/hidden-uzbekistan.jpg',
            20 => 'images/blog/yurt-camping-aydarkul.jpg',
            21 => 'images/blog/fergana-valley-crafts.jpg',
            22 => 'images/blog/uzbekistan-photography.jpg',
            23 => 'images/blog/chimgan-mountains.jpg',
            24 => 'images/blog/timur-history.jpg',
        ];

        $updated = 0;
        $notFound = 0;

        foreach ($updates as $id => $imagePath) {
            $post = BlogPost::find($id);

            if ($post) {
                $oldPath = $post->featured_image;
                $post->featured_image = $imagePath;
                $post->save();

                $this->line("✓ Post #{$id}: {$post->title}");
                $this->line("  Old: {$oldPath}");
                $this->line("  New: {$imagePath}");
                $this->line('');

                $updated++;
            } else {
                $this->error("✗ Post #{$id} not found");
                $notFound++;
            }
        }

        $this->info('');
        $this->info("Summary:");
        $this->info("  Updated: {$updated} posts");
        if ($notFound > 0) {
            $this->warn("  Not found: {$notFound} posts");
        }
        $this->info('');
        $this->info('✓ All blog images updated successfully!');

        return Command::SUCCESS;
    }
}
