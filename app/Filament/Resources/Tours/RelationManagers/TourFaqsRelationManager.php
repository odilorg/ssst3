<?php

namespace App\Filament\Resources\Tours\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TourFaqsRelationManager extends RelationManager
{
    protected static string $relationship = 'faqs';

    protected static ?string $title = 'Часто задаваемые вопросы';

    protected static ?string $modelLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'FAQs';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('question_en')
                    ->label('🇬🇧 Question (English)')
                    ->required()
                    ->maxLength(255)
                    ->afterStateHydrated(function ($component, $state, $record) {
                        if ($record) {
                            $component->state($record->getTranslation('question', 'en'));
                        }
                    })
                    ->dehydrated(false)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('question_ru')
                    ->label('🇷🇺 Вопрос (Русский)')
                    ->maxLength(255)
                    ->afterStateHydrated(function ($component, $state, $record) {
                        if ($record) {
                            $component->state($record->getTranslation('question', 'ru'));
                        }
                    })
                    ->dehydrated(false)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('question_uz')
                    ->label('🇺🇿 Savol (O\'zbek)')
                    ->maxLength(255)
                    ->afterStateHydrated(function ($component, $state, $record) {
                        if ($record) {
                            $component->state($record->getTranslation('question', 'uz'));
                        }
                    })
                    ->dehydrated(false)
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('question')
                    ->afterStateHydrated(function ($component, $record) {
                        if ($record) {
                            $component->state($record->getTranslations('question'));
                        }
                    }),

                Forms\Components\Textarea::make('answer_en')
                    ->label('🇬🇧 Answer (English)')
                    ->required()
                    ->rows(5)
                    ->afterStateHydrated(function ($component, $state, $record) {
                        if ($record) {
                            $component->state($record->getTranslation('answer', 'en'));
                        }
                    })
                    ->dehydrated(false)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('answer_ru')
                    ->label('🇷🇺 Ответ (Русский)')
                    ->rows(5)
                    ->afterStateHydrated(function ($component, $state, $record) {
                        if ($record) {
                            $component->state($record->getTranslation('answer', 'ru'));
                        }
                    })
                    ->dehydrated(false)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('answer_uz')
                    ->label('🇺🇿 Javob (O\'zbek)')
                    ->rows(5)
                    ->afterStateHydrated(function ($component, $state, $record) {
                        if ($record) {
                            $component->state($record->getTranslation('answer', 'uz'));
                        }
                    })
                    ->dehydrated(false)
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('answer')
                    ->afterStateHydrated(function ($component, $record) {
                        if ($record) {
                            $component->state($record->getTranslations('answer'));
                        }
                    }),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Порядок сортировки')
                    ->numeric()
                    ->default(0)
                    ->helperText('Меньшее число = выше в списке'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                Tables\Columns\TextColumn::make('question')
                    ->label('Вопрос')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('answer')
                    ->label('Ответ')
                    ->limit(60)
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Добавить FAQ'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->paginated(false);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->handleTranslatableFields($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->handleTranslatableFields($data);
    }

    private function handleTranslatableFields(array $data): array
    {
        $translatableFields = ['question', 'answer'];

        foreach ($translatableFields as $field) {
            if (isset($data[$field . '_en']) || isset($data[$field . '_ru']) || isset($data[$field . '_uz'])) {
                $data[$field] = [
                    'en' => $data[$field . '_en'] ?? '',
                    'ru' => $data[$field . '_ru'] ?? '',
                    'uz' => $data[$field . '_uz'] ?? '',
                ];

                unset($data[$field . '_en']);
                unset($data[$field . '_ru']);
                unset($data[$field . '_uz']);
            }
        }

        return $data;
    }
}
