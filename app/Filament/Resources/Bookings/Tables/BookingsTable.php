<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Schemas\Components\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Guide;
use App\Models\Restaurant;
use App\Models\Hotel;
use App\Models\Transport;
use App\Models\Room;
use App\Models\MealType;
use Filament\Notifications\Notification;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->label('Номер бронирования')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('Клиент')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tour.title')
                    ->label('Тур')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                TextColumn::make('start_date')
                    ->label('Дата начала')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Дата окончания')
                    ->date()
                    ->sortable(),
                TextColumn::make('pax_total')
                    ->label('Участников')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'in_progress' => 'info',
                        'completed' => 'primary',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Черновик',
                        'pending' => 'В ожидании',
                        'confirmed' => 'Подтверждено',
                        'in_progress' => 'В процессе',
                        'completed' => 'Завершено',
                        'cancelled' => 'Отменено',
                    })
                    ->sortable(),
                TextColumn::make('currency')
                    ->label('Валюта')
                    ->searchable(),
                TextColumn::make('total_price')
                    ->label('Стоимость')
                    ->money('USD')
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

        Action::make('estimate')
            ->label('Смета')
            ->icon('heroicon-o-calculator')
            ->color('info')
            ->url(fn ($record) => route('booking.estimate.print', $record))
            ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
