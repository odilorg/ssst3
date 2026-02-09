<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Guide;
use App\Models\Driver;
use App\Models\Tour;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;

class BookingCalendar extends Component
{
    public array $events = [];
    public array $resources = [];
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

        $this->loadEvents();
        $this->loadResources();
        $this->loadGridData();
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

        // Get all tours that have bookings
        $tours = Tour::whereHas('bookings')->orderBy('title')->get();

        // Build grid data: tours as rows, dates as columns
        $this->gridData = [];

        foreach ($tours as $tour) {
            // Skip if we have a tour filter and this isn't the selected tour
            if ($this->tourFilter && $tour->id != $this->tourFilter) {
                continue;
            }

            $tourBookings = [];

            // Get bookings for this tour in the date range
            $query = Booking::with(['customer'])
                ->where('tour_id', $tour->id)
                ->where(function ($q) {
                    $q->whereBetween('start_date', [$this->gridStartDate, $this->gridEndDate])
                      ->orWhereBetween('end_date', [$this->gridStartDate, $this->gridEndDate])
                      ->orWhere(function ($q2) {
                          $q2->where('start_date', '<=', $this->gridStartDate)
                             ->where('end_date', '>=', $this->gridEndDate);
                      });
                });

            // Apply status filter
            if ($this->statusFilter) {
                $query->where('status', $this->statusFilter);
            }

            $bookings = $query->get();

            // Map bookings to their dates
            foreach ($this->gridDates as $dateInfo) {
                $date = $dateInfo['date'];
                $tourBookings[$date] = [];

                foreach ($bookings as $booking) {
                    // Check if this booking spans this date
                    $bookingStart = $booking->start_date->format('Y-m-d');
                    $bookingEnd = $booking->end_date ? $booking->end_date->format('Y-m-d') : $bookingStart;

                    if ($date >= $bookingStart && $date <= $bookingEnd) {
                        $tourBookings[$date][] = [
                            'id' => $booking->id,
                            'reference' => $booking->reference ?? '',
                            'customerName' => $booking->customer?->name ?? 'Unknown',
                            'guests' => $booking->guests_count ?? 0,
                            'status' => $booking->status ?? 'pending',
                        ];
                    }
                }
            }

            $this->gridData[$tour->id] = [
                'title' => $tour->title,
                'bookings' => $tourBookings,
            ];
        }
    }

    #[\Livewire\Attributes\On("handleDateRangeChanged")]
    public function handleDateRangeChanged(string $start, string $end): void
    {
        $this->startDate = $start;
        $this->endDate = $end;
        $this->loadEvents();
    }

    public function loadEvents(): void
    {
        $query = Booking::with(['tour', 'customer'])
            ->whereNotNull('start_date')
            ->whereBetween('start_date', [$this->startDate, $this->endDate]);

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

    public function loadResources(): void
    {
        $guides = Guide::orderBy('name')->get()->map(fn($g) => [
            'id' => 'guide-' . $g->id,
            'title' => $g->name ?? 'Unnamed Guide',
            'type' => 'guide',
        ]);

        $drivers = Driver::orderBy('name')->get()->map(fn($d) => [
            'id' => 'driver-' . $d->id,
            'title' => $d->name ?? 'Unnamed Driver',
            'type' => 'driver',
        ]);

        $this->resources = collect([
            ['id' => 'unassigned', 'title' => 'Unassigned', 'type' => 'none']
        ])
            ->merge($guides)
            ->merge($drivers)
            ->toArray();

        $this->dispatch('resourcesLoaded', resources: $this->resources);
    }

    public function handleEventClick(int $bookingId): void
    {
        \Log::info('handleEventClick called', ['bookingId' => $bookingId]);

        $booking = Booking::with(['tour', 'customer'])
            ->find($bookingId);

        \Log::info('Booking found', ['found' => $booking !== null]);

        if ($booking) {
            $this->selectedBooking = [
                'id' => $booking->id,
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
            \Log::info('Modal should show', ['showModal' => $this->showModal]);
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedBooking = null;
    }

    public function handleEventDrop(int $bookingId, string $newStart, ?string $newEnd = null): void
    {
        $this->rescheduleBooking($bookingId, $newStart);
    }

    public function rescheduleBooking(int $bookingId, string $newDate): void
    {
        $booking = Booking::find($bookingId);

        if (!$booking) {
            session()->flash('error', 'Booking not found');
            return;
        }

        // Calculate duration to maintain multi-day tours
        $duration = 0;
        if ($booking->start_date && $booking->end_date) {
            $duration = $booking->start_date->diffInDays($booking->end_date);
        }

        // Update dates
        $newStartDate = Carbon::parse($newDate);
        $booking->start_date = $newStartDate;

        if ($duration > 0) {
            $booking->end_date = $newStartDate->copy()->addDays($duration);
        }

        $booking->save();

        // Reload grid data
        $this->loadGridData();
        $this->loadEvents();

        // Flash success message
        session()->flash('message', "Booking #{$booking->reference} rescheduled to " . $newStartDate->format('d M Y'));
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
