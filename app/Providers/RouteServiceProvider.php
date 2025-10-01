<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/home';

    public function boot()
    {
        \->configureRateLimiting();

        \->routes(function () {
            Route::prefix('tour_app')
                 ->middleware('web')
                 ->namespace(\->namespace)
                 ->group(base_path('routes/web.php'));

            Route::prefix('tour_app')
                 ->middleware('web')
                 ->group(base_path('routes/filament.php'));

            Route::prefix('tour_app/api')
                 ->middleware('api')
                 ->namespace(\->namespace)
                 ->group(base_path('routes/api.php'));
        });
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request \) {
            return Limit::perMinute(60)->by(optional(\->user())->id ?: \->ip());
        });
    }
}
