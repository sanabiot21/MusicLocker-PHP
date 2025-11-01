<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Force HTTPS URLs for assets when deployed behind a proxy (e.g., Render)
        // This ensures asset() helper generates HTTPS URLs, fixing mixed content errors
        // Check if request is secure via proxy headers or if in production
        if ($this->app->environment('production') 
            || request()->header('X-Forwarded-Proto') === 'https' 
            || request()->header('X-Forwarded-Ssl') === 'on'
            || request()->secure()) {
            URL::forceScheme('https');
        }
    }
}
