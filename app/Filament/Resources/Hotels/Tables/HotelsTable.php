<?php

namespace App\Filament\Resources\Hotels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class HotelsTable
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
                    ->icon('heroicon-o-building-office-2'),

                TextColumn::make('city.name')
                    ->label('Город')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-map-pin')
                    ->color('gray'),

                TextColumn::make('type')
                    ->label('Тип')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bed_breakfast' => 'B&B',
                        '3_star' => '⭐⭐⭐',
                        '4_star' => '⭐⭐⭐⭐',
                        '5_star' => '⭐⭐⭐⭐⭐',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'bed_breakfast' => 'gray',
                        '3_star' => 'warning',
                        '4_star' => 'success',
                        '5_star' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label('Компания')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-building-storefront')
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->copyMessage('Телефон скопирован')
                    ->copyMessageDuration(1500),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('Email скопирован')
                    ->copyMessageDuration(1500)
                    ->limit(25),

                TextColumn::make('rooms_count')
                    ->label('Номера')
                    ->counts('rooms')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state . ' ном.' : '—'),

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

                SelectFilter::make('type')
                    ->label('Тип гостиницы')
                    ->options([
                        'bed_breakfast' => 'B&B',
                        '3_star' => '3 Star',
                        '4_star' => '4 Star',
                        '5_star' => '5 Star',
                    ])
                    ->multiple()
                    ->indicator('Тип'),

                SelectFilter::make('company_id')
                    ->label('Компания')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->indicator('Компания'),
            ])
            ->recordActions([
                EditAction::make(),
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
