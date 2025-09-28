<?php

namespace App\Filament\Resources\Drivers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DriversTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('ФИО')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->limit(20),
                TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable(),
                ImageColumn::make('profile_image')
                    ->label('Фото профиля')
                    ->circular(),
                TextColumn::make('address')
                    ->label('Адрес')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('license_number')
                    ->label('Серия и номер водительского удостоверения')
                    ->searchable()
                    ->limit(20),
                TextColumn::make('license_expiry_date')
                    ->label('Дата окончания действия водительского удостоверения')
                    ->searchable()
                    ->limit(20),
                ImageColumn::make('license_image')
                    ->label('Фото водительского удостоверения')
                    ->circular(),
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
