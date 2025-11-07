<?php

namespace App\Filament\Resources\TourInquiries\Tables;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TourInquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->copyMessage('Reference copied!'),

                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->description(fn ($record) => $record->customer_email),

                TextColumn::make('tour.title')
                    ->label('Tour')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->tour?->title)
                    ->url(fn ($record) => $record->tour ? route('filament.admin.resources.tours.tours.edit', $record->tour) : null)
                    ->openUrlInNewTab(),

                TextColumn::make('message')
                    ->label('Message')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->message),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->colors([
                        'primary' => 'new',
                        'success' => 'replied',
                        'warning' => 'converted',
                        'secondary' => 'closed',
                    ])
                    ->icons([
                        'heroicon-o-sparkles' => 'new',
                        'heroicon-o-chat-bubble-left-right' => 'replied',
                        'heroicon-o-check-circle' => 'converted',
                        'heroicon-o-x-circle' => 'closed',
                    ]),

                TextColumn::make('preferred_date')
                    ->label('Preferred Date')
                    ->date()
                    ->sortable()
                    ->placeholder('Not specified')
                    ->toggleable(),

                TextColumn::make('estimated_guests')
                    ->label('Guests')
                    ->sortable()
                    ->placeholder('Not specified')
                    ->suffix(' guests')
                    ->toggleable(),

                TextColumn::make('customer_country')
                    ->label('Country')
                    ->searchable()
                    ->placeholder('Not specified')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('customer_phone')
                    ->label('Phone')
                    ->searchable()
                    ->placeholder('Not provided')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable()
                    ->description(fn ($record) => $record->created_at->diffForHumans())
                    ->toggleable(),

                TextColumn::make('replied_at')
                    ->label('Replied')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not replied')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('repliedBy.name')
                    ->label('Replied By')
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('booking.reference')
                    ->label('Booking')
                    ->searchable()
                    ->placeholder('Not converted')
                    ->url(fn ($record) => $record->booking ? route('filament.admin.resources.bookings.bookings.edit', $record->booking) : null)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'new' => 'New',
                        'replied' => 'Replied',
                        'converted' => 'Converted',
                        'closed' => 'Closed',
                    ])
                    ->default('new')
                    ->multiple(),

                SelectFilter::make('tour_id')
                    ->label('Tour')
                    ->relationship('tour', 'title')
                    ->searchable()
                    ->preload(),

                Filter::make('has_preferred_date')
                    ->label('Has Preferred Date')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('preferred_date'))
                    ->toggle(),

                Filter::make('has_guests')
                    ->label('Has Guest Count')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('estimated_guests'))
                    ->toggle(),

                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('Received from')
                            ->placeholder('Start date'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Received until')
                            ->placeholder('End date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'Received from ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Received until ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                Action::make('mark_replied')
                    ->label('Mark as Replied')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'new')
                    ->requiresConfirmation()
                    ->modalHeading('Mark Inquiry as Replied')
                    ->modalDescription('This will mark the inquiry as replied and record your user ID.')
                    ->action(function ($record) {
                        $record->markAsReplied(auth()->user());
                        Notification::make()
                            ->title('Inquiry marked as replied')
                            ->success()
                            ->send();
                    }),

                Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->visible(fn ($record) => in_array($record->status, ['new', 'replied']))
                    ->requiresConfirmation()
                    ->modalHeading('Close Inquiry')
                    ->modalDescription('Are you sure you want to close this inquiry?')
                    ->action(function ($record) {
                        $record->status = 'closed';
                        $record->save();
                        Notification::make()
                            ->title('Inquiry closed')
                            ->success()
                            ->send();
                    }),

                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }
}
