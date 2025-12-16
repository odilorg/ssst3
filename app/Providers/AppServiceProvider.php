<?php

namespace App\Providers;

use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\City;
use App\Models\ItineraryItem;
use App\Models\Tour;
use App\Models\TourExtra;
use App\Models\TourFaq;
use App\Models\Transport;
use App\Models\TransportInstancePrice;
use App\Observers\BookingObserver;
use App\Observers\ImageConversionObserver;
use App\Observers\ItineraryItemObserver;
use App\Observers\TourExtraObserver;
use App\Observers\TourFaqObserver;
use App\Observers\TourObserver;
use App\Observers\TransportObserver;
use App\Observers\TransportInstancePriceObserver;
use Illuminate\Support\ServiceProvider;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ru', 'en', 'uz'])
                ->visible(outsidePanels: true)
                ->labels([
                    'ru' => 'Русский',
                    'en' => 'English',
                    'uz' => 'O\'zbekcha',
                ]);
        });

        // Register observers
        Booking::observe(BookingObserver::class);
        Transport::observe(TransportObserver::class);
        TransportInstancePrice::observe(TransportInstancePriceObserver::class);
        Tour::observe(TourObserver::class);
        TourFaq::observe(TourFaqObserver::class);
        ItineraryItem::observe(ItineraryItemObserver::class);
        TourExtra::observe(TourExtraObserver::class);

        // Register image conversion observer for models with images
        Tour::observe(ImageConversionObserver::class);
        BlogPost::observe(ImageConversionObserver::class);
        City::observe(ImageConversionObserver::class);
    }
}
