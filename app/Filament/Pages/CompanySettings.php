<?php

namespace App\Filament\Pages;

use App\Models\CompanySetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Company Name')
                            ->required(),
                        TextInput::make('legal_name')
                            ->label('Legal Name'),
                        TextInput::make('tax_id')
                            ->label('Tax ID / VAT Number'),
                        TextInput::make('email')
                            ->label('Email')
                            ->email(),
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel(),
                        TextInput::make('website')
                            ->label('Website')
                            ->url(),
                    ])
                    ->columns(2),

                Section::make('Address Information')
                    ->schema([
                        Textarea::make('office_address')
                            ->label('Office Address')
                            ->rows(2),
                        TextInput::make('city')
                            ->label('City'),
                        TextInput::make('country')
                            ->label('Country'),
                        TextInput::make('postal_code')
                            ->label('Postal Code'),
                    ])
                    ->columns(2),

                Section::make('Banking Information')
                    ->description('Add your bank account details for invoices and payments')
                    ->schema([
                        Repeater::make('bank_accounts')
                            ->label('Bank Accounts')
                            ->schema([
                                TextInput::make('bank_name')
                                    ->label('Bank Name')
                                    ->required(),
                                TextInput::make('account_number')
                                    ->label('Account Number')
                                    ->required(),
                                TextInput::make('swift_code')
                                    ->label('SWIFT/BIC Code'),
                                TextInput::make('currency')
                                    ->label('Currency')
                                    ->default('USD'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['bank_name'] ?? 'New Bank Account'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
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
