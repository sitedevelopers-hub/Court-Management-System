<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * Development-only middleware: clears the config and view caches on every
 * request so edits to views/config take effect immediately without running
 * `php artisan view:clear` / `config:clear` by hand. Registered in
 * bootstrap/app.php only when APP_ENV=local.
 */
class RefreshDevCaches
{
    public function handle(Request $request, Closure $next)
    {
        // Only active in local development — production stays fully cached.
        if (!app()->environment('local')) {
            return $next($request);
        }

        try {
            Artisan::call('config:clear');
            Artisan::call('view:clear');
        } catch (\Throwable $e) {
            // Never break a request because of cache clearing.
        }

        return $next($request);
    }
}
