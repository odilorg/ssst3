<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Tour;
use App\Models\TourFaq;
use App\Models\Transport;
use App\Models\TransportInstancePrice;
use App\Observers\BookingObserver;
use App\Observers\TourFaqObserver;
use App\Observers\TourObserver;
use App\Observers\TransportObserver;
use App\Observers\TransportInstancePriceObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        Booking::observe(BookingObserver::class);
        Transport::observe(TransportObserver::class);
        TransportInstancePrice::observe(TransportInstancePriceObserver::class);
        Tour::observe(TourObserver::class);
        TourFaq::observe(TourFaqObserver::class);
    }
}
