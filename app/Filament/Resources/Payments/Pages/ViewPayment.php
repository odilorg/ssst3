<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_booking')
                ->label('Посмотреть бронирование')
                ->icon('heroicon-o-calendar')
                ->color('primary')
                ->url(fn () => \App\Filament\Resources\Bookings\BookingResource::getUrl('edit', ['record' => $this->record->booking_id])),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function getContentTabLabel(): ?string
    {
        return 'Детали платежа';
    }
}
