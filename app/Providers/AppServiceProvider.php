<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS di production environment
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Force HTTPS untuk ngrok tunneling (development)
        if (str_contains(request()->getHost(), 'ngrok')) {
            URL::forceScheme('https');
        }
    }
}
