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
    use \LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

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
