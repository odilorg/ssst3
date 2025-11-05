<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('booking.reference')
                    ->label('Бронирование')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->url(fn ($record) => BookingResource::getUrl('edit', ['record' => $record->booking_id]))
                    ->color('primary'),

                Tables\Columns\TextColumn::make('booking.customer_name')
                    ->label('Клиент')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->weight(FontWeight::Medium),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Сумма')
                    ->money('USD')
                    ->color(fn ($record) => $record->isRefund() ? 'danger' : 'success')
                    ->weight(FontWeight::Bold)
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Ожидание',
                        'completed' => 'Завершен',
                        'failed' => 'Неудачно',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ])
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('payment_type')
                    ->label('Тип')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'deposit' => 'Депозит',
                        'full_payment' => 'Полная',
                        'balance' => 'Баланс',
                        'refund' => 'Возврат',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'deposit',
                        'success' => 'full_payment',
                        'info' => 'balance',
                        'danger' => 'refund',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Метод')
                    ->getStateUsing(fn ($record) => $record->getPaymentMethodName())
                    ->toggleable(),

                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('ID транзакции')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Обработано')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('booking.tour.title')
                    ->label('Тур')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидание',
                        'completed' => 'Завершен',
                        'failed' => 'Неудачно',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('payment_type')
                    ->label('Тип платежа')
                    ->options([
                        'deposit' => 'Депозит',
                        'full_payment' => 'Полная оплата',
                        'balance' => 'Баланс',
                        'refund' => 'Возврат',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Метод оплаты')
                    ->options([
                        'octo_uzcard' => 'UzCard via OCTO',
                        'octo_humo' => 'HUMO via OCTO',
                        'octo_visa' => 'VISA via OCTO',
                        'octo_mastercard' => 'MasterCard via OCTO',
                        'bank_transfer' => 'Банковский перевод',
                        'cash' => 'Наличные',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('От')
                            ->native(false),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('До')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),

                Tables\Filters\Filter::make('completed_today')
                    ->label('Завершено сегодня')
                    ->query(fn ($query) => $query
                        ->where('status', 'completed')
                        ->whereDate('processed_at', now())
                    ),

                Tables\Filters\Filter::make('refunds')
                    ->label('Только возвраты')
                    ->query(fn ($query) => $query->where('payment_type', 'refund')),

                Tables\Filters\Filter::make('high_value')
                    ->label('Высокая сумма (>$1000)')
                    ->query(fn ($query) => $query->where('amount', '>', 1000)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Детали платежа')
                    ->modalContent(fn ($record) => view('filament.resources.payment-details', [
                        'payment' => $record,
                        'gatewayResponse' => $record->gateway_response,
                    ]))
                    ->modalWidth('2xl'),

                Tables\Actions\Action::make('view_booking')
                    ->label('Посмотреть бронирование')
                    ->icon('heroicon-o-calendar')
                    ->color('info')
                    ->url(fn ($record) => BookingResource::getUrl('edit', ['record' => $record->booking_id])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportBulkAction::make()
                        ->label('Экспорт выбранных'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->emptyStateHeading('Нет платежей')
            ->emptyStateDescription('Платежи будут отображаться здесь после обработки')
            ->emptyStateIcon('heroicon-o-credit-card');
    }
}
