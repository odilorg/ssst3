<?php

namespace App\Filament\Resources\TourDepartures\Tables;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;

class TourDeparturesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tour.title')
                    ->label('Тур')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->wrap(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Дата начала')
                    ->date('d M Y')
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Дата окончания')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('departure_type')
                    ->label('Тип')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'group' => 'Группа',
                        'private' => 'Приватный',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'group',
                        'warning' => 'private',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open' => 'Открыт',
                        'guaranteed' => 'Гарантирован',
                        'full' => 'Полный',
                        'cancelled' => 'Отменен',
                        'completed' => 'Завершен',
                        default => $state,
                    })
                    ->colors([
                        'secondary' => 'open',
                        'success' => 'guaranteed',
                        'danger' => 'full',
                        'warning' => 'cancelled',
                        'primary' => 'completed',
                    ]),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('Вместимость')
                    ->getStateUsing(fn ($record) => "{$record->booked_pax}/{$record->max_pax}")
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->isFull() => 'danger',
                        $record->isGuaranteed() => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\ViewColumn::make('occupancy')
                    ->label('Заполненность')
                    ->view('filament.tables.columns.occupancy-progress')
                    ->state(fn ($record) => [
                        'percentage' => $record->getOccupancyPercentage(),
                        'booked' => $record->booked_pax,
                        'max' => $record->max_pax,
                    ]),

                Tables\Columns\TextColumn::make('price_per_person')
                    ->label('Цена')
                    ->money('USD')
                    ->getStateUsing(fn ($record) => $record->getEffectivePrice())
                    ->toggleable(),

                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Брони')
                    ->counts('confirmedBookings')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tour_id')
                    ->label('Тур')
                    ->relationship('tour', 'title')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'open' => 'Открыт',
                        'guaranteed' => 'Гарантирован',
                        'full' => 'Полный',
                        'cancelled' => 'Отменен',
                        'completed' => 'Завершен',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('departure_type')
                    ->label('Тип')
                    ->options([
                        'group' => 'Группа',
                        'private' => 'Приватный',
                    ]),

                Tables\Filters\Filter::make('upcoming')
                    ->label('Предстоящие')
                    ->query(fn ($query) => $query->where('start_date', '>=', now())),

                Tables\Filters\Filter::make('available')
                    ->label('Доступные')
                    ->query(fn ($query) => $query->available()),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('От'),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('До'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('start_date', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('start_date', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalContent(fn ($record) => view('filament.resources.tour-departure-details', [
                        'departure' => $record,
                    ]))
                    ->modalWidth('2xl'),

                Tables\Actions\Action::make('view_bookings')
                    ->label('Брони')
                    ->icon('heroicon-o-ticket')
                    ->color('info')
                    ->visible(fn ($record) => $record->bookings()->count() > 0)
                    ->url(fn ($record) => BookingResource::getUrl('index', [
                        'tableFilters' => [
                            'departure_id' => ['value' => $record->id],
                        ],
                    ])),

                Tables\Actions\Action::make('mark_guaranteed')
                    ->label('Гарантировать')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'open')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'guaranteed']);
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Отправление гарантировано')
                            ->send();
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('Отменить')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => !in_array($record->status, ['cancelled', 'completed']))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'cancelled']);
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Отправление отменено')
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => $record->bookings()->count() === 0),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_date', 'asc')
            ->poll('30s');
    }
}
