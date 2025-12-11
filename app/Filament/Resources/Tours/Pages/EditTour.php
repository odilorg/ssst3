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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Expand JSON translations to separate locale fields
        $translatableFields = [
            'title', 'short_description', 'long_description',
            'seo_title', 'seo_description', 'seo_keywords',
            'highlights', 'included_items', 'excluded_items'
        ];

        foreach ($translatableFields as $field) {
            if (isset($data[$field])) {
                // Get translations from the model
                $translations = is_string($data[$field]) 
                    ? json_decode($data[$field], true) 
                    : $data[$field];

                // If it's still a string, treat as English
                if (is_string($translations)) {
                    $data[$field] = [
                        'en' => $translations,
                        'ru' => '',
                        'uz' => '',
                    ];
                } elseif (is_array($translations)) {
                    $data[$field] = [
                        'en' => $translations['en'] ?? '',
                        'ru' => $translations['ru'] ?? '',
                        'uz' => $translations['uz'] ?? '',
                    ];
                }
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Data is already in correct format from TranslatableField
        // Spatie will handle the JSON conversion automatically
        return $data;
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $data = $this->form->getState();

        $this->handleRecordUpdate($this->record, $data);

        if ($shouldSendSavedNotification) {
            $this->getSavedNotification()->send();
        }

        if ($shouldRedirect) {
            $this->redirect($this->getRedirectUrl());
        }
    }

    public function hasSkippableSteps(): bool
    {
        return true;
    }
}
