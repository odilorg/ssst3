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
    use \LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

    protected static string $resource = TourResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getSteps(): array
    {
        return TourForm::getWizardSteps();
    }

    public function hasSkippableSteps(): bool
    {
        return true;
    }
}
