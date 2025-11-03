<?php

namespace App\Observers;

use App\Models\TourFaq;
use Illuminate\Support\Facades\Cache;

class TourFaqObserver
{
    /**
     * Handle the TourFaq "created" event.
     */
    public function created(TourFaq $tourFaq): void
    {
        $this->clearFaqCache($tourFaq);
    }

    /**
     * Handle the TourFaq "updated" event.
     */
    public function updated(TourFaq $tourFaq): void
    {
        $this->clearFaqCache($tourFaq);
    }

    /**
     * Handle the TourFaq "deleted" event.
     */
    public function deleted(TourFaq $tourFaq): void
    {
        $this->clearFaqCache($tourFaq);
    }

    /**
     * Handle the TourFaq "restored" event.
     */
    public function restored(TourFaq $tourFaq): void
    {
        $this->clearFaqCache($tourFaq);
    }

    /**
     * Handle the TourFaq "force deleted" event.
     */
    public function forceDeleted(TourFaq $tourFaq): void
    {
        $this->clearFaqCache($tourFaq);
    }

    /**
     * Clear FAQ cache for the associated tour
     */
    protected function clearFaqCache(TourFaq $tourFaq): void
    {
        // Get the tour to access its slug
        $tour = $tourFaq->tour;

        if ($tour) {
            // Clear the FAQ cache for this specific tour
            Cache::forget("tour.{$tour->slug}.faqs");

            // Also clear the main tour cache to ensure consistency
            Cache::forget("tour.{$tour->slug}");
        }
    }
}
