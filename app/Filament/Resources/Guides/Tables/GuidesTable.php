<?php

namespace App\Filament\Resources\Guides\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class GuidesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Фото')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),

                TextColumn::make('name')
                    ->label('Имя')
                    ->searchable(['first_name', 'last_name', 'patronymic'])
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-user'),

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

                TextColumn::make('city.name')
                    ->label('Город')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-map-pin')
                    ->color('gray'),

                TextColumn::make('spokenLanguages.name')
                    ->label('Языки')
                    ->badge()
                    ->separator(',')
                    ->searchable()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->color('info'),

                TextColumn::make('languages_count')
                    ->label('Кол-во языков')
                    ->counts('spokenLanguages')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('certificate_number')
                    ->label('Сертификат')
                    ->searchable()
                    ->limit(15)
                    ->toggleable()
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state) => $state ? '✓ ' . $state : 'Нет'),

                TextColumn::make('certificate_category')
                    ->label('Категория')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        '1' => 'success',
                        '2' => 'warning',
                        '3' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state ? 'Кат. ' . $state : '-')
                    ->toggleable(),

                IconColumn::make('is_marketing')
                    ->label('Маркетинг')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

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

                SelectFilter::make('spokenLanguages')
                    ->label('Языки')
                    ->relationship('spokenLanguages', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->indicator('Язык'),

                SelectFilter::make('certificate_category')
                    ->label('Категория сертификата')
                    ->options([
                        '1' => 'Категория 1',
                        '2' => 'Категория 2',
                        '3' => 'Категория 3',
                    ])
                    ->indicator('Категория'),

                TernaryFilter::make('has_certificate')
                    ->label('Наличие сертификата')
                    ->placeholder('Все гиды')
                    ->trueLabel('Есть сертификат')
                    ->falseLabel('Нет сертификата')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('certificate_number'),
                        false: fn ($query) => $query->whereNull('certificate_number'),
                    )
                    ->indicator('Сертификат'),

                TernaryFilter::make('is_marketing')
                    ->label('Маркетинг')
                    ->placeholder('Все гиды')
                    ->trueLabel('Только маркетинг')
                    ->falseLabel('Без маркетинга')
                    ->indicator('Маркетинг'),
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
