<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\BookingItinerarySync;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        // Sync itinerary when booking is first created
        BookingItinerarySync::fromTripTemplate($booking);
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // Re-sync when tour_id or start_date changes
        if ($booking->wasChanged(['tour_id', 'start_date'])) {
            BookingItinerarySync::fromTripTemplate($booking);
        }
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        //
    }
}
