<?php

namespace App\Observers;

use App\Models\Tour;
use Illuminate\Support\Facades\Cache;

class TourObserver
{
    /**
     * Handle the Tour "created" event.
     */
    public function created(Tour $tour): void
    {
        $this->clearTourCache($tour);
    }

    /**
     * Handle the Tour "updated" event.
     */
    public function updated(Tour $tour): void
    {
        $this->clearTourCache($tour);
    }

    /**
     * Handle the Tour "deleted" event.
     */
    public function deleted(Tour $tour): void
    {
        $this->clearTourCache($tour);
    }

    /**
     * Handle the Tour "restored" event.
     */
    public function restored(Tour $tour): void
    {
        $this->clearTourCache($tour);
    }

    /**
     * Handle the Tour "force deleted" event.
     */
    public function forceDeleted(Tour $tour): void
    {
        $this->clearTourCache($tour);
    }

    /**
     * Clear all caches related to this tour
     */
    protected function clearTourCache(Tour $tour): void
    {
        // Clear specific tour cache
        Cache::forget("tour.{$tour->slug}");

        // Clear tours list cache
        Cache::forget('tours.list');

        // Clear tour-specific section caches
        Cache::forget("tour.{$tour->slug}.faqs");
        Cache::forget("tour.{$tour->slug}.extras");

        // Clear review caches (pages 1-10, should cover most cases)
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("tour.{$tour->slug}.reviews.page.{$i}");
        }
    }
}
