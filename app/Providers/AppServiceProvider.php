<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Transport;
use App\Observers\BookingObserver;
use App\Observers\TransportObserver;
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
    }
}
