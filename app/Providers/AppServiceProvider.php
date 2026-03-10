<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    private function configureRateLimiting(): void
    {
        // 10 intentos por minuto por IP en login
        RateLimiter::for('auth.login', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // 5 registros por minuto por IP
        RateLimiter::for('auth.register', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // 6 reenvíos de verificación por minuto por usuario
        RateLimiter::for('auth.email.resend', function (Request $request) {
            return Limit::perMinute(6)->by($request->user()?->id ?: $request->ip());
        });
    }
}
