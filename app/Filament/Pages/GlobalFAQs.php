<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class GlobalFAQs extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-question-mark-circle';
    protected string $view = 'filament.pages.global-faqs';
    protected static ?string $navigationLabel = 'Global FAQs';
    protected static ?string $title = 'Global FAQs';

    protected static ?int $navigationSort = 99;

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public function mount(): void
    {
        $faqs = Setting::get('global_faqs', []);
        $this->form->fill(['faqs' => $faqs]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Repeater::make('faqs')
                    ->label('Global FAQs')
                    ->schema([
                        Textarea::make('question')
                            ->label('Question')
                            ->required()
                            ->rows(2)
                            ->placeholder('What should I bring?')
                            ->columnSpanFull(),

                        Textarea::make('answer')
                            ->label('Answer')
                            ->required()
                            ->rows(4)
                            ->placeholder('Comfortable walking shoes, sun protection...')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->itemLabel(fn (array $state): ?string => $state['question'] ?? 'New FAQ')
                    ->addActionLabel('Add FAQ')
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
            'global_faqs',
            $data['faqs'] ?? [],
            'json',
            'faqs'
        );

        Notification::make()
            ->success()
            ->title('Settings Saved')
            ->body('Global FAQs have been updated successfully.')
            ->send();
    }
}
