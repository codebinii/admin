<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Log;

/**
 * Centralised file-based tracing helper.
 *
 * Channels:
 *   requests   → storage/logs/requests/laravel-YYYY-MM-DD.log
 *   auth_events → storage/logs/auth/laravel-YYYY-MM-DD.log
 *   app_events  → storage/logs/app/laravel-YYYY-MM-DD.log
 */
final class AppLog
{
    private const SENSITIVE = [
        'password', 'password_confirmation', 'current_password',
        'token', 'api_key', 'secret', 'otp', 'code',
    ];

    /** HTTP request / response tracing. */
    public static function request(string $event, array $context = []): void
    {
        if (! config('logging.trace_enabled', true)) {
            return;
        }

        Log::channel('requests')->info($event, $context);
    }

    /** Authentication events (login, logout, register, OTP…). */
    public static function auth(string $event, array $context = []): void
    {
        if (! config('logging.trace_enabled', true)) {
            return;
        }

        Log::channel('auth_events')->info($event, $context);
    }

    /** Business / domain events (hash, dv, saas…). */
    public static function event(string $event, array $context = []): void
    {
        if (! config('logging.trace_enabled', true)) {
            return;
        }

        Log::channel('app_events')->info($event, $context);
    }

    /** Removes or masks sensitive fields before logging. */
    public static function mask(array $data): array
    {
        foreach (self::SENSITIVE as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = '***';
            }
        }

        return $data;
    }
}
