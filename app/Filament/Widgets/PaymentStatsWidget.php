<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Today's statistics
        $todayRevenue = Payment::where('status', 'completed')
            ->whereDate('processed_at', now())
            ->sum('amount');

        $todayPayments = Payment::where('status', 'completed')
            ->whereDate('processed_at', now())
            ->count();

        // Pending payments
        $pendingPayments = Payment::where('status', 'pending')->count();
        $pendingAmount = Payment::where('status', 'pending')->sum('amount');

        // This month statistics
        $monthRevenue = Payment::where('status', 'completed')
            ->whereMonth('processed_at', now()->month)
            ->whereYear('processed_at', now()->year)
            ->sum('amount');

        $lastMonthRevenue = Payment::where('status', 'completed')
            ->whereMonth('processed_at', now()->subMonth()->month)
            ->whereYear('processed_at', now()->subMonth()->year)
            ->sum('amount');

        $revenueChange = $lastMonthRevenue > 0
            ? round((($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        // Success rate
        $totalProcessed = Payment::whereIn('status', ['completed', 'failed'])
            ->whereMonth('created_at', now()->month)
            ->count();

        $completedCount = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->count();

        $successRate = $totalProcessed > 0
            ? round(($completedCount / $totalProcessed) * 100, 1)
            : 0;

        return [
            Stat::make('Today\'s Revenue', '$' . number_format($todayRevenue, 2))
                ->description($todayPayments . ' payments completed')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart([100, 150, 200, 180, 220, 250, $todayRevenue]),

            Stat::make('Pending Payments', $pendingPayments)
                ->description('Total: $' . number_format($pendingAmount, 2))
                ->descriptionIcon('heroicon-o-clock')
                ->color($pendingPayments > 10 ? 'warning' : 'info')
                ->url(route('filament.admin.resources.payments.index', ['tableFilters[status][values][0]' => 'pending'])),

            Stat::make('This Month', '$' . number_format($monthRevenue, 2))
                ->description(($revenueChange >= 0 ? '+' : '') . $revenueChange . '% from last month')
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger'),

            Stat::make('Success Rate', $successRate . '%')
                ->description('This month')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color($successRate >= 90 ? 'success' : ($successRate >= 75 ? 'warning' : 'danger')),
        ];
    }
}
