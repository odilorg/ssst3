<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $overdueCount = Lead::overdueFollowup()->count();
        $todayCount = Lead::whereNotNull('next_followup_at')
            ->whereDate('next_followup_at', today())
            ->count();
        $thisWeekCount = Lead::whereNotNull('next_followup_at')
            ->where('next_followup_at', '>=', now())
            ->where('next_followup_at', '<=', now()->endOfWeek())
            ->count();
        $activeLeadsCount = Lead::active()->count();

        return [
            Stat::make('Overdue Follow-ups', $overdueCount)
                ->description('Leads waiting for follow-up')
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color($overdueCount > 0 ? 'danger' : 'success')
                ->url(route('filament.admin.resources.leads.index', ['tableFilters' => ['overdue_followup' => ['isActive' => true]]])),

            Stat::make('Due Today', $todayCount)
                ->description('Follow-ups scheduled for today')
                ->descriptionIcon('heroicon-o-clock')
                ->color($todayCount > 0 ? 'warning' : 'gray'),

            Stat::make('This Week', $thisWeekCount)
                ->description('Upcoming follow-ups')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('primary'),

            Stat::make('Active Leads', $activeLeadsCount)
                ->description('Total leads in pipeline')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('success')
                ->url(route('filament.admin.resources.leads.index', ['tableFilters' => ['active' => ['isActive' => true]]])),
        ];
    }
}
