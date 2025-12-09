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

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            Action::make('save_and_exit')
                ->label('Save & Exit')
                ->action('saveAndExit')
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }

    public function saveAndExit(): void
    {
        $data = $this->form->getState();

        $this->record = $this->handleRecordCreation($data);

        $this->redirect($this->getRedirectUrl());
    }

    public function hasSkippableSteps(): bool
    {
        return true;
    }
}
