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

        // Update departure booked_pax count
        $this->updateDepartureBookedPax($booking);
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

        // Update departure counts if status, pax_total, or departure_id changed
        if ($booking->wasChanged(['status', 'pax_total', 'departure_id'])) {
            // Update old departure if it changed
            if ($booking->wasChanged('departure_id') && $booking->getOriginal('departure_id')) {
                $oldDeparture = \App\Models\TourDeparture::find($booking->getOriginal('departure_id'));
                if ($oldDeparture) {
                    $oldDeparture->updateBookedPax();
                }
            }

            // Update current departure
            $this->updateDepartureBookedPax($booking);
        }
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        // Update departure count when booking is deleted
        $this->updateDepartureBookedPax($booking);
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
        // Update departure count when booking is force deleted
        $this->updateDepartureBookedPax($booking);
    }

    /**
     * Helper method to update departure booked_pax count
     */
    protected function updateDepartureBookedPax(Booking $booking): void
    {
        if ($booking->departure_id && $booking->departure) {
            $booking->departure->updateBookedPax();
        }
    }
}
