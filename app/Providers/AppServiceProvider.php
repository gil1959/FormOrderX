<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\View\Components\AppNavbar;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blade::component('app-navbar', AppNavbar::class);

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
