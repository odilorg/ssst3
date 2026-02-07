<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\CityTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Seed City Translations Command
 *
 * Creates default locale translations for all cities by copying
 * existing City fields into the city_translations table.
 *
 * Usage:
 *   php artisan multilang:seed-city-translations
 *   php artisan multilang:seed-city-translations --force
 */
class MultilangSeedCityTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'multilang:seed-city-translations
                            {--force : Overwrite existing translations for default locale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed city_translations table with default locale data from existing cities';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $defaultLocale = config('multilang.default_locale', 'en');
        $force = $this->option('force');

        $this->info("Seeding city translations for default locale: {$defaultLocale}");
        $this->newLine();

        // Get all cities
        $cities = City::all();

        if ($cities->isEmpty()) {
            $this->warn('No cities found in database.');
            return Command::SUCCESS;
        }

        $this->info("Found {$cities->count()} cities to process.");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($cities->count());
        $progressBar->start();

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        DB::beginTransaction();

        try {
            foreach ($cities as $city) {
                $result = $this->processCity($city, $defaultLocale, $force);

                switch ($result) {
                    case 'created':
                        $created++;
                        break;
                    case 'updated':
                        $updated++;
                        break;
                    case 'skipped':
                        $skipped++;
                        break;
                    case 'error':
                        $errors++;
                        break;
                }

                $progressBar->advance();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->newLine(2);
            $this->error("Transaction failed: {$e->getMessage()}");
            return Command::FAILURE;
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary table
        $this->info('Seeding complete!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Created', $created],
                ['Updated', $updated],
                ['Skipped', $skipped],
                ['Errors', $errors],
                ['Total', $cities->count()],
            ]
        );

        if ($skipped > 0 && !$force) {
            $this->newLine();
            $this->comment('Use --force to overwrite existing translations.');
        }

        return Command::SUCCESS;
    }

    /**
     * Process a single city and create/update its translation.
     */
    private function processCity(City $city, string $locale, bool $force): string
    {
        try {
            // Check if translation already exists
            $existing = CityTranslation::where('city_id', $city->id)
                ->where('locale', $locale)
                ->first();

            if ($existing && !$force) {
                return 'skipped';
            }

            // Prepare translation data with defensive field mapping
            $data = $this->mapCityToTranslation($city, $locale);

            if ($existing) {
                // Update existing translation
                $existing->update($data);
                return 'updated';
            }

            // Create new translation
            CityTranslation::create($data);
            return 'created';

        } catch (\Exception $e) {
            $this->newLine();
            $this->warn("Error processing city {$city->id}: {$e->getMessage()}");
            return 'error';
        }
    }

    /**
     * Map City fields to CityTranslation fields defensively.
     */
    private function mapCityToTranslation(City $city, string $locale): array
    {
        // Defensive name mapping
        $name = $this->getFieldValue($city, ['name', 'title'], 'Unnamed City');

        // Defensive slug mapping
        $slug = $this->getFieldValue($city, ['slug'], null);
        if (empty($slug)) {
            $slug = \Illuminate\Support\Str::slug($name);
        }

        // Map tagline
        $tagline = $this->getFieldValue($city, ['tagline'], null);

        // Map short_description
        $shortDescription = $this->getFieldValue($city, ['short_description', 'excerpt'], null);

        // Map description: try description, long_description, or null
        $description = $this->getFieldValue($city, ['description', 'long_description', 'content'], null);

        // Map SEO fields defensively
        $seoTitle = $this->getFieldValue($city, ['meta_title', 'seo_title'], null);
        $seoDescription = $this->getFieldValue($city, ['meta_description', 'seo_description'], null);

        return [
            'city_id' => $city->id,
            'locale' => $locale,
            'name' => $name,
            'slug' => $slug,
            'tagline' => $tagline,
            'short_description' => $shortDescription,
            'description' => $description,
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
        ];
    }

    /**
     * Get field value from model, trying multiple possible field names.
     */
    private function getFieldValue(City $city, array $fieldNames, mixed $default = null): mixed
    {
        foreach ($fieldNames as $fieldName) {
            if (isset($city->{$fieldName}) && !empty($city->{$fieldName})) {
                return $city->{$fieldName};
            }

            if (isset($city->getAttributes()[$fieldName]) && !empty($city->getAttributes()[$fieldName])) {
                return $city->getAttributes()[$fieldName];
            }
        }

        return $default;
    }
}
