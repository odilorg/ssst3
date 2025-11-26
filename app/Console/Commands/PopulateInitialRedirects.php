<?php

namespace App\Console\Commands;

use App\Models\Redirect;
use Illuminate\Console\Command;

class PopulateInitialRedirects extends Command
{
    protected $signature = 'redirects:populate-initial';
    protected $description = 'Populate initial redirects for deleted tours and WordPress migration';

    public function handle()
    {
        $this->info('📋 Populating initial redirects...');
        $this->newLine();

        $redirects = [
            // Deleted duplicate tours
            [
                'old_path' => '/tours/kyrgyzstan-nomadic-adventure-song-kul-lake',
                'new_path' => '/tours/kyrgyzstan-nomadic-adventure-song-kul-tian-shan',
                'notes' => 'Deleted duplicate Kyrgyzstan tour - merged into main tour',
            ],
            [
                'old_path' => '/tours/seven-lakes-day-tour',
                'new_path' => '/tours/tajikistan-seven-lakes-marguzor-sarazm-unesco-samarkand',
                'notes' => 'Deleted duplicate Seven Lakes tour - redirecting to complete version',
            ],
            [
                'old_path' => '/tours/samarkand-city-tour-registan-square-and-historical-monuments',
                'new_path' => '/tours/samarkand-heritage-full-day-unesco-explorer',
                'notes' => 'Deleted duplicate Samarkand city tour - merged into UNESCO Explorer',
            ],
            [
                'old_path' => '/tours/samarkand-history-tour',
                'new_path' => '/tours/samarkand-heritage-full-day-unesco-explorer',
                'notes' => 'Deleted incomplete WordPress import - redirecting to best Samarkand tour',
            ],

            // WordPress multilingual paths to new structure
            // Handle /en/ prefix
            [
                'old_path' => '/en',
                'new_path' => '/',
                'notes' => 'WordPress English homepage redirect',
            ],
            [
                'old_path' => '/en/tours',
                'new_path' => '/tours',
                'notes' => 'WordPress tours listing redirect',
            ],
            [
                'old_path' => '/en/insight',
                'new_path' => '/blog',
                'notes' => 'WordPress insights listing redirect',
            ],

            // Other language prefixes (redirect to English homepage)
            [
                'old_path' => '/ja',
                'new_path' => '/',
                'notes' => 'Japanese language redirect (no longer supported)',
            ],
            [
                'old_path' => '/it',
                'new_path' => '/',
                'notes' => 'Italian language redirect (no longer supported)',
            ],
            [
                'old_path' => '/fr',
                'new_path' => '/',
                'notes' => 'French language redirect (no longer supported)',
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($redirects as $redirectData) {
            // Check if redirect already exists
            $existing = Redirect::where('old_path', $redirectData['old_path'])->first();

            if ($existing) {
                $this->warn("Skipped (exists): {$redirectData['old_path']}");
                $skipped++;
                continue;
            }

            // Create redirect
            Redirect::create([
                'old_path' => $redirectData['old_path'],
                'new_path' => $redirectData['new_path'],
                'status_code' => 301,
                'is_active' => true,
                'notes' => $redirectData['notes'],
            ]);

            $this->info("✅ Created: {$redirectData['old_path']} → {$redirectData['new_path']}");
            $created++;
        }

        $this->newLine();
        $this->info("✅ Redirect population complete!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Created', $created],
                ['Skipped (already exist)', $skipped],
                ['Total', count($redirects)],
            ]
        );

        $this->newLine();
        $this->comment('💡 Next steps:');
        $this->comment('1. Visit /admin/redirects to manage redirects');
        $this->comment('2. Add more specific redirects as needed');
        $this->comment('3. Monitor redirect hits in the admin panel');

        return Command::SUCCESS;
    }
}
