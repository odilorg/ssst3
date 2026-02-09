<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class SyncGlobalTranslations extends Command
{
    protected $signature = 'translations:sync-global
                            {--locale= : Specific locale to sync (default: all)}';

    protected $description = 'Sync global FAQs and requirements from language files to Settings table';

    public function handle()
    {
        $locale = $this->option('locale');
        $locales = $locale ? [$locale] : config('multilang.locales', ['en', 'ru', 'fr', 'de', 'ja', 'zh']);

        $this->info('Syncing global translations to Settings table...');

        foreach ($locales as $locale) {
            $this->syncFaqsForLocale($locale);
            $this->syncRequirementsForLocale($locale);
        }

        $this->info('✓ Global translations synced successfully!');
    }

    protected function syncFaqsForLocale(string $locale): void
    {
        // Load FAQ translations from language file
        $faqDefault = __('ui.faq_default', [], $locale);

        // Skip if translations not found or in wrong format
        if (!is_array($faqDefault) || empty($faqDefault)) {
            $this->warn("⚠ Skipping FAQs for {$locale}: translations not found or empty");
            return;
        }

        // Convert to Settings format
        $globalFaqs = [];
        foreach ($faqDefault as $key => $faq) {
            if (isset($faq['question']) && isset($faq['answer'])) {
                $globalFaqs[] = [
                    'question' => $faq['question'],
                    'answer' => $faq['answer'],
                ];
            }
        }

        if (empty($globalFaqs)) {
            $this->warn("⚠ Skipping FAQs for {$locale}: no valid FAQ entries");
            return;
        }

        // Save to Settings table
        $settingKey = $locale === 'en' ? 'global_faqs' : "global_faqs_{$locale}";
        Setting::set($settingKey, $globalFaqs);

        $this->info("✓ Synced {$settingKey} (" . count($globalFaqs) . " FAQs)");
    }

    protected function syncRequirementsForLocale(string $locale): void
    {
        // Load requirement translations from language file
        $requirementsDefault = __('ui.requirements_default', [], $locale);

        // Skip if translations not found or in wrong format
        if (!is_array($requirementsDefault) || empty($requirementsDefault)) {
            $this->warn("⚠ Skipping requirements for {$locale}: translations not found or empty");
            return;
        }

        // Convert to Settings format
        $globalRequirements = [];
        $iconMap = [
            'walking' => 'walking',
            'dress_code' => 'clothing',
            'cash' => 'money',
            'photography' => 'camera',
            'weather' => 'sun',
            'accessibility' => 'wheelchair',
        ];

        foreach ($requirementsDefault as $key => $requirement) {
            if (isset($requirement['title']) && isset($requirement['text'])) {
                $globalRequirements[] = [
                    'icon' => $iconMap[$key] ?? 'info',
                    'title' => $requirement['title'],
                    'text' => $requirement['text'],
                ];
            }
        }

        if (empty($globalRequirements)) {
            $this->warn("⚠ Skipping requirements for {$locale}: no valid requirement entries");
            return;
        }

        // Save to Settings table
        $settingKey = $locale === 'en' ? 'global_requirements' : "global_requirements_{$locale}";
        Setting::set($settingKey, $globalRequirements);

        $this->info("✓ Synced {$settingKey} (" . count($globalRequirements) . " requirements)");
    }
}
