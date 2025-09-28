<?php

namespace App\Filament\Resources\OilChanges\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OilChangesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transport.plate_number')
                    ->label('Номерной знак')
                    ->sortable(),
                TextColumn::make('oil_change_date')
                    ->label('Дата замены')
                    ->date()
                    ->sortable(),
                TextColumn::make('mileage_at_change')
                    ->label('Пробег при замене')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('next_change_date')
                    ->label('Дата следующей замены')
                    ->date()
                    ->sortable(),
                TextColumn::make('next_change_mileage')
                    ->label('Пробег следующей замены')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cost')
                    ->label('Стоимость')
                    ->money('UZS')
                    ->sortable(),
                TextColumn::make('oil_type')
                    ->label('Тип масла')
                    ->searchable(),
                TextColumn::make('service_center')
                    ->label('Сервисный центр')
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
