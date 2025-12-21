<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * Path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        parent::boot();
        $this->configureRateLimiting();

        $this->routes(function () {

            // ================================
            // API ROUTES (NO CSRF)
            // ================================
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // ================================
            // WEB ROUTES (WITH CSRF)
            // ================================
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
    protected function configureRateLimiting(): void
    {
        // Submit order: ketat per IP + token
        RateLimiter::for('embed-submit', function (Request $request) {
            $token = (string) $request->route('token');
            $ip = (string) $request->ip();

            return Limit::perMinute(8)->by("embed-submit|{$token}|{$ip}");
        });

        // Issue nonce: lebih longgar
        RateLimiter::for('embed-nonce', function (Request $request) {
            $token = (string) $request->route('token');
            $ip = (string) $request->ip();

            return Limit::perMinute(30)->by("embed-nonce|{$token}|{$ip}");
        });
    }
}
