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
        // Only force HTTPS if:
        // 1. We're in a web context (not CLI), AND
        // 2. The request is already HTTPS (via proxy headers or direct connection)
        // This prevents redirect loops by not forcing HTTPS when the request is HTTP
        if ($this->app->runningInConsole()) {
            return;
        }

        try {
            $request = request();
            if (!$request) {
                return;
            }

            // Check if behind a proxy (like Render) with X-Forwarded-Proto header
            if ($request->hasHeader('X-Forwarded-Proto')) {
                // Only force HTTPS if the proxy indicates the original request was HTTPS
                if ($request->header('X-Forwarded-Proto') === 'https') {
                    URL::forceScheme('https');
                }
            } elseif ($request->secure()) {
                // Direct HTTPS connection (not behind proxy)
                URL::forceScheme('https');
            }
        } catch (\Exception $e) {
            // Silently fail if request context is not available
            // This can happen during application bootstrapping
        }
    }
}
