<?php

namespace App\Filament\Widgets;

use App\Models\TourCategory;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CategoryStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $totalCategories = TourCategory::count();
        $activeCategories = TourCategory::active()->count();
        $homepageCategories = TourCategory::active()->where('show_on_homepage', true)->count();
        $emptyCategories = TourCategory::active()
            ->doesntHave('tours')
            ->count();

        // Calculate total tours across all categories (with deduplication)
        $totalCategorizedTours = TourCategory::active()
            ->withCount(['tours' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->sum('tours_count');

        return [
            Stat::make('Active Categories', $activeCategories)
                ->description("{$totalCategories} total categories")
                ->descriptionIcon('heroicon-o-rectangle-stack')
                ->color('success')
                ->url(route('filament.admin.resources.tour-categories.index')),

            Stat::make('Homepage Categories', $homepageCategories)
                ->description('Shown on homepage section')
                ->descriptionIcon('heroicon-o-home')
                ->color($homepageCategories > 6 ? 'warning' : 'primary')
                ->extraAttributes([
                    'title' => $homepageCategories > 6 ? 'Warning: More than 6 categories on homepage' : '',
                ]),

            Stat::make('Categorized Tours', $totalCategorizedTours)
                ->description('Tours with categories assigned')
                ->descriptionIcon('heroicon-o-map')
                ->color('info'),

            Stat::make('Empty Categories', $emptyCategories)
                ->description('Categories without tours')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($emptyCategories > 0 ? 'warning' : 'success'),
        ];
    }
}
