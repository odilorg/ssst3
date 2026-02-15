<?php

namespace App\Filament\Resources\Cities\RelationManagers;

use App\Jobs\TranslateCityWithAI;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class CityTranslationsRelationManager extends RelationManager
{
    protected static string $relationship = 'translations';

    protected static ?string $title = 'Переводы';

    protected static ?string $modelLabel = 'Перевод';

    protected static ?string $pluralModelLabel = 'Переводы';

    protected static function getLocaleOptions(): array
    {
        $flags = [
            'en' => "\u{1F1EC}\u{1F1E7}", 'ru' => "\u{1F1F7}\u{1F1FA}", 'uz' => "\u{1F1FA}\u{1F1FF}",
            'fr' => "\u{1F1EB}\u{1F1F7}", 'es' => "\u{1F1EA}\u{1F1F8}", 'de' => "\u{1F1E9}\u{1F1EA}",
            'zh' => "\u{1F1E8}\u{1F1F3}", 'ar' => "\u{1F1F8}\u{1F1E6}", 'it' => "\u{1F1EE}\u{1F1F9}",
            'pt' => "\u{1F1F5}\u{1F1F9}", 'ja' => "\u{1F1EF}\u{1F1F5}", 'ko' => "\u{1F1F0}\u{1F1F7}",
            'tr' => "\u{1F1F9}\u{1F1F7}",
        ];

        $options = [];
        foreach (config('ai-translation.locale_names', []) as $code => $name) {
            $flag = $flags[$code] ?? '';
            $options[$code] = "{$flag} {$name}";
        }

        return $options;
    }

    protected static function getLocaleFlag(string $locale): string
    {
        $flags = [
            'en' => "\u{1F1EC}\u{1F1E7}", 'ru' => "\u{1F1F7}\u{1F1FA}", 'uz' => "\u{1F1FA}\u{1F1FF}",
            'fr' => "\u{1F1EB}\u{1F1F7}", 'es' => "\u{1F1EA}\u{1F1F8}", 'de' => "\u{1F1E9}\u{1F1EA}",
            'zh' => "\u{1F1E8}\u{1F1F3}", 'ar' => "\u{1F1F8}\u{1F1E6}", 'it' => "\u{1F1EE}\u{1F1F9}",
            'pt' => "\u{1F1F5}\u{1F1F9}", 'ja' => "\u{1F1EF}\u{1F1F5}", 'ko' => "\u{1F1F0}\u{1F1F7}",
            'tr' => "\u{1F1F9}\u{1F1F7}",
        ];

        return $flags[$locale] ?? '';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Section::make('Основные данные')
                    ->description('Язык и базовая информация перевода')
                    ->schema([
                        Forms\Components\Select::make('locale')
                            ->label('Язык')
                            ->options(self::getLocaleOptions())
                            ->required()
                            ->native(false)
                            ->unique(
                                table: 'city_translations',
                                column: 'locale',
                                ignoreRecord: true,
                                modifyRuleUsing: function (Unique $rule) {
                                    return $rule->where('city_id', $this->ownerRecord->id);
                                }
                            )
                            ->validationMessages([
                                'unique' => 'Перевод для этого языка уже существует.',
                            ])
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('name')
                            ->label('Название города')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL-адрес (slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(
                                table: 'city_translations',
                                column: 'slug',
                                ignoreRecord: true,
                                modifyRuleUsing: function (Unique $rule, Forms\Get $get) {
                                    return $rule->where('locale', $get('locale'));
                                }
                            )
                            ->validationMessages([
                                'unique' => 'Этот URL-адрес уже используется для данного языка.',
                            ])
                            ->helperText('Уникальный URL для каждого языка.')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('tagline')
                            ->label('Слоган')
                            ->maxLength(255)
                            ->helperText('Краткий слоган города')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Описания')
                    ->description('Тексты для страницы города')
                    ->schema([
                        Forms\Components\Textarea::make('short_description')
                            ->label('Краткое описание')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Для карточек и превью (до 500 символов)')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('description')
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

                Forms\Components\Section::make('SEO')
                    ->description('Метаданные для поисковых систем')
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('SEO заголовок')
                            ->maxLength(70)
                            ->helperText('Рекомендуется до 70 символов'),

                        Forms\Components\Textarea::make('seo_description')
                            ->label('SEO описание')
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText('Рекомендуется до 160 символов'),
                    ])
                    ->columns(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('locale')
                    ->label('Язык')
                    ->formatStateUsing(fn (string $state): string => self::getLocaleFlag($state) . ' ' . strtoupper($state))
                    ->sortable()
                    ->width(80),

                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->limit(40)
                    ->wrap(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('has_description')
                    ->label('Описание')
                    ->state(fn ($record): bool => !empty($record->description))
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
                    ->options(self::getLocaleOptions()),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Добавить перевод'),
            ])
            ->actions([
                Action::make('ai_translate')
                    ->label('AI Translate')
                    ->icon('heroicon-o-language')
                    ->color('info')
                    ->visible(fn ($record): bool => $record->locale !== 'en' && config('ai-translation.enabled', true))
                    ->requiresConfirmation()
                    ->modalHeading('AI Translation')
                    ->modalDescription(fn ($record) => 'Translate this city to ' . strtoupper($record->locale) . ' using AI? Existing content will be overwritten.')
                    ->modalSubmitActionLabel('Translate')
                    ->action(function ($record) {
                        $city = $this->ownerRecord;
                        $targetLocale = strtoupper($record->locale);

                        TranslateCityWithAI::dispatch(
                            $city->id,
                            $record->id,
                            auth()->id()
                        );

                        Notification::make()
                            ->title('Translation Queued')
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
