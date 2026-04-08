<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Actions\GeneratePaymentLinkAction;
use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Bookings\Widgets\BookingCostWidget;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pay_link_test')
                ->label('Generate Payment Link')
                ->icon('heroicon-o-link')
                ->color('success')
                ->action(fn () => null),
            DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            BookingCostWidget::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }

    public function getFooterWidgetsData(): array
    {
        return [
            'record' => $this->record,
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
