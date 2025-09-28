<?php

namespace App\Filament\Resources\TransportTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransportTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Тип транспорта')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Категория')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bus' => 'success',
                        'car' => 'primary',
                        'mikro_bus' => 'warning',
                        'mini_van' => 'info',
                        'air' => 'danger',
                        'rail' => 'gray',
                    }),
                TextColumn::make('running_days')
                    ->label('Дни работы')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', array_map(fn($day) => match($day) {
                        'monday' => 'Пн',
                        'tuesday' => 'Вт',
                        'wednesday' => 'Ср',
                        'thursday' => 'Чт',
                        'friday' => 'Пт',
                        'saturday' => 'Сб',
                        'sunday' => 'Вс',
                    }, $state)) : '')
                    ->limit(50),
                TextColumn::make('transports_count')
                    ->label('Количество транспорта')
                    ->counts('transports')
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
