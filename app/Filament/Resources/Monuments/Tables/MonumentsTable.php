<?php

namespace App\Filament\Resources\Monuments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MonumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images')
                    ->label('Фото')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(),

                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-building-library')
                    ->limit(30),

                TextColumn::make('city.name')
                    ->label('Город')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-map-pin')
                    ->color('gray'),

                TextColumn::make('company.name')
                    ->label('Компания')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-building-office')
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('ticket_price')
                    ->label('Базовая цена')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => $state ? '$' . number_format($state, 2) : '—')
                    ->sortable(),

                TextColumn::make('foreigner_adult_price')
                    ->label('Иностранец (взр.)')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => $state ? '$' . number_format($state, 2) : '—')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('local_adult_price')
                    ->label('Местный (взр.)')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0) . ' сум' : '—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('voucher')
                    ->label('Ваучер')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable()
                    ->toggleable(),

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

                SelectFilter::make('company_id')
                    ->label('Компания')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->indicator('Компания'),

                TernaryFilter::make('voucher')
                    ->label('Генерация ваучера')
                    ->placeholder('Все монументы')
                    ->trueLabel('С ваучером')
                    ->falseLabel('Без ваучера')
                    ->indicator('Ваучер'),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
