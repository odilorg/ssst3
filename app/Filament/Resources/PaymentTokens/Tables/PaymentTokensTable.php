<?php

namespace App\Filament\Resources\PaymentTokens\Tables;

use App\Filament\Resources\Bookings\BookingResource;
use App\Services\PaymentTokenService;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentTokensTable
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
                    ->label('Booking')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->url(fn ($record) => $record->booking_id ? BookingResource::getUrl('edit', ['record' => $record->booking_id]) : null)
                    ->color('primary'),

                Tables\Columns\TextColumn::make('booking.customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('token')
                    ->label('Token')
                    ->limit(20)
                    ->copyable()
                    ->copyMessage('Token copied!')
                    ->weight(FontWeight::Medium)
                    ->fontFamily('mono'),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'balance_payment' => 'Balance Payment',
                        'deposit_payment' => 'Deposit Payment',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->colors([
                        'info' => 'balance_payment',
                        'primary' => 'deposit_payment',
                    ])
                    ->sortable(),

                Tables\Columns\IconColumn::make('valid_status')
                    ->label('Valid')
                    ->getStateUsing(fn ($record) => $record->isValid())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\IconColumn::make('used_status')
                    ->label('Used')
                    ->getStateUsing(fn ($record) => $record->isUsed())
                    ->boolean()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->color(fn ($record) => $record->isExpired() ? 'danger' : ($record->expires_at->isBefore(now()->addDay()) ? 'warning' : 'success'))
                    ->weight(fn ($record) => $record->isExpired() ? FontWeight::Bold : FontWeight::Medium)
                    ->description(fn ($record) => $record->isExpired() ? 'Expired' : ($record->expires_at->isBefore(now()->addDay()) ? 'Expires soon' : null)),

                Tables\Columns\TextColumn::make('used_at')
                    ->label('Used At')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Token Type')
                    ->options([
                        'balance_payment' => 'Balance Payment',
                        'deposit_payment' => 'Deposit Payment',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('valid_only')
                    ->label('Valid Tokens')
                    ->query(fn ($query) => $query
                        ->where('expires_at', '>', now())
                        ->whereNull('used_at')
                    ),

                Tables\Filters\Filter::make('used_only')
                    ->label('Used Tokens')
                    ->query(fn ($query) => $query->whereNotNull('used_at')),

                Tables\Filters\Filter::make('expired')
                    ->label('Expired')
                    ->query(fn ($query) => $query->where('expires_at', '<', now())),

                Tables\Filters\Filter::make('expires_soon')
                    ->label('Expires Soon (24h)')
                    ->query(fn ($query) => $query
                        ->where('expires_at', '>', now())
                        ->where('expires_at', '<', now()->addDay())
                    ),

                Tables\Filters\Filter::make('active')
                    ->label('Active (Valid & Not Expired)')
                    ->query(fn ($query) => $query
                        ->where('expires_at', '>', now())
                        ->whereNull('used_at')
                    ),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('From')
                            ->native(false),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('Until')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view_url')
                    ->label('View URL')
                    ->icon('heroicon-o-link')
                    ->color('info')
                    ->modalHeading('Payment URL')
                    ->modalContent(fn ($record) => view('filament.resources.payment-token-url', [
                        'token' => $record,
                        'url' => route('balance-payment.show', ['token' => $record->token]),
                    ]))
                    ->modalWidth('md'),

                Tables\Actions\Action::make('invalidate')
                    ->label('Invalidate')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Invalidate Token')
                    ->modalDescription('Are you sure you want to invalidate this token? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, invalidate')
                    ->visible(fn ($record) => $record->isValid())
                    ->action(function ($record) {
                        // Invalidate by setting expiry to past
                        $record->update([
                            'expires_at' => now()->subMinute(),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Token Invalidated')
                            ->body('Token for booking ' . $record->booking->reference . ' has been invalidated.')
                            ->success()
                            ->send();

                        \Log::info('Payment token manually invalidated by admin', [
                            'token_id' => $record->id,
                            'booking_id' => $record->booking_id,
                            'admin_id' => auth()->id(),
                        ]);
                    }),

                Tables\Actions\Action::make('regenerate')
                    ->label('Regenerate')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\TextInput::make('expiry_days')
                            ->label('Expiry Days')
                            ->numeric()
                            ->default(7)
                            ->required()
                            ->minValue(1)
                            ->maxValue(30),
                    ])
                    ->modalHeading('Regenerate Token')
                    ->modalDescription('This will invalidate the current token and generate a new one.')
                    ->modalSubmitActionLabel('Regenerate')
                    ->visible(fn ($record) => !$record->isUsed())
                    ->action(function ($record, array $data) {
                        // Invalidate current token by setting expiry to past
                        $record->update([
                            'expires_at' => now()->subMinute(),
                        ]);

                        // Generate new token
                        $tokenService = app(PaymentTokenService::class);
                        $newToken = $tokenService->generateBalancePaymentToken(
                            $record->booking,
                            $data['expiry_days']
                        );

                        $newUrl = route('balance-payment.show', ['token' => $newToken]);

                        \Filament\Notifications\Notification::make()
                            ->title('Token Regenerated')
                            ->body('New token generated for booking ' . $record->booking->reference)
                            ->success()
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('copy')
                                    ->button()
                                    ->label('Copy URL')
                                    ->url($newUrl)
                                    ->openUrlInNewTab(),
                            ])
                            ->send();

                        \Log::info('Payment token regenerated by admin', [
                            'old_token_id' => $record->id,
                            'booking_id' => $record->booking_id,
                            'expiry_days' => $data['expiry_days'],
                            'admin_id' => auth()->id(),
                        ]);
                    }),

                Tables\Actions\Action::make('view_booking')
                    ->label('View Booking')
                    ->icon('heroicon-o-calendar')
                    ->color('info')
                    ->url(fn ($record) => BookingResource::getUrl('edit', ['record' => $record->booking_id])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('invalidate_selected')
                        ->label('Invalidate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->isValid()) {
                                    $record->update([
                                        'expires_at' => now()->subMinute(),
                                    ]);
                                    $count++;
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Tokens Invalidated')
                                ->body("{$count} tokens have been invalidated.")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\ExportBulkAction::make()
                        ->label('Export Selected'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->emptyStateHeading('No Payment Tokens')
            ->emptyStateDescription('Payment tokens will appear here when generated')
            ->emptyStateIcon('heroicon-o-key');
    }
}
