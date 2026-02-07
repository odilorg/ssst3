<?php

namespace App\Filament\Resources\TourDepartures\Pages;

use App\Filament\Resources\TourDepartures\TourDepartureResource;
use App\Models\Tour;
use App\Models\TourDeparture;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\DB;

class ListTourDepartures extends ListRecords
{
    protected static string $resource = TourDepartureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('bulk_create')
                ->label('Bulk Create Departures')
                ->icon('heroicon-o-calendar-days')
                ->color('success')
                ->modalHeading('Bulk Create Departures')
                ->modalDescription('Generate multiple departure dates at once for a tour.')
                ->modalWidth('3xl')
                ->form([
                    Section::make('Tour Selection')
                        ->schema([
                            Select::make('tour_id')
                                ->label('Tour')
                                ->options(fn () => Tour::where('is_active', true)->pluck('title', 'id'))
                                ->searchable()
                                ->required()
                                ->helperText('Select the tour to create departures for'),
                        ]),

                    Section::make('Date Generation')
                        ->schema([
                            Radio::make('mode')
                                ->label('Generation Mode')
                                ->options([
                                    'pattern' => 'Pattern (recurring schedule)',
                                    'manual' => 'Manual (pick individual dates)',
                                ])
                                ->default('pattern')
                                ->required()
                                ->live(),

                            // === Pattern Mode Fields ===
                            Grid::make(2)
                                ->schema([
                                    DatePicker::make('range_start')
                                        ->label('From Date')
                                        ->required()
                                        ->native(false)
                                        ->displayFormat('M d, Y')
                                        ->minDate(now()),

                                    DatePicker::make('range_end')
                                        ->label('To Date')
                                        ->required()
                                        ->native(false)
                                        ->displayFormat('M d, Y')
                                        ->after('range_start'),
                                ])
                                ->visible(fn ($get) => $get('mode') === 'pattern'),

                            Select::make('frequency')
                                ->label('Frequency')
                                ->options([
                                    'daily' => 'Daily',
                                    'weekly' => 'Weekly',
                                    'biweekly' => 'Every 2 weeks',
                                    'monthly' => 'Monthly (specific day)',
                                    'custom' => 'Custom interval (every N days)',
                                ])
                                ->default('weekly')
                                ->required()
                                ->live()
                                ->visible(fn ($get) => $get('mode') === 'pattern'),

                            CheckboxList::make('days_of_week')
                                ->label('Days of Week')
                                ->options([
                                    1 => 'Monday',
                                    2 => 'Tuesday',
                                    3 => 'Wednesday',
                                    4 => 'Thursday',
                                    5 => 'Friday',
                                    6 => 'Saturday',
                                    0 => 'Sunday',
                                ])
                                ->columns(4)
                                ->required()
                                ->visible(fn ($get) => $get('mode') === 'pattern' && in_array($get('frequency'), ['weekly', 'biweekly'])),

                            TextInput::make('day_of_month')
                                ->label('Day of Month')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(28)
                                ->required()
                                ->helperText('1-28 (avoiding month-end edge cases)')
                                ->visible(fn ($get) => $get('mode') === 'pattern' && $get('frequency') === 'monthly'),

                            TextInput::make('custom_interval_days')
                                ->label('Interval (days)')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(90)
                                ->required()
                                ->helperText('Departure every N days starting from "From Date"')
                                ->visible(fn ($get) => $get('mode') === 'pattern' && $get('frequency') === 'custom'),

                            // === Manual Mode Fields ===
                            Repeater::make('manual_dates')
                                ->label('Departure Dates')
                                ->schema([
                                    DatePicker::make('date')
                                        ->label('Start Date')
                                        ->required()
                                        ->native(false)
                                        ->displayFormat('M d, Y')
                                        ->minDate(now()),
                                ])
                                ->minItems(1)
                                ->maxItems(50)
                                ->defaultItems(1)
                                ->addActionLabel('Add Date')
                                ->visible(fn ($get) => $get('mode') === 'manual'),
                        ]),

                    Section::make('Departure Settings')
                        ->description('Applied to all generated departures')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Select::make('departure_type')
                                        ->options([
                                            'group' => 'Group',
                                            'private' => 'Private',
                                        ])
                                        ->default('group')
                                        ->required(),

                                    Select::make('status')
                                        ->options([
                                            'open' => 'Open',
                                            'guaranteed' => 'Guaranteed',
                                        ])
                                        ->default('open')
                                        ->required(),
                                ]),

                            Grid::make(3)
                                ->schema([
                                    TextInput::make('max_pax')
                                        ->label('Max Guests')
                                        ->numeric()
                                        ->default(6)
                                        ->minValue(1)
                                        ->required(),

                                    TextInput::make('min_pax')
                                        ->label('Min Guests')
                                        ->numeric()
                                        ->default(2)
                                        ->minValue(1),

                                    TextInput::make('price_per_person')
                                        ->label('Price Override')
                                        ->numeric()
                                        ->prefix('$')
                                        ->helperText('Leave empty to use tour pricing'),
                                ]),

                            Textarea::make('notes')
                                ->label('Internal Notes')
                                ->rows(2)
                                ->columnSpanFull(),
                        ]),
                ])
                ->action(function (array $data) {
                    $tour = Tour::findOrFail($data['tour_id']);
                    $durationDays = $tour->duration_days ?? 1;

                    // Resolve dates based on mode
                    $startDates = [];

                    if ($data['mode'] === 'pattern') {
                        $rangeStart = Carbon::parse($data['range_start']);
                        $rangeEnd = Carbon::parse($data['range_end']);
                        $frequency = $data['frequency'];

                        if ($frequency === 'daily') {
                            $current = $rangeStart->copy();
                            while ($current->lte($rangeEnd) && count($startDates) < 100) {
                                $startDates[] = $current->copy();
                                $current->addDay();
                            }
                        } elseif ($frequency === 'weekly' || $frequency === 'biweekly') {
                            $daysOfWeek = array_map('intval', $data['days_of_week'] ?? []);
                            $step = $frequency === 'biweekly' ? 2 : 1;

                            $current = $rangeStart->copy();
                            $weekCounter = 0;
                            $lastWeek = null;

                            while ($current->lte($rangeEnd) && count($startDates) < 100) {
                                $currentWeek = $current->weekOfYear . '-' . $current->year;

                                if ($lastWeek !== null && $currentWeek !== $lastWeek) {
                                    $weekCounter++;
                                }

                                if (in_array($current->dayOfWeek, $daysOfWeek)) {
                                    if ($frequency === 'weekly' || $weekCounter % $step === 0) {
                                        $startDates[] = $current->copy();
                                    }
                                }

                                $lastWeek = $currentWeek;
                                $current->addDay();
                            }
                        } elseif ($frequency === 'monthly') {
                            $dayOfMonth = (int) $data['day_of_month'];
                            $current = $rangeStart->copy()->day($dayOfMonth);

                            if ($current->lt($rangeStart)) {
                                $current->addMonth();
                            }

                            while ($current->lte($rangeEnd) && count($startDates) < 100) {
                                $startDates[] = $current->copy();
                                $current->addMonth();
                            }
                        } elseif ($frequency === 'custom') {
                            $interval = (int) $data['custom_interval_days'];
                            $current = $rangeStart->copy();

                            while ($current->lte($rangeEnd) && count($startDates) < 100) {
                                $startDates[] = $current->copy();
                                $current->addDays($interval);
                            }
                        }
                    } else {
                        // Manual mode
                        foreach ($data['manual_dates'] ?? [] as $entry) {
                            if (!empty($entry['date'])) {
                                $startDates[] = Carbon::parse($entry['date']);
                            }
                        }
                    }

                    // Safety limit
                    if (count($startDates) > 100) {
                        $startDates = array_slice($startDates, 0, 100);
                    }

                    if (empty($startDates)) {
                        Notification::make()
                            ->warning()
                            ->title('No Dates Generated')
                            ->body('No departure dates matched the selected pattern. Check your date range and frequency settings.')
                            ->send();
                        return;
                    }

                    // Check for existing departures (duplicate prevention)
                    $existingDates = TourDeparture::where('tour_id', $tour->id)
                        ->whereIn('start_date', array_map(fn ($d) => $d->format('Y-m-d'), $startDates))
                        ->pluck('start_date')
                        ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
                        ->toArray();

                    $created = 0;
                    $skipped = 0;

                    DB::transaction(function () use ($startDates, $existingDates, $tour, $durationDays, $data, &$created, &$skipped) {
                        foreach ($startDates as $startDate) {
                            $dateStr = $startDate->format('Y-m-d');

                            if (in_array($dateStr, $existingDates)) {
                                $skipped++;
                                continue;
                            }

                            $endDate = $startDate->copy()->addDays($durationDays - 1);

                            TourDeparture::create([
                                'tour_id' => $tour->id,
                                'start_date' => $dateStr,
                                'end_date' => $endDate->format('Y-m-d'),
                                'max_pax' => $data['max_pax'],
                                'min_pax' => $data['min_pax'] ?? null,
                                'booked_pax' => 0,
                                'price_per_person' => $data['price_per_person'] ?? null,
                                'status' => $data['status'],
                                'departure_type' => $data['departure_type'],
                                'notes' => $data['notes'] ?? null,
                            ]);

                            $created++;
                        }
                    });

                    $message = "{$created} departure(s) created for \"{$tour->title}\".";
                    if ($skipped > 0) {
                        $message .= " {$skipped} duplicate(s) skipped.";
                    }

                    Notification::make()
                        ->success()
                        ->title('Bulk Create Complete')
                        ->body($message)
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}
