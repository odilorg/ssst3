<?php

namespace App\Filament\Resources\Tours\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ToursTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Название тура')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('duration_days')
                    ->label('Продолжительность')
                    ->suffix(' дн.')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('short_description')
                    ->label('Краткое описание')
                    ->searchable()
                    ->limit(50),
                IconColumn::make('is_active')
                    ->label('Активный')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('bookings_count')
                    ->label('Количество бронирований')
                    ->counts('bookings')
                    ->sortable(),
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
                Action::make('view_frontend')
                    ->label('View Frontend')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('info')
                    ->url(fn ($record) => '/tour-details.html?slug=' . $record->slug)
                    ->openUrlInNewTab(),
                ViewAction::make()
                    ->label('View Formatted')
                    ->icon('heroicon-o-document-text'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
