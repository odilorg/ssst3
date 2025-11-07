<?php

namespace App\Filament\Resources\Companies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Лого')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-company.png')),

                TextColumn::make('name')
                    ->label('Название компании')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-building-office')
                    ->color('primary'),

                IconColumn::make('is_operator')
                    ->label('Оператор')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

                TextColumn::make('city.name')
                    ->label('Город')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-map-pin')
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->copyMessage('Телефон скопирован')
                    ->copyMessageDuration(1500)
                    ->placeholder('—'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->copyable()
                    ->copyMessage('Email скопирован')
                    ->copyMessageDuration(1500)
                    ->placeholder('—')
                    ->limit(30),

                TextColumn::make('inn')
                    ->label('ИНН')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->placeholder('—')
                    ->copyable()
                    ->copyMessage('ИНН скопирован')
                    ->copyMessageDuration(1500),

                TextColumn::make('license_number')
                    ->label('Лицензия')
                    ->searchable()
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-shield-check')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('director_name')
                    ->label('Директор')
                    ->searchable()
                    ->icon('heroicon-o-user-circle')
                    ->limit(25)
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('account_number')
                    ->label('Счет')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ? substr($state, 0, 8) . '...' : '—')
                    ->tooltip(fn ($state) => $state)
                    ->copyable()
                    ->copyMessage('Счет скопирован')
                    ->copyMessageDuration(1500)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('bank_name')
                    ->label('Банк')
                    ->searchable()
                    ->icon('heroicon-o-building-library')
                    ->limit(25)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('bank_mfo')
                    ->label('МФО')
                    ->searchable()
                    ->badge()
                    ->color('warning')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('city_id')
                    ->label('Город')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->indicator('Город'),

                TernaryFilter::make('is_operator')
                    ->label('Туроператор')
                    ->placeholder('Все компании')
                    ->trueLabel('Туроператоры')
                    ->falseLabel('Не туроператоры')
                    ->indicator('Оператор'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('is_operator', 'desc')
            ->striped();
    }
}
