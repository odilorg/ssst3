<?php

namespace App\Filament\Pages;

use App\Models\CompanySetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

class CompanySettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    protected string $view = 'filament.pages.company-settings';
    protected static ?string $navigationLabel = 'Company Settings';
    protected static ?string $title = 'Company Settings';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public function mount(): void
    {
        $settings = CompanySetting::getOrCreate();
        $this->form->fill($settings->toArray());
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Basic Information')->schema([
                TextInput::make('company_name')->required(),
                TextInput::make('legal_name'),
                TextInput::make('tax_id')->label('Tax ID'),
                TextInput::make('email')->email(),
                TextInput::make('phone'),
                TextInput::make('website')->url(),
            ])->columns(2),
            
            Section::make('Address')->schema([
                Textarea::make('office_address')->rows(2),
                TextInput::make('city'),
                TextInput::make('country'),
            ])->columns(2),
            
            Section::make('Banking')->schema([
                Repeater::make('bank_accounts')->schema([
                    TextInput::make('bank_name')->required(),
                    TextInput::make('account_number')->required(),
                    TextInput::make('swift_code'),
                ])->columns(2)->defaultItems(0),
            ]),
        ];
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $settings = CompanySetting::getOrCreate();
        $settings->update($data);
        
        Notification::make()
            ->success()
            ->title('Settings Saved')
            ->body('Company settings have been updated successfully.')
            ->send();
    }
}
