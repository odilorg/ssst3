<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Guide;
use App\Models\Driver;
use App\Models\Tour;
use Carbon\Carbon;
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

    // Date range for fetching events
    public ?string $startDate = null;
    public ?string $endDate = null;

    protected $listeners = [
        'dateRangeChanged' => 'handleDateRangeChanged',
        'eventClicked' => 'handleEventClick',
        'eventDropped' => 'handleEventDrop',
    ];

    public function mount(): void
    {
        // Default to current month
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->loadEvents();
        $this->loadResources();
    }

    public function handleDateRangeChanged(string $start, string $end): void
    {
        $this->startDate = $start;
        $this->endDate = $end;
        $this->loadEvents();
    }

    public function loadEvents(): void
    {
        $query = Booking::with(['tour', 'customer', 'itineraryItems.assignments.assignable'])
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
            // Get assigned guide/driver for resource view
            $resourceIds = [];
            foreach ($booking->itineraryItems as $item) {
                foreach ($item->assignments as $assignment) {
                    if ($assignment->assignable_type === 'App\\Models\\Guide') {
                        $resourceIds[] = 'guide-' . $assignment->assignable_id;
                    } elseif ($assignment->assignable_type === 'App\\Models\\Driver') {
                        $resourceIds[] = 'driver-' . $assignment->assignable_id;
                    }
                }
            }

            return [
                'id' => $booking->id,
                'title' => ($booking->tour?->title ?? 'No Tour') . ' - ' .
                          ($booking->customer?->name ?? 'Unknown') .
                          ' (' . $booking->guests_count . 'p)',
                'start' => $booking->start_date->format('Y-m-d'),
                'end' => $booking->end_date ? $booking->end_date->addDay()->format('Y-m-d') : null,
                'backgroundColor' => $this->getStatusColor($booking->status),
                'borderColor' => $this->getStatusColor($booking->status),
                'resourceIds' => array_unique($resourceIds) ?: ['unassigned'],
                'extendedProps' => [
                    'status' => $booking->status,
                    'paymentStatus' => $booking->payment_status,
                    'guests' => $booking->guests_count,
                    'tourId' => $booking->tour_id,
                    'customerId' => $booking->customer_id,
                    'customerName' => $booking->customer?->name,
                    'customerEmail' => $booking->customer?->email,
                    'tourTitle' => $booking->tour?->title,
                    'reference' => $booking->reference,
                ],
            ];
        })->toArray();

        $this->dispatch('eventsLoaded', events: $this->events);
    }

    public function loadResources(): void
    {
        $guides = Guide::orderBy('name')->get()->map(fn($g) => [
            'id' => 'guide-' . $g->id,
            'title' => $g->name,
            'type' => 'guide',
        ]);

        $drivers = Driver::orderBy('name')->get()->map(fn($d) => [
            'id' => 'driver-' . $d->id,
            'title' => $d->name,
            'type' => 'driver',
        ]);

        // Add unassigned resource
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
        $booking = Booking::with(['tour', 'customer', 'itineraryItems.assignments.assignable'])
            ->find($bookingId);

        if ($booking) {
            $this->selectedBooking = [
                'id' => $booking->id,
                'reference' => $booking->reference,
                'tourTitle' => $booking->tour?->title ?? 'No Tour',
                'customerName' => $booking->customer?->name ?? 'Unknown',
                'customerEmail' => $booking->customer?->email,
                'customerPhone' => $booking->customer?->phone,
                'startDate' => $booking->start_date->format('d M Y'),
                'endDate' => $booking->end_date?->format('d M Y'),
                'guests' => $booking->guests_count,
                'status' => $booking->status,
                'paymentStatus' => $booking->payment_status,
                'totalPrice' => number_format($booking->total_price ?? 0, 2),
                'currency' => $booking->currency ?? 'USD',
                'specialRequests' => $booking->special_requests,
                'notes' => $booking->notes,
            ];
            $this->showModal = true;
        }
    }

    public function handleEventDrop(int $bookingId, string $newStart, ?string $newEnd): void
    {
        $booking = Booking::find($bookingId);

        if (!$booking) {
            $this->dispatch('notify', type: 'error', message: 'Booking not found');
            return;
        }

        // Calculate duration to maintain it
        $duration = $booking->start_date->diffInDays($booking->end_date);

        $booking->start_date = Carbon::parse($newStart);
        $booking->end_date = $newEnd
            ? Carbon::parse($newEnd)->subDay()
            : Carbon::parse($newStart)->addDays($duration);
        $booking->save();

        $this->loadEvents();

        $this->dispatch('notify',
            type: 'success',
            message: "Booking #{$booking->reference} rescheduled to " . $booking->start_date->format('d M Y')
        );
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedBooking = null;
    }

    public function updatedStatusFilter(): void
    {
        $this->loadEvents();
    }

    public function updatedTourFilter(): void
    {
        $this->loadEvents();
    }

    public function getTours()
    {
        return Tour::whereHas('bookings')->orderBy('title')->pluck('title', 'id');
    }

    protected function getStatusColor(string $status): string
    {
        return match ($status) {
            'confirmed' => '#22c55e',      // green
            'pending' => '#eab308',         // yellow
            'pending_payment' => '#f97316', // orange
            'cancelled' => '#ef4444',       // red
            default => '#6b7280',           // gray
        };
    }

    public function render()
    {
        return view('livewire.booking-calendar', [
            'tours' => $this->getTours(),
        ]);
    }
}
