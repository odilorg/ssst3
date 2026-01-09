<?php

namespace App\Filament\Resources\Tours\Pages;

use App\Filament\Resources\Tours\TourResource;
use App\Filament\Resources\Tours\Schemas\TourForm;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;

class EditTour extends EditRecord
{
    use EditRecord\Concerns\HasWizard;

    protected static string $resource = TourResource::class;
    
    public bool $autoSaveEnabled = true;
    public int $autoSaveInterval = 60; // seconds

    protected function getHeaderActions(): array
    {
        return [
            Action::make('quick_save')
                ->label('Save')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->action(function () {
                    $this->save();
                    Notification::make()
                        ->title('Saved successfully')
                        ->success()
                        ->duration(2000)
                        ->send();
                })
                ->keyBindings(['mod+s'])
                ->extraAttributes(['title' => 'Ctrl+S to save'])
                ->requiresConfirmation(false),
            Action::make('toggle_autosave')
                ->label(fn () => $this->autoSaveEnabled ? 'Auto-save: ON' : 'Auto-save: OFF')
                ->icon(fn () => $this->autoSaveEnabled ? Heroicon::OutlinedClock : Heroicon::OutlinedNoSymbol)
                ->color(fn () => $this->autoSaveEnabled ? 'gray' : 'warning')
                ->action(function () {
                    $this->autoSaveEnabled = !$this->autoSaveEnabled;
                    Notification::make()
                        ->title($this->autoSaveEnabled ? 'Auto-save enabled (60s)' : 'Auto-save disabled')
                        ->success()
                        ->duration(2000)
                        ->send();
                }),
            Action::make('view_frontend')
                ->label('View Frontend')
                ->icon(Heroicon::OutlinedEye)
                ->color('info')
                ->url(fn () => '/'.'tours/' . $this->record->slug)
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

    public function hasSkippableSteps(): bool
    {
        return true;
    }
    
    #[On('auto-save')]
    public function autoSave(): void
    {
        if ($this->autoSaveEnabled) {
            $this->save(shouldRedirect: false);
            Notification::make()
                ->title('Auto-saved')
                ->icon('heroicon-o-clock')
                ->iconColor('success')
                ->duration(2000)
                ->send();
        }
    }

    protected function getContentFooter(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.pages.tour-edit-autosave', [
            'autoSaveEnabled' => $this->autoSaveEnabled,
            'autoSaveInterval' => $this->autoSaveInterval,
        ]);
    }
}
