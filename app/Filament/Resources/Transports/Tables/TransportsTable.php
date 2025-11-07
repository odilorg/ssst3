<?php

namespace App\Filament\Resources\Transports\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransportsTable
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

                TextColumn::make('plate_number')
                    ->label('Номерной знак')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-identification')
                    ->copyable()
                    ->copyMessage('Номер скопирован')
                    ->copyMessageDuration(1500),

                TextColumn::make('transportType.type')
                    ->label('Тип')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-truck')
                    ->color('info'),

                TextColumn::make('category')
                    ->label('Категория')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bus' => 'Автобус',
                        'car' => 'Легковой',
                        'mikro_bus' => 'Микроавтобус',
                        'mini_van' => 'Минивэн',
                        'air' => 'Авиа',
                        'rail' => 'Ж/Д',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'bus' => 'success',
                        'car' => 'primary',
                        'mikro_bus' => 'warning',
                        'mini_van' => 'info',
                        'air' => 'danger',
                        'rail' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('model')
                    ->label('Модель')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-cog-6-tooth')
                    ->limit(20)
                    ->placeholder('—'),

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

                TextColumn::make('driver.name')
                    ->label('Водитель')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('number_of_seat')
                    ->label('Мест')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => $state ? $state . ' мест' : '—'),

                TextColumn::make('fuel_type')
                    ->label('Топливо')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'diesel' => 'Дизель',
                        'benzin/propane' => 'Бензин/Пропан',
                        'natural_gaz' => 'Газ',
                        default => '—',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'diesel' => 'success',
                        'benzin/propane' => 'warning',
                        'natural_gaz' => 'info',
                        default => 'gray',
                    })
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('transportInstancePrices_count')
                    ->label('Цены')
                    ->counts('transportInstancePrices')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state . ' цен' . ($state > 1 ? 'ы' : 'а') : 'По умолчанию')
                    ->toggleable(),

                TextColumn::make('amenities_count')
                    ->label('Удобства')
                    ->counts('amenities')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state : '—')
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

                SelectFilter::make('driver_id')
                    ->label('Водитель')
                    ->relationship('driver', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->indicator('Водитель'),

                SelectFilter::make('transport_type_id')
                    ->label('Тип транспорта')
                    ->relationship('transportType', 'type')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->indicator('Тип'),

                SelectFilter::make('category')
                    ->label('Категория')
                    ->options([
                        'bus' => 'Автобус',
                        'car' => 'Легковой автомобиль',
                        'mikro_bus' => 'Микроавтобус',
                        'mini_van' => 'Минивэн',
                        'air' => 'Авиатранспорт',
                        'rail' => 'Железнодорожный',
                    ])
                    ->multiple()
                    ->query(function ($query, array $data) {
                        if (!empty($data['values'])) {
                            $query->whereHas('transportType', function ($q) use ($data) {
                                $q->whereIn('category', $data['values']);
                            });
                        }
                    })
                    ->indicator('Категория'),

                SelectFilter::make('fuel_type')
                    ->label('Тип топлива')
                    ->options([
                        'diesel' => 'Дизель',
                        'benzin/propane' => 'Бензин/Пропан',
                        'natural_gaz' => 'Газ',
                    ])
                    ->multiple()
                    ->indicator('Топливо'),
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
