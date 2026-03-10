<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\CanalSoporte;
use App\Models\ConfigDefecto;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class WhatsAppVerificationService
{
    private const CACHE_PREFIX        = 'whatsapp_otp:';
    private const CACHE_LOCK_PREFIX   = 'whatsapp_resend_lock:';
    private const CACHE_CONFIG        = 'whatsapp_config';
    private const CACHE_SUPPORT       = 'whatsapp_support_contact';
    private const CACHE_CONFIG_TTL    = 60;  // minutes
    private const RESEND_LOCK_MINUTES = 30;
    private const GRAPH_BASE          = 'https://graph.facebook.com';

    // ──────────────────────────────────────────────────────────────
    // Public API
    // ──────────────────────────────────────────────────────────────

    /**
     * Generate OTP, persist it in cache and dispatch via Meta Cloud API.
     * Returns ['otp_expires_in' => int, 'resend_in' => int] on success.
     *
     * @throws \RuntimeException when the API call fails
     */
    public function sendOtp(User $user): array
    {
        $config = $this->getConfig();
        $otp    = $this->generateOtp();
        $ttl    = (int) $config->tiempo_otp;

        Cache::put($this->cacheKey($user->id), $otp, now()->addMinutes($ttl));

        // Store lock expiry as timestamp so we can compute remaining minutes
        Cache::put(
            $this->lockKey($user->id),
            now()->addMinutes(self::RESEND_LOCK_MINUTES)->timestamp,
            now()->addMinutes(self::RESEND_LOCK_MINUTES),
        );

        $this->dispatchTemplate(
            config:         $config,
            to:             $this->normalizeNumber($user->whatsapp),
            otp:            $otp,
            supportContact: $this->getSupportContact(),
        );

        return [
            'otp_expires_in' => $ttl,
            'resend_in'      => self::RESEND_LOCK_MINUTES,
        ];
    }

    public function clearResendLock(User $user): void
    {
        Cache::forget($this->lockKey($user->id));
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

    /**
     * Validate the OTP submitted by the user and mark whatsapp as verified.
     */
    public function verifyOtp(User $user, string $code): bool
    {
        $key    = $this->cacheKey($user->id);
        $stored = Cache::get($key);

        if ($stored === null || $stored !== $code) {
            return false;
        }

        Cache::forget($key);
        $user->update(['whatsapp_verified_at' => now()]);

        return true;
    }

    // ──────────────────────────────────────────────────────────────
    // Internals
    // ──────────────────────────────────────────────────────────────

    private function dispatchTemplate(ConfigDefecto $config, string $to, string $otp, string $supportContact): void
    {
        $url = sprintf(
            '%s/%s/%s/messages',
            self::GRAPH_BASE,
            $config->whatsapp_api_version,
            $config->whatsapp_phone_id,
        );

        $response = Http::withToken($config->whatsapp_token)
            ->post($url, [
                'messaging_product' => 'whatsapp',
                'to'                => $to,
                'type'              => 'template',
                'template'          => [
                    'name'       => $config->whatsapp_template_verificacion,
                    'language'   => ['code' => 'es'],
                    'components' => [
                        [
                            'type'       => 'body',
                            'parameters' => [
                                ['type' => 'text', 'text' => $otp],
                                ['type' => 'text', 'text' => $supportContact],
                            ],
                        ],
                        [
                            'type'       => 'button',
                            'sub_type'   => 'url',
                            'index'      => '0',
                            'parameters' => [
                                ['type' => 'text', 'text' => $otp],
                            ],
                        ],
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException(
                'Meta Cloud API error: ' . $response->body(),
                $response->status(),
            );
        }
    }

    private function getConfig(): ConfigDefecto
    {
        return Cache::remember(self::CACHE_CONFIG, now()->addMinutes(self::CACHE_CONFIG_TTL), fn () => ConfigDefecto::main());
    }

    private function getSupportContact(): string
    {
        $raw = Cache::remember(
            self::CACHE_SUPPORT,
            now()->addMinutes(self::CACHE_CONFIG_TTL),
            fn () => CanalSoporte::where('canal', 'whatsapp')->where('activo', true)->value('detalle') ?? '',
        );

        return preg_replace('/\s+/', '', $raw);
    }

    private function generateOtp(): string
    {
        return (string) random_int(100000, 999999);
    }

    private function cacheKey(int|string $userId): string
    {
        return self::CACHE_PREFIX . $userId;
    }

    private function lockKey(int|string $userId): string
    {
        return self::CACHE_LOCK_PREFIX . $userId;
    }

    private function normalizeNumber(string $number): string
    {
        // Remove +, spaces, dashes — Meta API expects digits only (e.g. 573155533324)
        return preg_replace('/[^0-9]/', '', $number);
    }
}
