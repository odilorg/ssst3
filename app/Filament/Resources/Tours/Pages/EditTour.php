<?php

namespace App\Filament\Resources\Tours\Pages;

use App\Filament\Resources\Tours\TourResource;
use App\Filament\Resources\Tours\Schemas\TourForm;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Log;

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
        Log::info('=== SAVE METHOD CALLED ===');
        $data = $this->form->getState();
        Log::info('Form data received', ['keys' => array_keys($data)]);
        
        // IMPORTANT: Call mutation method to transform locale fields into JSON
        $data = $this->mutateFormDataBeforeSave($data);
        Log::info('After mutation', ['title' => $data['title'] ?? 'NOT SET']);

        $this->handleRecordUpdate($this->record, $data);
        Log::info('Record updated', ['tour_id' => $this->record->id]);

        if ($shouldSendSavedNotification) {
            $this->getSavedNotification()->send();
        }

        if ($shouldRedirect) {
            $this->redirect($this->getRedirectUrl());
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        Log::info('=== MUTATION STARTED ===');
        Log::info('Received data keys:', array_keys($data));
        
        // Check for title fields specifically
        if (isset($data['title_en'])) {
            Log::info('Found title_en:', [$data['title_en']]);
        }
        if (isset($data['title_ru'])) {
            Log::info('Found title_ru:', [$data['title_ru']]);
        }
        if (isset($data['title_uz'])) {
            Log::info('Found title_uz:', [$data['title_uz']]);
        }
        
        // Collect locale-specific fields into JSON structure
        $translatableFields = ['title', 'short_description', 'long_description',
            'seo_title', 'seo_description', 'seo_keywords',
            'highlights', 'included_items', 'excluded_items'];

        foreach ($translatableFields as $field) {
            if (isset($data[$field . '_en']) || isset($data[$field . '_ru']) || isset($data[$field . '_uz'])) {
                $translations = [
                    'en' => $data[$field . '_en'] ?? '',
                    'ru' => $data[$field . '_ru'] ?? '',
                    'uz' => $data[$field . '_uz'] ?? '',
                ];
                
                Log::info("Creating {$field} translations:", $translations);
                $data[$field] = $translations;
                
                // Remove the temporary locale fields
                unset($data[$field . '_en']);
                unset($data[$field . '_ru']);
                unset($data[$field . '_uz']);
            }
        }
        
        Log::info('=== MUTATION COMPLETE ===');
        if (isset($data['title'])) {
            Log::info('Final title value:', $data['title']);
        }

        return $data;
    }

    public function hasSkippableSteps(): bool
    {
        return true;
    }
}
