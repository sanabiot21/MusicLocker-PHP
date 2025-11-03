<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
        
        // Trust proxy headers for HTTPS detection (required for Render deployment)
        // This allows Laravel to detect HTTPS from X-Forwarded-Proto header
        $middleware->trustProxies(
            at: '*',
            headers:
                \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR
                | \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_HOST
                | \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PORT
                | \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PROTO,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle database connection errors gracefully
        $exceptions->render(function (\Illuminate\Database\QueryException $e, $request) {
            // Check if it's a connection error
            if (str_contains($e->getMessage(), 'Connection refused') || 
                str_contains($e->getMessage(), 'could not connect') ||
                str_contains($e->getMessage(), 'server closed the connection') ||
                str_contains($e->getMessage(), 'SQLSTATE[08006]')) {
                
                // Log the error
                \Log::error('Database connection error: ' . $e->getMessage());
                
                // Try to reconnect
                try {
                    \DB::reconnect();
                    
                    // For API requests, return JSON
                    if ($request->expectsJson() || $request->is('api/*')) {
                        return response()->json([
                            'error' => 'Database connection issue. Please try again.',
                            'retry' => true
                        ], 503);
                    }
                    
                    // For web requests, redirect back with error
                    if ($request->isMethod('GET')) {
                        return redirect()->back()->withErrors(['error' => 'Connection issue. Please try again.']);
                    }
                    
                    // For POST requests, redirect back
                    return redirect()->back()->withInput()->withErrors(['error' => 'Connection issue. Please try again.']);
                } catch (\Exception $reconnectError) {
                    \Log::error('Database reconnection failed: ' . $reconnectError->getMessage());
                    
                    // For API requests
                    if ($request->expectsJson() || $request->is('api/*')) {
                        return response()->json([
                            'error' => 'Database connection error. Please try again in a moment.'
                        ], 503);
                    }
                    
                    // For web requests
                    return response()->view('errors.500', [
                        'message' => 'Database connection error. Please try again in a moment.'
                    ], 503);
                }
            }
        });
    })->create();

