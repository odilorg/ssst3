<?php

namespace App\Filament\Resources\Tours\Pages;

use App\Filament\Resources\Tours\TourResource;
use App\Filament\Resources\Tours\Schemas\TourForm;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditTour extends EditRecord
{
    use EditRecord\Concerns\HasWizard;

    protected static string $resource = TourResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->action(function () {
                    $this->save(shouldRedirect: false);
                })
                ->color('primary')
                ->icon('heroicon-o-check'),
            Action::make('view_frontend')
                ->label('View Frontend')
                ->icon(Heroicon::OutlinedEye)
                ->color('info')
                ->url(fn () => '/tours/' . $this->record->slug)
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getSteps(): array
    {
        return TourForm::getWizardSteps();
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $data = $this->form->getState();
        
        // IMPORTANT: Call mutation method to transform locale fields into JSON
        $data = $this->mutateFormDataBeforeSave($data);

        $this->handleRecordUpdate($this->record, $data);

        if ($shouldSendSavedNotification) {
            $this->getSavedNotification()->send();
        }

        if ($shouldRedirect) {
            $this->redirect($this->getRedirectUrl());
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Collect locale-specific fields into JSON structure
        $translatableFields = ['title', 'short_description', 'long_description',
            'seo_title', 'seo_description', 'seo_keywords',
            'highlights', 'included_items', 'excluded_items'];

        foreach ($translatableFields as $field) {
            if (isset($data[$field . '_en']) || isset($data[$field . '_ru']) || isset($data[$field . '_uz'])) {
                $data[$field] = [
                    'en' => $data[$field . '_en'] ?? '',
                    'ru' => $data[$field . '_ru'] ?? '',
                    'uz' => $data[$field . '_uz'] ?? '',
                ];
                
                // Remove the temporary locale fields
                unset($data[$field . '_en']);
                unset($data[$field . '_ru']);
                unset($data[$field . '_uz']);
            }
        }

        return $data;
    }

    public function hasSkippableSteps(): bool
    {
        return true;
    }
}
