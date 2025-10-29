<?php

namespace App\Filament\Resources\Tours\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TourExtrasRelationManager extends RelationManager
{
    protected static string $relationship = 'extras';

    protected static ?string $title = 'Дополнительные услуги';

    protected static ?string $modelLabel = 'Дополнительная услуга';

    protected static ?string $pluralModelLabel = 'Дополнительные услуги';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('Название')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label('Описание')
                    ->rows(4)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('price')
                    ->label('Цена')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->prefix('$'),

                Forms\Components\TextInput::make('currency')
                    ->label('Валюта')
                    ->required()
                    ->default('USD')
                    ->maxLength(3),

                Forms\Components\Select::make('price_unit')
                    ->label('Единица цены')
                    ->options([
                        'per_person' => 'За человека',
                        'per_group' => 'За группу',
                        'per_session' => 'За сессию',
                    ])
                    ->required()
                    ->default('per_person'),

                Forms\Components\TextInput::make('icon')
                    ->label('Иконка')
                    ->maxLength(50)
                    ->helperText('Например: car, camera, utensils'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Активна')
                    ->default(true),

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
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Описание')
                    ->limit(40)
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money(fn ($record) => $record->currency ?? 'USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_unit')
                    ->label('Единица')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'per_person' => 'За чел.',
                        'per_group' => 'За группу',
                        'per_session' => 'За сессию',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активна')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('icon')
                    ->label('Иконка')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активные')
                    ->boolean()
                    ->trueLabel('Только активные')
                    ->falseLabel('Только неактивные')
                    ->native(false),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить услугу'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->paginated(false);
    }
}
