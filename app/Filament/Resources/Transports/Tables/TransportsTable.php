<?php

namespace App\Filament\Resources\Transports\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plate_number')
                    ->label('Номерной знак')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('driver.name')
                    ->label('Водитель')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('images')
                    ->label('Изображения')
                    ->circular()
                    ->stacked(),
                TextColumn::make('model')
                    ->label('Модель')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('number_of_seat')
                    ->label('Количество мест')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Категория')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bus' => 'success',
                        'car' => 'primary',
                        'mikro_bus' => 'warning',
                        'mini_van' => 'info',
                        'air' => 'danger',
                        'rail' => 'gray',
                    }),
                TextColumn::make('amenities.name')
                    ->label('Удобства')
                    ->listWithLineBreaks()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
