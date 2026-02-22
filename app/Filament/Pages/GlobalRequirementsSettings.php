<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Tours\Schemas\TourForm;
use App\Models\Setting;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class GlobalRequirementsSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.global-requirements-settings';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-clipboard-document-list';
    }

    public static function getNavigationLabel(): string
    {
        return 'Global Requirements';
    }

    public function getTitle(): string
    {
        return 'Global Requirements Settings';
    }

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public function mount(): void
    {
        $this->form->fill([
            'requirements_en' => $this->ensureArray(Setting::get('global_requirements')),
            'requirements_ru' => $this->ensureArray(Setting::get('global_requirements_ru')),
            'requirements_uz' => $this->ensureArray(Setting::get('global_requirements_uz')),
        ]);
    }

    private function ensureArray(mixed $value): array
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }

        return is_array($value) ? $value : [];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('English (Default)')
                    ->description('Default global requirements shown on all tours')
                    ->schema([
                        Repeater::make('requirements_en')
                            ->label('Requirements')
                            ->schema([
                                Select::make('icon')
                                    ->label('Icon')
                                    ->options(TourForm::getRequirementIconOptions())
                                    ->searchable()
                                    ->required(),
                                TextInput::make('title')
                                    ->label('Title')
                                    ->required(),
                                Textarea::make('text')
                                    ->label('Description')
                                    ->rows(3)
                                    ->required(),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->defaultItems(0),
                    ]),

                Section::make('Russian Translation')
                    ->description('Russian version of global requirements')
                    ->schema([
                        Repeater::make('requirements_ru')
                            ->label('Требования (Russian)')
                            ->schema([
                                Select::make('icon')
                                    ->label('Иконка')
                                    ->options(TourForm::getRequirementIconOptions())
                                    ->searchable()
                                    ->required(),
                                TextInput::make('title')
                                    ->label('Заголовок')
                                    ->required(),
                                Textarea::make('text')
                                    ->label('Описание')
                                    ->rows(3)
                                    ->required(),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->defaultItems(0),
                    ]),

                Section::make('Uzbek Translation')
                    ->description('Uzbek version of global requirements')
                    ->schema([
                        Repeater::make('requirements_uz')
                            ->label('Talablar (Uzbek)')
                            ->schema([
                                Select::make('icon')
                                    ->label('Belgi')
                                    ->options(TourForm::getRequirementIconOptions())
                                    ->searchable()
                                    ->required(),
                                TextInput::make('title')
                                    ->label('Sarlavha')
                                    ->required(),
                                Textarea::make('text')
                                    ->label('Tavsif')
                                    ->rows(3)
                                    ->required(),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->defaultItems(0),
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

        Setting::set('global_requirements', $data['requirements_en']);
        Setting::set('global_requirements_ru', $data['requirements_ru']);
        Setting::set('global_requirements_uz', $data['requirements_uz']);

        Notification::make()
            ->title('Settings Saved')
            ->body('Global requirements have been updated successfully.')
            ->success()
            ->send();
    }
}
