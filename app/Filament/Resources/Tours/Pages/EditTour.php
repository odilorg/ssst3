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

    protected function fillForm(): void
    {
        // Get the base data
        $data = $this->record->attributesToArray();
        
        // Manually expand translatable fields
        $translatableFields = [
            'title', 'short_description', 'long_description',
            'seo_title', 'seo_description', 'seo_keywords',
            'highlights', 'included_items', 'excluded_items'
        ];

        foreach ($translatableFields as $field) {
            if (isset($data[$field])) {
                // Get raw translations
                $translations = $this->record->getTranslations($field);
                
                // Remove the original field
                unset($data[$field]);
                
                // Add locale-specific fields
                $data[$field . '.en'] = $translations['en'] ?? '';
                $data[$field . '.ru'] = $translations['ru'] ?? '';
                $data[$field . '.uz'] = $translations['uz'] ?? '';
            }
        }

        $this->form->fill($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Collapse locale-specific fields back to arrays
        $translatableFields = [
            'title', 'short_description', 'long_description',
            'seo_title', 'seo_description', 'seo_keywords',
            'highlights', 'included_items', 'excluded_items'
        ];

        foreach ($translatableFields as $field) {
            $data[$field] = [
                'en' => $data[$field . '.en'] ?? '',
                'ru' => $data[$field . '.ru'] ?? '',
                'uz' => $data[$field . '.uz'] ?? '',
            ];
            
            // Remove the locale-specific fields
            unset($data[$field . '.en']);
            unset($data[$field . '.ru']);
            unset($data[$field . '.uz']);
        }

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
