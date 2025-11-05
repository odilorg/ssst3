<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_all')
                ->label('Экспорт всех')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    // TODO: Implement export functionality
                    \Filament\Notifications\Notification::make()
                        ->title('Экспорт запущен')
                        ->body('Файл будет готов к скачиванию через несколько секунд')
                        ->success()
                        ->send();
                }),
        ];
    }
}
