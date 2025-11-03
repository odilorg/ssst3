<?php

namespace App\Observers;

use App\Models\ItineraryItem;
use Illuminate\Support\Facades\Cache;

class ItineraryItemObserver
{
    /**
     * Handle the ItineraryItem "created" event.
     */
    public function created(ItineraryItem $itineraryItem): void
    {
        $this->clearItineraryCache($itineraryItem);
    }

    /**
     * Handle the ItineraryItem "updated" event.
     */
    public function updated(ItineraryItem $itineraryItem): void
    {
        $this->clearItineraryCache($itineraryItem);
    }

    /**
     * Handle the ItineraryItem "deleted" event.
     */
    public function deleted(ItineraryItem $itineraryItem): void
    {
        $this->clearItineraryCache($itineraryItem);
    }

    /**
     * Handle the ItineraryItem "restored" event.
     */
    public function restored(ItineraryItem $itineraryItem): void
    {
        $this->clearItineraryCache($itineraryItem);
    }

    /**
     * Handle the ItineraryItem "force deleted" event.
     */
    public function forceDeleted(ItineraryItem $itineraryItem): void
    {
        $this->clearItineraryCache($itineraryItem);
    }

    /**
     * Clear itinerary cache for the associated tour
     */
    protected function clearItineraryCache(ItineraryItem $itineraryItem): void
    {
        $tour = $itineraryItem->tour;

        if ($tour) {
            // Clear the main tour cache
            Cache::forget("tour.{$tour->slug}");

            // Itinerary is loaded directly via relationship,
            // so clearing tour cache is sufficient
        }
    }
}
