<?php

namespace App\Filament\Widgets;

use App\Models\TourInquiry;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InquiryStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $totalInquiries = TourInquiry::count();
        $newInquiries = TourInquiry::where('status', 'new')->count();
        $repliedInquiries = TourInquiry::where('status', 'replied')->count();
        $convertedInquiries = TourInquiry::whereNotNull('booking_id')->count();
        $conversionRate = $totalInquiries > 0
            ? round(($convertedInquiries / $totalInquiries) * 100, 1)
            : 0;

        return [
            Stat::make('Total Inquiries', $totalInquiries)
                ->description('All time inquiries')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),

            Stat::make('New Inquiries', $newInquiries)
                ->description('Awaiting response')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color($newInquiries > 0 ? 'danger' : 'success'),

            Stat::make('Replied Inquiries', $repliedInquiries)
                ->description('Responded to')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('info'),

            Stat::make('Conversion Rate', $conversionRate . '%')
                ->description($convertedInquiries . ' converted to bookings')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($conversionRate > 30 ? 'success' : 'warning'),
        ];
    }
}
