<?php

namespace App\Filament\Resources\TourDepartures\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class TourDepartureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Departure Details')
                    ->description('Configure the tour departure dates and basic information')
                    ->schema([
                        Select::make('tour_id')
                            ->label('Tour')
                            ->relationship('tour', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // Auto-calculate end date when tour is selected and start date exists
                                $startDate = $get('start_date');
                                if ($state && $startDate) {
                                    $tour = \App\Models\Tour::find($state);
                                    if ($tour && $tour->duration_days) {
                                        $start = \Carbon\Carbon::parse($startDate);
                                        $endDate = $start->copy()->addDays($tour->duration_days - 1);
                                        $set('end_date', $endDate->format('Y-m-d'));
                                    }
                                }
                            })
                            ->helperText('Select which tour this departure is for'),

                        Select::make('departure_type')
                            ->options(['group' => 'Group', 'private' => 'Private'])
                            ->default('group')
                            ->required()
                            ->helperText('Group departures are shown in calendar, private are on-demand'),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('M d, Y')
                                    ->minDate(now())
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        // Auto-calculate end date based on tour duration
                                        $tourId = $get('tour_id');
                                        if ($state && $tourId) {
                                            $tour = \App\Models\Tour::find($tourId);
                                            if ($tour && $tour->duration_days) {
                                                $startDate = \Carbon\Carbon::parse($state);
                                                $endDate = $startDate->copy()->addDays($tour->duration_days - 1);
                                                $set('end_date', $endDate->format('Y-m-d'));
                                            }
                                        }
                                    })
                                    ->helperText('First day of the tour (end date will auto-calculate)'),

                                DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('M d, Y')
                                    ->after('start_date')
                                    ->helperText('Auto-calculated from start date + tour duration'),
                            ]),
                    ]),

                Section::make('Capacity & Booking')
                    ->description('Manage departure capacity and booking limits')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('max_pax')
                                    ->label('Maximum Guests')
                                    ->required()
                                    ->numeric()
                                    ->default(6)
                                    ->minValue(1)
                                    ->helperText('Total capacity for this departure'),

                                TextInput::make('min_pax')
                                    ->label('Minimum Guests')
                                    ->numeric()
                                    ->default(2)
                                    ->minValue(1)
                                    ->helperText('Min guests needed to run (optional)'),

                                TextInput::make('booked_pax')
                                    ->label('Booked Guests')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->disabled()
                                    ->helperText('Auto-updated when bookings are made'),
                            ]),

                        TextInput::make('price_per_person')
                            ->label('Price Override (Optional)')
                            ->numeric()
                            ->prefix('$')
                            ->helperText('Leave empty to use tour pricing tiers'),
                    ]),

                Section::make('Status & Notes')
                    ->description('Manage departure status and internal notes')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'open' => 'Open (Available)',
                                'guaranteed' => 'Guaranteed (Confirmed to run)',
                                'full' => 'Full (Sold out)',
                                'completed' => 'Completed (Tour finished)',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('open')
                            ->required()
                            ->helperText('Status shown on calendar: Open = blue, Guaranteed = green, Full = red'),

                        Textarea::make('notes')
                            ->label('Internal Notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Private notes (not shown to customers)'),
                    ]),
            ]);
    }
}
