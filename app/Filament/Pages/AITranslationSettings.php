<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Models\TranslationLog;
use App\Services\OpenAI\TranslationService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Log;

class AITranslationSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-language';
    protected string $view = 'filament.pages.ai-translation-settings';
    protected static ?string $navigationLabel = 'AI Translation';
    protected static ?string $title = 'AI Translation Settings';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function getNavigationSort(): ?int
    {
        return 99; // Show at bottom of Settings group
    }

    public function mount(): void
    {
        $this->form->fill([
            'api_key' => Setting::get('ai_translation_api_key'),
            'model' => config('ai-translation.openai.model', 'gpt-4-turbo'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('OpenAI API Configuration')
                    ->description('Configure your OpenAI API key and translation settings.')
                    ->schema([
                        TextInput::make('api_key')
                            ->label('OpenAI API Key')
                            ->password()
                            ->revealable()
                            ->placeholder('sk-proj-...')
                            ->helperText('Get your API key from https://platform.openai.com/api-keys')
                            ->required()
                            ->columnSpan(2),

                        Select::make('model')
                            ->label('Translation Model')
                            ->options([
                                'gpt-4-turbo' => 'GPT-4 Turbo (Recommended - Best Quality)',
                                'gpt-4' => 'GPT-4 (High Quality)',
                                'gpt-3.5-turbo' => 'GPT-3.5 Turbo (Faster & Cheaper)',
                            ])
                            ->default('gpt-4-turbo')
                            ->helperText('GPT-4 Turbo provides the best translation quality for tourism content.')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Usage Statistics')
                    ->description('Current month translation usage and costs.')
                    ->schema([
                        TextInput::make('monthly_translations')
                            ->label('Translations This Month')
                            ->default(fn () => TranslationLog::getTotalCount('month'))
                            ->disabled()
                            ->suffix('translations'),

                        TextInput::make('monthly_cost')
                            ->label('Cost This Month')
                            ->default(fn () => '$' . number_format(TranslationLog::getTotalCost('month'), 2))
                            ->disabled()
                            ->prefix('USD'),

                        TextInput::make('today_translations')
                            ->label('Translations Today')
                            ->default(fn () => TranslationLog::getTotalCount('day'))
                            ->disabled()
                            ->suffix('translations'),

                        TextInput::make('today_cost')
                            ->label('Cost Today')
                            ->default(fn () => '$' . number_format(TranslationLog::getTotalCost('day'), 2))
                            ->disabled()
                            ->prefix('USD'),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->action('save'),

            Action::make('test_connection')
                ->label('Test API Key')
                ->icon('heroicon-o-signal')
                ->color('info')
                ->action(function () {
                    try {
                        $apiKey = $this->form->getState()['api_key'] ?? null;

                        if (!$apiKey) {
                            Notification::make()
                                ->title('API Key Required')
                                ->body('Please enter your OpenAI API key first.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Temporarily set API key for testing
                        Setting::set('ai_translation_api_key', $apiKey);

                        $service = new TranslationService();

                        if ($service->validateApiKey()) {
                            Notification::make()
                                ->title('Connection Successful')
                                ->body('Your OpenAI API key is valid and working.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Connection Failed')
                                ->body('Could not connect to OpenAI. Please check your API key.')
                                ->danger()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error')
                            ->body('API key validation failed: ' . $e->getMessage())
                            ->danger()
                            ->send();

                        Log::error('AI Translation API key test failed', [
                            'error' => $e->getMessage(),
                        ]);
                    }
                }),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            // Save API key (encrypted via Setting model)
            Setting::set('ai_translation_api_key', $data['api_key']);

            // Save model preference to config or database
            Setting::set('ai_translation_model', $data['model']);

            Notification::make()
                ->title('Settings Saved')
                ->body('AI Translation settings have been updated successfully.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to save settings: ' . $e->getMessage())
                ->danger()
                ->send();

            Log::error('Failed to save AI Translation settings', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
