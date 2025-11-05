<?php

namespace App\Filament\Resources\TourDepartures\Pages;

use App\Filament\Resources\TourDepartures\TourDepartureResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditTourDeparture extends EditRecord
{
    protected static string $resource = TourDepartureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_bookings')
                ->label('Посмотреть брони')
                ->icon('heroicon-o-ticket')
                ->color('info')
                ->visible(fn () => $this->record->bookings()->count() > 0)
                ->url(fn () => \App\Filament\Resources\Bookings\BookingResource::getUrl('index', [
                    'tableFilters' => [
                        'departure_id' => ['value' => $this->record->id],
                    ],
                ])),

            DeleteAction::make()
                ->visible(fn () => $this->record->bookings()->count() === 0),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
