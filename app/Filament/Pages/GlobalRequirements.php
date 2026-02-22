<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Tours\Schemas\TourForm;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class GlobalRequirements extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected string $view = 'filament.pages.global-requirements';
    protected static ?string $navigationLabel = 'Global Requirements';
    protected static ?string $title = 'Global Requirements';

    protected static ?int $navigationSort = 98;

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public function mount(): void
    {
        $requirements = Setting::get('global_requirements', []);
        if (is_string($requirements)) {
            $requirements = json_decode($requirements, true) ?? [];
        }
        $this->form->fill(['requirements' => is_array($requirements) ? $requirements : []]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Repeater::make('requirements')
                    ->schema([
                        Select::make('icon')
                            ->label('Icon')
                            ->options(TourForm::getRequirementIconOptions())
                            ->required()
                            ->searchable()
                            ->columnSpanFull(),

                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Moderate walking required')
                            ->columnSpanFull(),

                        Textarea::make('text')
                            ->label('Description')
                            ->required()
                            ->rows(3)
                            ->placeholder('Detailed description of the requirement...')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'New Requirement')
                    ->addActionLabel('Add Requirement')
                    ->reorderable()
                    ->cloneable()
                    ->defaultItems(0)
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        Setting::set(
            'global_requirements',
            $data['requirements'] ?? [],
            'json',
            'requirements'
        );

        Notification::make()
            ->success()
            ->title('Settings Saved')
            ->body('Global requirements have been updated successfully.')
            ->send();
    }
}
