<?php

namespace App\Filament\Resources\Contacts\Pages;

use App\Filament\Resources\Contacts\ContactResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContact extends ViewRecord
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('markAsReplied')
                ->label('Mark as Replied')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->visible(fn (): bool => $this->record->status !== 'replied')
                ->requiresConfirmation()
                ->action(fn () => $this->record->markAsReplied(auth()->user()))
                ->after(fn () => $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]))),

            Actions\Action::make('close')
                ->label('Close')
                ->icon('heroicon-m-x-circle')
                ->color('gray')
                ->visible(fn (): bool => $this->record->status !== 'closed')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['status' => 'closed']);
                })
                ->after(fn () => $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]))),

            Actions\Action::make('replyViaEmail')
                ->label('Reply via Email')
                ->icon('heroicon-m-envelope')
                ->color('primary')
                ->url(fn (): string => 'mailto:' . $this->record->email . '?subject=Re: ' . urlencode($this->record->reference))
                ->openUrlInNewTab(),

            Actions\DeleteAction::make(),
        ];
    }
}
