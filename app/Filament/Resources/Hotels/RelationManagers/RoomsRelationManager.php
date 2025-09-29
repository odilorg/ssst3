<?php

namespace App\Filament\Resources\Hotels\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('Название номера')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Например: Deluxe Suite, Standard Room')
                    ->helperText('Введите название номера'),
                Forms\Components\Select::make('room_type_id')
                    ->label('Тип номера')
                    ->relationship('roomType', 'type')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('type')
                            ->label('Тип номера')
                            ->required()
                            ->maxLength(255),
                    ]),
                Forms\Components\TextInput::make('cost_per_night')
                    ->label('Стоимость за ночь')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0),
                Forms\Components\TextInput::make('room_size')
                    ->label('Размер номера (м²)')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                Forms\Components\Textarea::make('description')
                    ->label('Описание номера')
                    ->maxLength(555)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('images')
                    ->label('Изображения номера')
                    ->image()
                    ->multiple()
                    ->columnSpanFull(),
                Forms\Components\Select::make('amenities')
                    ->label('Удобства')
                    ->relationship('amenities', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Название удобства')
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название номера')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roomType.type')
                    ->label('Тип номера')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_per_night')
                    ->label('Стоимость за ночь')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('room_size')
                    ->label('Размер (м²)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('images')
                    ->label('Изображения')
                    ->circular()
                    ->stacked(),
                Tables\Columns\TextColumn::make('amenities.name')
                    ->label('Удобства')
                    ->badge()
                    ->separator(',')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Ensure name field is always present
                        if (empty($data['name'])) {
                            $data['name'] = 'Room ' . uniqid();
                        }
                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
