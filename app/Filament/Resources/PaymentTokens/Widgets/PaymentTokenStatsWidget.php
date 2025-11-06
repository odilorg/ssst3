<?php

namespace App\Filament\Resources\PaymentTokens\Widgets;

use App\Models\PaymentToken;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentTokenStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $activeTokens = PaymentToken::where('expires_at', '>', now())
            ->whereNull('used_at')
            ->count();

        $expiringSoon = PaymentToken::where('expires_at', '>', now())
            ->where('expires_at', '<', now()->addDay())
            ->whereNull('used_at')
            ->count();

        $usedToday = PaymentToken::whereNotNull('used_at')
            ->whereDate('used_at', now())
            ->count();

        $expiredToday = PaymentToken::where('expires_at', '>=', now()->startOfDay())
            ->where('expires_at', '<', now())
            ->count();

        $totalTokens = PaymentToken::count();
        $usedTokens = PaymentToken::whereNotNull('used_at')->count();
        $usageRate = $totalTokens > 0 ? round(($usedTokens / $totalTokens) * 100, 1) : 0;

        return [
            Stat::make('Active Tokens', $activeTokens)
                ->description('Valid & not expired')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart([7, 5, 10, 8, 9, 12, $activeTokens]),

            Stat::make('Expiring Soon', $expiringSoon)
                ->description('Within 24 hours')
                ->descriptionIcon('heroicon-o-clock')
                ->color($expiringSoon > 0 ? 'warning' : 'success'),

            Stat::make('Used Today', $usedToday)
                ->description('Tokens used today')
                ->descriptionIcon('heroicon-o-check')
                ->color('info'),

            Stat::make('Usage Rate', $usageRate . '%')
                ->description('Overall conversion rate')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color($usageRate > 70 ? 'success' : ($usageRate > 40 ? 'warning' : 'danger')),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
