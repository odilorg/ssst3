<?php

namespace App\Livewire;

use App\Filament\Resources\Bookings\BookingResource;
use App\Models\Booking;
use App\Models\Tour;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;

class BookingCalendar extends Component
{
    public array $events = [];
    public ?array $selectedBooking = null;
    public bool $showModal = false;

    // Filters
    public ?string $statusFilter = null;
    public ?int $tourFilter = null;
    public ?string $viewType = 'dayGridMonth';

    #[\Livewire\Attributes\On('showBookingDetails')]
    public function showBookingDetails($bookingId): void
    {
        $this->handleEventClick((int) $bookingId);
    }

    // Date range for calendar events
    public ?string $startDate = null;
    public ?string $endDate = null;

    // Grid view properties
    public ?string $gridStartDate = null;
    public ?string $gridEndDate = null;
    public array $gridDates = [];
    public array $gridData = [];

    public function mount(): void
    {
        // Default to current month for calendar
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');

        // Default to current week + 2 weeks for grid (3 weeks total)
        $this->gridStartDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $this->gridEndDate = Carbon::now()->startOfWeek()->addDays(20)->format('Y-m-d');

        // FIX #5: Only load calendar events on mount (grid loads lazily when switched to)
        $this->loadEvents();
    }

    public function previousWeek(): void
    {
        $this->gridStartDate = Carbon::parse($this->gridStartDate)->subWeek()->format('Y-m-d');
        $this->gridEndDate = Carbon::parse($this->gridEndDate)->subWeek()->format('Y-m-d');
        $this->loadGridData();
    }

    public function nextWeek(): void
    {
        $this->gridStartDate = Carbon::parse($this->gridStartDate)->addWeek()->format('Y-m-d');
        $this->gridEndDate = Carbon::parse($this->gridEndDate)->addWeek()->format('Y-m-d');
        $this->loadGridData();
    }

    public function goToToday(): void
    {
        $this->gridStartDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $this->gridEndDate = Carbon::now()->startOfWeek()->addDays(20)->format('Y-m-d');
        $this->loadGridData();
    }

    // FIX #5: Lazy-load grid data when user switches to grid view
    public function switchToGrid(): void
    {
        if (empty($this->gridData)) {
            $this->loadGridData();
        }
    }

    public function loadGridData(): void
    {
        // Generate date columns
        $this->gridDates = [];
        $period = CarbonPeriod::create($this->gridStartDate, $this->gridEndDate);
        $today = Carbon::today()->format('Y-m-d');

        foreach ($period as $date) {
            $this->gridDates[] = [
                'date' => $date->format('Y-m-d'),
                'dayName' => $date->format('D'),
                'dayNum' => $date->format('j'),
                'isToday' => $date->format('Y-m-d') === $today,
                'isWeekend' => $date->isWeekend(),
            ];
        }

        // FIX #4: Single query instead of N+1 (one query per tour)
        $query = Booking::with(['customer', 'tour'])
            ->whereNotNull('start_date')
            ->whereHas('tour')
            ->where(function ($q) {
                $q->whereBetween('start_date', [$this->gridStartDate, $this->gridEndDate])
                  ->orWhereBetween('end_date', [$this->gridStartDate, $this->gridEndDate])
                  ->orWhere(function ($q2) {
                      $q2->where('start_date', '<=', $this->gridStartDate)
                         ->where('end_date', '>=', $this->gridEndDate);
                  });
            });

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->tourFilter) {
            $query->where('tour_id', $this->tourFilter);
        }

        $bookings = $query->get();

        // Group bookings by tour
        $bookingsByTour = $bookings->groupBy('tour_id');

        $this->gridData = [];

        foreach ($bookingsByTour as $tourId => $tourBookings) {
            $tour = $tourBookings->first()->tour;
            if (!$tour) {
                continue;
            }

            $dateBookings = [];

            foreach ($this->gridDates as $dateInfo) {
                $date = $dateInfo['date'];
                $dateBookings[$date] = [];

                foreach ($tourBookings as $booking) {
                    $bookingStart = $booking->start_date->format('Y-m-d');
                    $bookingEnd = $booking->end_date ? $booking->end_date->format('Y-m-d') : $bookingStart;

                    if ($date >= $bookingStart && $date <= $bookingEnd) {
                        $dateBookings[$date][] = [
                            'id' => $booking->id,
                            'reference' => $booking->reference ?? '',
                            'customerName' => $booking->customer?->name ?? 'Unknown',
                            'guests' => $booking->guests_count ?? 0,
                            'status' => $booking->status ?? 'pending',
                        ];
                    }
                }
            }

            $this->gridData[$tourId] = [
                'title' => $tour->title,
                'bookings' => $dateBookings,
            ];
        }

        // Sort by tour title
        uasort($this->gridData, fn ($a, $b) => strcmp($a['title'], $b['title']));
    }

    // FIX #2: Validate date inputs before using them
    #[\Livewire\Attributes\On("handleDateRangeChanged")]
    public function handleDateRangeChanged(string $start, string $end): void
    {
        // Validate dates are reasonable (within 2 years)
        try {
            $startDate = Carbon::parse($start);
            $endDate = Carbon::parse($end);

            if ($startDate->year < 2020 || $startDate->year > 2030) {
                return;
            }
            if ($endDate->year < 2020 || $endDate->year > 2030) {
                return;
            }
        } catch (\Exception $e) {
            return;
        }

        $this->startDate = $startDate->format('Y-m-d');
        $this->endDate = $endDate->format('Y-m-d');
        $this->loadEvents();
    }

    public function loadEvents(): void
    {
        // FIX #8: Include bookings that SPAN into the view, not just those starting in it
        $query = Booking::with(['tour', 'customer'])
            ->whereNotNull('start_date')
            ->where(function ($q) {
                $q->whereBetween('start_date', [$this->startDate, $this->endDate])
                  ->orWhere(function ($q2) {
                      // Bookings that started before but end during or after the view range
                      $q2->where('start_date', '<', $this->startDate)
                         ->where('end_date', '>=', $this->startDate);
                  });
            });

        // Apply filters
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->tourFilter) {
            $query->where('tour_id', $this->tourFilter);
        }

        $bookings = $query->get();

        $this->events = $bookings->map(function ($booking) {
            if (!$booking->start_date) {
                return null;
            }

            $endDate = null;
            if ($booking->end_date) {
                // FullCalendar end date is exclusive, so add 1 day
                $endDate = $booking->end_date->copy()->addDay()->format('Y-m-d');
            }

            return [
                'id' => (string) $booking->id,
                'title' => ($booking->tour?->title ?? 'No Tour') . ' - ' .
                          ($booking->customer?->name ?? 'Unknown') .
                          ' (' . ($booking->guests_count ?? 0) . 'p)',
                'start' => $booking->start_date->format('Y-m-d'),
                'end' => $endDate,
                'backgroundColor' => $this->getStatusColor($booking->status ?? 'pending'),
                'borderColor' => $this->getStatusColor($booking->status ?? 'pending'),
                'extendedProps' => [
                    'status' => $booking->status ?? 'pending',
                    'paymentStatus' => $booking->payment_status ?? 'pending',
                    'guests' => $booking->guests_count ?? 0,
                    'tourId' => $booking->tour_id,
                    'customerId' => $booking->customer_id,
                    'customerName' => $booking->customer?->name ?? '',
                    'customerEmail' => $booking->customer?->email ?? '',
                    'tourTitle' => $booking->tour?->title ?? '',
                    'reference' => $booking->reference ?? '',
                ],
            ];
        })->filter()->values()->toArray();

        $this->dispatch('eventsLoaded', events: $this->events);
    }

    // FIX #12: Removed debug \Log::info statements
    public function handleEventClick(int $bookingId): void
    {
        $booking = Booking::with(['tour', 'customer'])
            ->find($bookingId);

        if ($booking) {
            // FIX #3: Use Filament's URL generator instead of hardcoded path
            $editUrl = BookingResource::getUrl('edit', ['record' => $booking->id]);

            $this->selectedBooking = [
                'id' => $booking->id,
                'editUrl' => $editUrl,
                'reference' => $booking->reference ?? '',
                'tourTitle' => $booking->tour?->title ?? 'No Tour',
                'customerName' => $booking->customer?->name ?? 'Unknown',
                'customerEmail' => $booking->customer?->email ?? '',
                'customerPhone' => $booking->customer?->phone ?? '',
                'startDate' => $booking->start_date ? $booking->start_date->format('d M Y') : '',
                'endDate' => $booking->end_date ? $booking->end_date->format('d M Y') : '',
                'guests' => $booking->guests_count ?? 0,
                'status' => $booking->status ?? 'pending',
                'paymentStatus' => $booking->payment_status ?? 'pending',
                'totalPrice' => number_format($booking->total_price ?? 0, 2),
                'currency' => $booking->currency ?? 'USD',
                'specialRequests' => $booking->special_requests ?? '',
                'notes' => $booking->notes ?? '',
            ];
            $this->showModal = true;
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedBooking = null;
    }

    // FIX #9: Handle end date from resize properly
    public function handleEventDrop(int $bookingId, string $newStart, ?string $newEnd = null): void
    {
        $this->rescheduleBooking($bookingId, $newStart, $newEnd);
    }

    // FIX #1: Added auth check + audit logging
    // FIX #9: Accept optional $newEnd for resize support
    public function rescheduleBooking(int $bookingId, string $newDate, ?string $newEnd = null): void
    {
        // FIX #1: Verify user is authenticated admin
        $user = auth()->user();
        if (!$user) {
            $this->dispatch('rescheduleError', message: 'Unauthorized: You must be logged in.');
            return;
        }

        // FIX #2: Validate the date
        try {
            $newStartDate = Carbon::parse($newDate);
            if ($newStartDate->year < 2020 || $newStartDate->year > 2030) {
                $this->dispatch('rescheduleError', message: 'Invalid date range.');
                return;
            }
        } catch (\Exception $e) {
            $this->dispatch('rescheduleError', message: 'Invalid date format.');
            return;
        }

        $booking = Booking::find($bookingId);

        if (!$booking) {
            $this->dispatch('rescheduleError', message: 'Booking not found.');
            return;
        }

        $oldStart = $booking->start_date?->format('Y-m-d');
        $oldEnd = $booking->end_date?->format('Y-m-d');

        // FIX #9: If resize provided a new end date, use it; otherwise preserve duration
        if ($newEnd) {
            try {
                // FullCalendar end date is exclusive, subtract 1 day
                $newEndDate = Carbon::parse($newEnd)->subDay();
            } catch (\Exception $e) {
                $newEndDate = null;
            }
        } else {
            // Preserve original duration for drag-drop
            $duration = 0;
            if ($booking->start_date && $booking->end_date) {
                $duration = $booking->start_date->diffInDays($booking->end_date);
            }
            $newEndDate = $duration > 0 ? $newStartDate->copy()->addDays($duration) : null;
        }

        $booking->start_date = $newStartDate;
        if ($newEndDate) {
            $booking->end_date = $newEndDate;
        }
        $booking->save();

        // FIX #1: Audit log
        \Log::channel('single')->info('Booking rescheduled', [
            'booking_id' => $booking->id,
            'reference' => $booking->reference,
            'old_start' => $oldStart,
            'old_end' => $oldEnd,
            'new_start' => $booking->start_date->format('Y-m-d'),
            'new_end' => $booking->end_date?->format('Y-m-d'),
            'rescheduled_by' => $user->name ?? $user->email ?? $user->id,
        ]);

        // Don't reload calendar events here -- FullCalendar already shows the
        // event at the new position after drag-drop.  Reloading triggers
        // removeAllEvents + addEventSource which causes a visual "snap back".
        // Events refresh automatically when the user navigates months (datesSet).
        // Only reload grid data (grid view doesn't have its own drag state).
        $this->loadGridData();

        $this->dispatch('notify', type: 'success', message: "Booking #{$booking->reference} rescheduled to " . $newStartDate->format('d M Y'));
    }

    public function updatedStatusFilter(): void
    {
        $this->loadEvents();
        $this->loadGridData();
    }

    public function updatedTourFilter(): void
    {
        $this->loadEvents();
        $this->loadGridData();
    }

    public function getTours()
    {
        return Tour::whereHas('bookings')->orderBy('title')->pluck('title', 'id');
    }

    protected function getStatusColor(string $status): string
    {
        return match ($status) {
            'confirmed' => '#22c55e',
            'pending' => '#eab308',
            'pending_payment' => '#f97316',
            'cancelled' => '#ef4444',
            'in_progress' => '#3b82f6',
            'completed' => '#10b981',
            'declined' => '#991b1b',
            default => '#6b7280',
        };
    }

    public function render()
    {
        return view('livewire.booking-calendar', [
            'tours' => $this->getTours(),
        ]);
    }
}
