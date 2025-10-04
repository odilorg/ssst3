<?php

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Http\Middleware\MirrorConfigToSubpackages;
use Filament\Pages;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::domain(config('filament.domain'))
    ->prefix('tour_app/' . config('filament.path'))
    ->name('filament.')
    ->middleware([
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
        SubstituteBindings::class,
        DispatchServingFilamentEvent::class,
        MirrorConfigToSubpackages::class,
    ])
    ->group(function () {
        foreach (Filament::getPages() as \) {
            Route::get(\::getRoute(), \)->name(\::getRouteName());
        }

        Route::get('/', Pages\Home::class)->name('pages.home');
    });
