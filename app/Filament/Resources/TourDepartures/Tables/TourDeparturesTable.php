<?php

namespace App\Filament\Resources\TourDepartures\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TourDeparturesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tour.title')
                    ->label('Tour')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->tour?->title),

                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->description(fn ($record) =>
                        $record->start_date->diffInDays($record->end_date) + 1 . ' days'
                    ),

                TextColumn::make('departure_time')
                    ->label('Time')
                    ->formatStateUsing(fn ($state) => $state ? substr($state, 0, 5) : '—')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('end_date')
                    ->label('End Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'primary' => 'open',
                        'success' => 'guaranteed',
                        'danger' => 'full',
                        'secondary' => 'completed',
                        'warning' => 'cancelled',
                    ])
                    ->icons([
                        'heroicon-o-calendar' => 'open',
                        'heroicon-o-check-circle' => 'guaranteed',
                        'heroicon-o-x-circle' => 'full',
                        'heroicon-o-check-badge' => 'completed',
                        'heroicon-o-ban' => 'cancelled',
                    ])
                    ->sortable(),

                TextColumn::make('capacity')
                    ->label('Capacity')
                    ->getStateUsing(fn ($record) =>
                        $record->booked_pax . ' / ' . $record->max_pax
                    )
                    ->description(fn ($record) =>
                        $record->spots_remaining . ' spots left'
                    )
                    ->color(fn ($record) => match(true) {
                        $record->is_sold_out => 'danger',
                        $record->is_filling_fast => 'warning',
                        default => 'success'
                    }),

                TextColumn::make('departure_type')
                    ->label('Type')
                    ->badge()
                    ->colors([
                        'info' => 'group',
                        'secondary' => 'private',
                    ])
                    ->sortable(),

                TextColumn::make('price_per_person')
                    ->label('Price')
                    ->money('USD')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Tour pricing'),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Date range filter - most important for finding specific departures
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')
                            ->label('From Date')
                            ->native(false)
                            ->displayFormat('M d, Y'),
                        DatePicker::make('until')
                            ->label('Until Date')
                            ->native(false)
                            ->displayFormat('M d, Y'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $date) => $q->where('start_date', '>=', $date))
                            ->when($data['until'] ?? null, fn ($q, $date) => $q->where('start_date', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'From ' . Carbon::parse($data['from'])->format('M d, Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Until ' . Carbon::parse($data['until'])->format('M d, Y');
                        }
                        return $indicators;
                    }),

                // Quick month filter - jump to any month quickly
                SelectFilter::make('month')
                    ->label('Month')
                    ->options(function () {
                        $options = [];
                        $now = Carbon::now();
                        // Past 2 months + current + next 10 months = ~13 options
                        for ($i = -2; $i <= 10; $i++) {
                            $date = $now->copy()->addMonths($i)->startOfMonth();
                            $key = $date->format('Y-m');
                            $options[$key] = $date->format('F Y');
                        }
                        return $options;
                    })
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        $date = Carbon::parse($data['value'] . '-01');
                        return $query
                            ->where('start_date', '>=', $date->startOfMonth())
                            ->where('start_date', '<=', $date->copy()->endOfMonth());
                    }),

                SelectFilter::make('tour')
                    ->relationship('tour', 'title', fn ($query) => $query->whereNotNull('title')->where('title', '!=', ''))
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'guaranteed' => 'Guaranteed',
                        'full' => 'Full',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),

                SelectFilter::make('departure_type')
                    ->options([
                        'group' => 'Group',
                        'private' => 'Private',
                    ]),

                // Quick filter: upcoming only (hide past)
                Filter::make('upcoming_only')
                    ->label('Upcoming only')
                    ->query(fn ($query) => $query->where('start_date', '>=', now()))
                    ->default(true)
                    ->toggle(),
            ])
            ->filtersFormColumns(3)
            ->defaultSort('start_date', 'asc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_change_status')
                        ->label('Change Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Select::make('status')
                                ->label('New Status')
                                ->options([
                                    'open' => 'Open',
                                    'guaranteed' => 'Guaranteed',
                                    'full' => 'Full',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                ])
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $count = $records->count();
                            $records->each->update(['status' => $data['status']]);
                            Notification::make()
                                ->success()
                                ->title("Updated {$count} departure(s)")
                                ->body("Status changed to \"{$data['status']}\".")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalDescription('This will change the status of all selected departures.'),

                    BulkAction::make('bulk_change_price')
                        ->label('Change Price')
                        ->icon('heroicon-o-currency-dollar')
                        ->form([
                            TextInput::make('price_per_person')
                                ->label('New Price per Person')
                                ->numeric()
                                ->prefix('$')
                                ->required()
                                ->minValue(0),
                        ])
                        ->action(function ($records, array $data) {
                            $count = $records->count();
                            $records->each->update(['price_per_person' => $data['price_per_person']]);
                            Notification::make()
                                ->success()
                                ->title("Updated {$count} departure(s)")
                                ->body("Price set to \${$data['price_per_person']} per person.")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalDescription('This will change the price of all selected departures.'),

                    BulkAction::make('bulk_change_capacity')
                        ->label('Change Capacity')
                        ->icon('heroicon-o-user-group')
                        ->form([
                            TextInput::make('max_pax')
                                ->label('New Max Guests')
                                ->numeric()
                                ->required()
                                ->minValue(1),
                        ])
                        ->action(function ($records, array $data) {
                            $count = $records->count();
                            $records->each->update(['max_pax' => $data['max_pax']]);
                            Notification::make()
                                ->success()
                                ->title("Updated {$count} departure(s)")
                                ->body("Max capacity set to {$data['max_pax']} guests.")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalDescription('This will change the max capacity of all selected departures.'),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
