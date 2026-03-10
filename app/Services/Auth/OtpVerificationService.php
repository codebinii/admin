<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\ConfigDefecto;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

abstract class OtpVerificationService
{
    private const CACHE_CONFIG     = 'auth_config_defecto';
    private const CACHE_CONFIG_TTL = 60; // minutes

    protected const RESEND_LOCK_MINUTES = 30;

    // ──────────────────────────────────────────────────────────────
    // Contract — subclasses define channel-specific behavior
    // ──────────────────────────────────────────────────────────────

    /** Cache key prefix, e.g. 'whatsapp' | 'phone' */
    abstract protected function channel(): string;

    /** User model field that holds the phone/whatsapp number */
    abstract protected function userPhoneField(): string;

    /** User model field to stamp on successful verification */
    abstract protected function verifiedAtField(): string;

    /** Send the OTP via the channel-specific external API */
    abstract protected function dispatch(ConfigDefecto $config, string $to, string $otp): void;

    // ──────────────────────────────────────────────────────────────
    // Public API
    // ──────────────────────────────────────────────────────────────

    /**
     * Generate OTP, persist in cache and dispatch via channel.
     *
     * @return array{otp_expires_in: int, resend_in: int}
     * @throws \RuntimeException when the external API call fails
     */
    public function sendOtp(User $user): array
    {
        $config = $this->getConfig();
        $otp    = $this->generateOtp();
        $ttl    = (int) $config->tiempo_otp;

        Cache::put($this->cacheKey($user->id), $otp, now()->addMinutes($ttl));

        // Store expiry timestamp so remaining minutes can be computed precisely
        Cache::put(
            $this->lockKey($user->id),
            now()->addMinutes(self::RESEND_LOCK_MINUTES)->timestamp,
            now()->addMinutes(self::RESEND_LOCK_MINUTES),
        );

        $field = $this->userPhoneField();
        $this->dispatch($config, $this->normalizeNumber($user->$field), $otp);

        return [
            'otp_expires_in' => $ttl,
            'resend_in'      => self::RESEND_LOCK_MINUTES,
        ];
    }

    /**
     * Validate the submitted OTP and stamp the verified_at field.
     */
    public function verifyOtp(User $user, string $code): bool
    {
        $key    = $this->cacheKey($user->id);
        $stored = Cache::get($key);

        if ($stored === null || $stored !== $code) {
            return false;
        }

        Cache::forget($key);
        $user->update([$this->verifiedAtField() => now()]);

        return true;
    }

    /**
     * Returns remaining resend lock minutes, or 0 if lock is not active.
     */
    public function resendLockRemainingMinutes(User $user): int
    {
        $expiresAt = Cache::get($this->lockKey($user->id));

        if ($expiresAt === null) {
            return 0;
        }

        return (int) max(1, ceil(($expiresAt - now()->timestamp) / 60));
    }

    public function clearResendLock(User $user): void
    {
        Cache::forget($this->lockKey($user->id));
    }

    // ──────────────────────────────────────────────────────────────
    // Internals
    // ──────────────────────────────────────────────────────────────

    protected function getConfig(): ConfigDefecto
    {
        return Cache::remember(
            self::CACHE_CONFIG,
            now()->addMinutes(self::CACHE_CONFIG_TTL),
            fn () => ConfigDefecto::main(),
        );
    }

    protected function normalizeNumber(string $number): string
    {
        return preg_replace('/[^0-9]/', '', $number);
    }

    private function generateOtp(): string
    {
        return (string) random_int(100000, 999999);
    }

    private function cacheKey(int|string $userId): string
    {
        return "{$this->channel()}_otp:{$userId}";
    }

    private function lockKey(int|string $userId): string
    {
        return "{$this->channel()}_resend_lock:{$userId}";
    }
}
