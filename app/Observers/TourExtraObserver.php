<?php

namespace App\Observers;

use App\Models\TourExtra;
use Illuminate\Support\Facades\Cache;

class TourExtraObserver
{
    /**
     * Handle the TourExtra "created" event.
     */
    public function created(TourExtra $tourExtra): void
    {
        $this->clearExtraCache($tourExtra);
    }

    /**
     * Handle the TourExtra "updated" event.
     */
    public function updated(TourExtra $tourExtra): void
    {
        $this->clearExtraCache($tourExtra);
    }

    /**
     * Handle the TourExtra "deleted" event.
     */
    public function deleted(TourExtra $tourExtra): void
    {
        $this->clearExtraCache($tourExtra);
    }

    /**
     * Handle the TourExtra "restored" event.
     */
    public function restored(TourExtra $tourExtra): void
    {
        $this->clearExtraCache($tourExtra);
    }

    /**
     * Handle the TourExtra "force deleted" event.
     */
    public function forceDeleted(TourExtra $tourExtra): void
    {
        $this->clearExtraCache($tourExtra);
    }

    /**
     * Clear extra cache for the associated tour
     */
    protected function clearExtraCache(TourExtra $tourExtra): void
    {
        $tour = $tourExtra->tour;

        if ($tour) {
            // Clear the extras cache for this specific tour
            Cache::forget("tour.{$tour->slug}.extras");

            // Also clear the main tour cache to ensure consistency
            Cache::forget("tour.{$tour->slug}");
        }
    }
}
