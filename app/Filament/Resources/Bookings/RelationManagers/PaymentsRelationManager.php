<?php

namespace App\Filament\Resources\Bookings\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'История платежей';

    protected static ?string $modelLabel = 'Платеж';

    protected static ?string $pluralModelLabel = 'Платежи';

    protected static ?string $recordTitleAttribute = 'created_at';

    public function form(Schema $schema): Schema
    {
        // Read-only - no form needed
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('created_at')
            ->columns([
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
                    ]),

                Tables\Columns\BadgeColumn::make('payment_type')
                    ->label('Тип')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'deposit' => 'Депозит',
                        'full_payment' => 'Полная оплата',
                        'balance' => 'Баланс',
                        'refund' => 'Возврат',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'deposit',
                        'success' => 'full_payment',
                        'info' => 'balance',
                        'danger' => 'refund',
                    ]),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Метод')
                    ->getStateUsing(fn ($record) => $record->getPaymentMethodName()),

                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('ID транзакции')
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Обработано')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Ожидание',
                        'completed' => 'Завершен',
                        'failed' => 'Неудачно',
                    ]),

                Tables\Filters\SelectFilter::make('payment_type')
                    ->options([
                        'deposit' => 'Депозит',
                        'full_payment' => 'Полная оплата',
                        'balance' => 'Баланс',
                        'refund' => 'Возврат',
                    ]),
            ])
            ->headerActions([
                // No create - payments are created via gateway/admin only
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Детали платежа')
                    ->modalContent(fn ($record) => view('filament.resources.payment-details', [
                        'payment' => $record,
                        'gatewayResponse' => $record->gateway_response,
                    ]))
                    ->modalWidth('2xl'),
            ])
            ->bulkActions([
                // No bulk actions for payments
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Нет платежей')
            ->emptyStateDescription('Платежи будут отображаться здесь после обработки')
            ->emptyStateIcon('heroicon-o-credit-card');
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
