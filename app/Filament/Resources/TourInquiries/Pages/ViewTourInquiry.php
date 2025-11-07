<?php

namespace App\Filament\Resources\TourInquiries\Pages;

use App\Filament\Resources\TourInquiries\TourInquiryResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTourInquiry extends ViewRecord
{
    protected static string $resource = TourInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mark_replied')
                ->label('Mark as Replied')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('success')
                ->visible(fn () => $this->record->status === 'new')
                ->requiresConfirmation()
                ->modalHeading('Mark Inquiry as Replied')
                ->modalDescription('This will mark the inquiry as replied and record your user ID.')
                ->action(function () {
                    $this->record->markAsReplied(auth()->user());
                    Notification::make()
                        ->title('Inquiry marked as replied')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'replied_at', 'replied_by']);
                }),

            Action::make('close')
                ->label('Close Inquiry')
                ->icon('heroicon-o-x-circle')
                ->color('warning')
                ->visible(fn () => in_array($this->record->status, ['new', 'replied']))
                ->requiresConfirmation()
                ->modalHeading('Close Inquiry')
                ->modalDescription('Are you sure you want to close this inquiry?')
                ->action(function () {
                    $this->record->status = 'closed';
                    $this->record->save();
                    Notification::make()
                        ->title('Inquiry closed')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
