<?php

namespace App\Console\Commands;

use App\Models\TourExtra;
use Illuminate\Console\Command;

class ConvertExtrasIconsToNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extras:convert-icons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert tour extras SVG icons to icon names';

    /**
     * Icon detection mapping
     */
    protected array $iconMap = [
        'icon--car' => 'car',
        'M18 7l-2-4H6L4 7H0v8h2v3h3v-3h12v3h3v-3h2V7h-4z' => 'car',

        'icon--utensils' => 'utensils',
        'M4 0v7a2 2 0 002 2v11h2V9a2 2 0 002-2V0H8v7H6V0H4z' => 'utensils',

        'icon--camera' => 'camera',
        'M10 5a4 4 0 100 8 4 4 0 000-8z' => 'camera',

        'icon--gift' => 'gift',
        'M18 6h-3.17A3 3 0 0012 2a3 3 0 00-2.83 4H2a2 2 0 00-2 2v2h20V8a2 2 0 00-2-2z' => 'gift',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Converting tour extras icons from SVG to names...');

        $extras = TourExtra::whereNotNull('icon')->get();
        $converted = 0;
        $skipped = 0;

        foreach ($extras as $extra) {
            // If already a simple icon name, skip it
            if (strlen($extra->icon) < 50 && !str_contains($extra->icon, '<svg')) {
                $skipped++;
                continue;
            }

            // Try to detect the icon
            $iconName = $this->detectIcon($extra->icon);

            if ($iconName) {
                $extra->icon = $iconName;
                $extra->save();
                $converted++;
                $this->line("  ✓ Converted: {$extra->name} -> {$iconName}");
            } else {
                $this->warn("  ✗ Could not detect icon for: {$extra->name}");
                $this->line("    Setting to default 'star' icon");
                $extra->icon = 'star';
                $extra->save();
                $converted++;
            }
        }

        $this->info("\nConversion complete!");
        $this->info("  Converted: {$converted}");
        $this->info("  Skipped (already converted): {$skipped}");
    }

    /**
     * Detect icon name from SVG markup
     */
    protected function detectIcon(string $svg): ?string
    {
        foreach ($this->iconMap as $pattern => $iconName) {
            if (str_contains($svg, $pattern)) {
                return $iconName;
            }
        }

        return null;
    }
}
