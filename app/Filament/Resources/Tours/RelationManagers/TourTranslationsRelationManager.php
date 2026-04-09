<?php

namespace App\Filament\Resources\Tours\RelationManagers;

use App\Jobs\TranslateTourWithAI;
use App\Models\TranslationLog;
use App\Services\OpenAI\TranslationService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class TourTranslationsRelationManager extends RelationManager
{
    protected static string $relationship = 'translations';

    protected static ?string $title = 'Переводы';

    protected static ?string $modelLabel = 'Перевод';

    protected static ?string $pluralModelLabel = 'Переводы';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основные данные')
                    ->description('Язык и базовая информация перевода')
                    ->schema([
                        Select::make('locale')
                            ->label('Язык')
                            ->options([
                                'en' => '🇬🇧 English',
                                'ru' => '🇷🇺 Русский',
                                'uz' => '🇺🇿 O\'zbekcha',
                                'fr' => '🇫🇷 Français',
                                'es' => '🇪🇸 Español',
                                'de' => '🇩🇪 Deutsch',
                                'zh' => '🇨🇳 中文',
                                'ar' => '🇸🇦 العربية',
                                'it' => '🇮🇹 Italiano',
                                'pt' => '🇵🇹 Português',
                                'ja' => '🇯🇵 日本語',
                                'ko' => '🇰🇷 한국어',
                                'tr' => '🇹🇷 Türkçe',
                            ])
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->unique(
                                table: 'tour_translations',
                                column: 'locale',
                                ignoreRecord: true,
                                modifyRuleUsing: function (Unique $rule) {
                                    return $rule->where('tour_id', $this->ownerRecord->id);
                                }
                            )
                            ->validationMessages([
                                'unique' => 'Перевод для этого языка уже существует.',
                            ])
                            ->columnSpan(1),

                        TextInput::make('title')
                            ->label('Заголовок')
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->helperText('Оставьте пустым для автоматического заполнения AI переводом')
                            ->columnSpan(1),

                        TextInput::make('slug')
                            ->label('URL-адрес (slug)')
                            ->maxLength(255)
                            ->unique(
                                table: 'tour_translations',
                                column: 'slug',
                                ignoreRecord: true,
                                modifyRuleUsing: function (Unique $rule, $get) {
                                    return $rule->where('locale', $get('locale'));
                                }
                            )
                            ->validationMessages([
                                'unique' => 'Этот URL-адрес уже используется для данного языка.',
                            ])
                            ->helperText('Уникальный URL для каждого языка. Автозаполняется из заголовка.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Контент')
                    ->description('Содержимое перевода')
                    ->schema([
                        Textarea::make('excerpt')
                            ->label('Краткое описание')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Краткое описание для карточек туров (до 500 символов)')
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Полное описание')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'h2',
                                'h3',
                                'bulletList',
                                'orderedList',
                                'link',
                                'redo',
                                'undo',
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Highlights (Основные моменты)')
                    ->description('Ключевые особенности тура')
                    ->collapsed()
                    ->schema([
                        Repeater::make('highlights_json')
                            ->label('Highlights')
                            ->schema([
                                Textarea::make('text')
                                    ->label('Текст')
                                    ->required()
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Добавить highlight')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['text'] ? Str::limit($state['text'], 50) : null)
                            ->columnSpanFull()
                            ->helperText('Если пусто, будут использованы highlights из основной модели Tour'),
                    ]),

                Section::make('Itinerary (Маршрут по дням)')
                    ->description('Подробный план тура по дням')
                    ->collapsed()
                    ->schema([
                        Repeater::make('itinerary_json')
                            ->label('Дни маршрута')
                            ->schema([
                                TextInput::make('day')
                                    ->label('День')
                                    ->numeric()
                                    ->required()
                                    ->default(fn ($get) => count($get('../../itinerary_json') ?? []) + 1),

                                TextInput::make('title')
                                    ->label('Название дня')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                RichEditor::make('description')
                                    ->label('Описание')
                                    ->required()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'bulletList',
                                        'orderedList',
                                    ])
                                    ->columnSpanFull(),

                                TextInput::make('duration_minutes')
                                    ->label('Продолжительность (минуты)')
                                    ->numeric()
                                    ->helperText('Например: 480 = 8 часов'),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Добавить день')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => isset($state['day']) ? "День {$state['day']}: " . ($state['title'] ?? '') : null)
                            ->columnSpanFull()
                            ->helperText('Если пусто, будет использован itinerary из основной модели Tour'),
                    ]),

                Section::make('Included / Excluded (Включено / Не включено)')
                    ->description('Что включено и не включено в стоимость тура')
                    ->collapsed()
                    ->schema([
                        Repeater::make('included_json')
                            ->label('Включено в стоимость')
                            ->schema([
                                TextInput::make('text')
                                    ->label('Текст')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Добавить пункт')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['text'] ?? null)
                            ->columnSpanFull()
                            ->helperText('Если пусто, будут использованы included из основной модели Tour'),

                        Repeater::make('excluded_json')
                            ->label('Не включено в стоимость')
                            ->schema([
                                TextInput::make('text')
                                    ->label('Текст')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Добавить пункт')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['text'] ?? null)
                            ->columnSpanFull()
                            ->helperText('Если пусто, будут использованы excluded из основной модели Tour'),
                    ]),

                Section::make('FAQ (Часто задаваемые вопросы)')
                    ->description('Вопросы и ответы о туре')
                    ->collapsed()
                    ->schema([
                        Repeater::make('faq_json')
                            ->label('FAQ')
                            ->schema([
                                TextInput::make('question')
                                    ->label('Вопрос')
                                    ->required()
                                    ->maxLength(500)
                                    ->columnSpanFull(),

                                Textarea::make('answer')
                                    ->label('Ответ')
                                    ->required()
                                    ->rows(3)
                                    ->maxLength(1000)
                                    ->columnSpanFull(),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Добавить вопрос')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['question'] ?? null)
                            ->columnSpanFull()
                            ->helperText('Если пусто, будут использованы FAQ из основной модели Tour'),
                    ]),

                Section::make('Requirements (Что нужно знать)')
                    ->description('Важная информация и требования перед поездкой')
                    ->collapsed()
                    ->schema([
                        Repeater::make('requirements_json')
                            ->label('Требования')
                            ->schema([
                                TextInput::make('text')
                                    ->label('Текст')
                                    ->required()
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Добавить требование')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['text'] ? Str::limit($state['text'], 50) : null)
                            ->columnSpanFull()
                            ->helperText('Если пусто, будут использованы requirements из основной модели Tour'),
                    ]),

                Section::make('Additional Content (Дополнительный контент)')
                    ->description('Политика отмены и инструкции по встрече')
                    ->collapsed()
                    ->schema([
                        Textarea::make('cancellation_policy')
                            ->label('Политика отмены')
                            ->rows(4)
                            ->maxLength(2000)
                            ->helperText('Если пусто, будет использована политика отмены из основной модели Tour')
                            ->columnSpanFull(),

                        Textarea::make('meeting_instructions')
                            ->label('Инструкции по встрече')
                            ->rows(4)
                            ->maxLength(2000)
                            ->helperText('Если пусто, будут использованы инструкции из основной модели Tour')
                            ->columnSpanFull(),
                    ]),

                Section::make('SEO')
                    ->description('Метаданные для поисковых систем')
                    ->collapsed()
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('SEO заголовок')
                            ->maxLength(70)
                            ->helperText('Рекомендуется до 70 символов. Если пусто, используется заголовок тура.'),

                        Textarea::make('seo_description')
                            ->label('SEO описание')
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText('Рекомендуется до 160 символов. Если пусто, используется краткое описание.'),
                    ])
                    ->columns(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('locale')
                    ->label('Язык')
                    ->formatStateUsing(function (string $state): string {
                        $info = config('multilang.locale_names.' . $state);
                        return $info
                            ? ($info['flag'] ?? '') . ' ' . strtoupper($state)
                            : strtoupper($state);
                    })
                    ->sortable()
                    ->width(80),

                Tables\Columns\TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('has_content')
                    ->label('Контент')
                    ->state(fn ($record): bool => !empty($record->content))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->width(80),

                Tables\Columns\IconColumn::make('has_seo')
                    ->label('SEO')
                    ->state(fn ($record): bool => !empty($record->seo_title) || !empty($record->seo_description))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->width(80),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('locale', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('locale')
                    ->label('Язык')
                    ->options([
                        'en' => 'English',
                        'ru' => 'Русский',
                        'fr' => 'Français',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Добавить перевод'),
            ])
            ->actions([
                Action::make('ai_translate')
                    ->label('🤖 AI Translate')
                    ->icon('heroicon-o-language')
                    ->color('info')
                    ->visible(fn ($record): bool => config('ai-translation.enabled', true))
                    ->requiresConfirmation()
                    ->modalHeading('AI Translation')
                    ->modalDescription(function ($record) {
                        $tour = $this->ownerRecord;
                        $contentLength = strlen($tour->title ?? '') + strlen($tour->long_description ?? '') + strlen($tour->short_description ?? '');
                        // Rough estimate: 4 chars = 1 token, input + output, model pricing
                        $tokens = (int) ceil($contentLength / 4);
                        $costs = config('ai-translation.cost_per_1k_tokens.deepseek-chat', ['input' => 0.00014, 'output' => 0.00028]);
                        $estimated = round(($tokens / 1000) * ($costs['input'] + $costs['output']) * 12, 4); // ~12 sections
                        $estimatedDisplay = $estimated < 0.01 ? '< $0.01' : '$' . number_format($estimated, 2);
                        return "Translate this tour to " . strtoupper($record->locale) . " using AI? Estimated cost: {$estimatedDisplay} USD.";
                    })
                    ->modalSubmitActionLabel('Translate')
                    ->action(function ($record) {
                        $tour = $this->ownerRecord;
                        $targetLocale = strtoupper($record->locale);

                        // Dispatch translation job with IDs (runs in background, no timeout)
                        TranslateTourWithAI::dispatch(
                            $tour->id,
                            $record->id,
                            auth()->id()
                        );

                        // Show queued notification
                        Notification::make()
                            ->title('🔄 Translation Queued')
                            ->body("Translation to {$targetLocale} has been queued. You will be notified when it's complete (typically 30-60 seconds).")
                            ->info()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->paginated(false);
    }
}
