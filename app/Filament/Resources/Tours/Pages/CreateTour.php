<?php

namespace App\Filament\Resources\Tours\Pages;

use App\Filament\Resources\Tours\TourResource;
use App\Filament\Resources\Tours\Schemas\TourForm;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Wizard;
use Filament\Schemas\Schema;
use Filament\Actions\Action;

class CreateTour extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = TourResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getSteps(): array
    {
        return TourForm::getWizardSteps();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
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
