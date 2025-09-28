<?php

namespace App\Filament\Resources\Monuments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MonumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->limit(20),
                TextColumn::make('company.name')
                    ->label('Компания')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('city.name')
                    ->label('Город')
                    ->searchable(),
                TextColumn::make('ticket_price')
                    ->label('Цена билета')
                    ->money('USD')
                    ->sortable(),
                ImageColumn::make('images')
                    ->label('Изображения')
                    ->circular()
                    ->stacked(),
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
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
