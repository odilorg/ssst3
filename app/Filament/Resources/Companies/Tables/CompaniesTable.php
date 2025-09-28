<?php

namespace App\Filament\Resources\Companies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Название компании')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address_street')
                    ->label('Адрес')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('address_city')
                    ->label('Город')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('inn')
                    ->label('ИНН')
                    ->sortable(),
                TextColumn::make('account_number')
                    ->label('Счет')
                    ->sortable(),
                TextColumn::make('bank_name')
                    ->label('Банк')
                    ->searchable()
                    ->limit(20),
                TextColumn::make('bank_mfo')
                    ->label('МФО')
                    ->sortable(),
                TextColumn::make('director_name')
                    ->label('Директор')
                    ->searchable()
                    ->limit(20),
                TextColumn::make('license_number')
                    ->label('Лицензия')
                    ->searchable(),
                ImageColumn::make('logo')
                    ->label('Логотип')
                    ->circular(),
                TextColumn::make('is_operator')
                    ->label('Туроператор')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                // TextColumn::make('hotels_list')
                //     ->label('Отели')
                //     ->getStateUsing(function ($record) {
                //         return $record->hotels->pluck('name')->join(', ');
                //     })
                //     ->limit(50)
                //     ->wrap()
                //     ->toggleable(),
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
            ])
            ->defaultSort('is_operator', 'desc');
    }
}
