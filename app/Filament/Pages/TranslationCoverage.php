<?php

namespace App\Filament\Pages;

use App\Services\TranslationCoverageService;
use BackedEnum;
use Filament\Pages\Page;

/**
 * Translation Coverage Report Page
 *
 * Shows translation coverage statistics for tours, cities, and blog posts.
 * Displays missing translations per locale and provides links to edit them.
 */
class TranslationCoverage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-language';
    protected string $view = 'filament.pages.translation-coverage';
    protected static ?string $navigationLabel = 'Translation Coverage';
    protected static ?string $title = 'Translation Coverage Report';
    protected static ?int $navigationSort = 97;

    public array $report = [];
    public array $localeNames = [];
    public array $locales = [];
    public string $selectedLocale = '';
    public string $selectedType = 'all';

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    /**
     * Only show this page when multilang is enabled.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return config('multilang.enabled', false);
    }

    public function mount(): void
    {
        $service = app(TranslationCoverageService::class);

        $this->locales = $service->getSupportedLocales();
        $this->localeNames = $service->getLocaleNames();
        $this->selectedLocale = $this->locales[0] ?? 'en';
        $this->report = $service->getFullReport();
    }

    /**
     * Refresh the report data.
     */
    public function refreshReport(): void
    {
        $service = app(TranslationCoverageService::class);
        $this->report = $service->getFullReport();
    }

    /**
     * Get coverage percentage color class.
     */
    public function getCoverageColor(float $percentage): string
    {
        if ($percentage >= 100) {
            return 'text-success-600 dark:text-success-400';
        }
        if ($percentage >= 75) {
            return 'text-warning-600 dark:text-warning-400';
        }
        return 'text-danger-600 dark:text-danger-400';
    }

    /**
     * Get coverage badge color.
     */
    public function getBadgeColor(float $percentage): string
    {
        if ($percentage >= 100) {
            return 'success';
        }
        if ($percentage >= 75) {
            return 'warning';
        }
        return 'danger';
    }
}
