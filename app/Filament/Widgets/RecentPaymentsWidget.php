<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Bookings\BookingResource;
use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentPaymentsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->latest()
                    ->limit(10)
            )
            ->heading('Recent Payments')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking.reference')
                    ->label('Booking')
                    ->searchable()
                    ->url(fn ($record) => BookingResource::getUrl('edit', ['record' => $record->booking_id]))
                    ->color('primary'),

                Tables\Columns\TextColumn::make('booking.customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->color(fn ($record) => $record->isRefund() ? 'danger' : 'success')
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ]),

                Tables\Columns\BadgeColumn::make('payment_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'deposit' => 'Deposit',
                        'full_payment' => 'Full',
                        'balance' => 'Balance',
                        'refund' => 'Refund',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'deposit',
                        'success' => 'full_payment',
                        'info' => 'balance',
                        'danger' => 'refund',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.payments.index') . '?tableSearch=' . $record->id)
                    ->openUrlInNewTab(),
            ])
            ->paginated(false);
    }
}
