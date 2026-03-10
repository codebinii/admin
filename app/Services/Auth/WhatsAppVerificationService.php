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
    private const CACHE_PREFIX = 'whatsapp_otp:';
    private const GRAPH_BASE   = 'https://graph.facebook.com';

    // ──────────────────────────────────────────────────────────────
    // Public API
    // ──────────────────────────────────────────────────────────────

    /**
     * Generate OTP, persist it in cache and dispatch via Meta Cloud API.
     *
     * @throws \RuntimeException when the API call fails
     */
    public function sendOtp(User $user): int
    {
        $config = ConfigDefecto::main();
        $otp    = $this->generateOtp();
        $ttl    = (int) $config->tiempo_otp;

        Cache::put($this->cacheKey($user->id), $otp, now()->addMinutes($ttl));

        $supportContact = CanalSoporte::where('canal', 'whatsapp')
            ->where('activo', true)
            ->value('detalle') ?? '';

        $this->dispatchTemplate(
            config:         $config,
            to:             $this->normalizeNumber($user->whatsapp),
            otp:            $otp,
            supportContact: preg_replace('/\s+/', '', $supportContact),
        );

        return $ttl;
    }

    /**
     * Validate the OTP submitted by the user and mark whatsapp as verified.
     */
    public function verifyOtp(User $user, string $code): bool
    {
        $key   = $this->cacheKey($user->id);
        $stored = Cache::get($key);

        if ($stored === null || (string) $stored !== $code) {
            return false;
        }

        Cache::forget($key);
        $user->update(['whatsapp_verified_at' => now()]);

        return true;
    }

    public function hasPendingOtp(User $user): bool
    {
        return Cache::has($this->cacheKey($user->id));
    }

    // ──────────────────────────────────────────────────────────────
    // Internals
    // ──────────────────────────────────────────────────────────────

    private function dispatchTemplate(ConfigDefecto $config, string $to, int $otp, string $supportContact): void
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
                                ['type' => 'text', 'text' => (string) $otp],
                                ['type' => 'text', 'text' => $supportContact],
                            ],
                        ],
                        [
                            'type'       => 'button',
                            'sub_type'   => 'url',
                            'index'      => '0',
                            'parameters' => [
                                ['type' => 'text', 'text' => (string) $otp],
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

    private function generateOtp(): int
    {
        return random_int(100000, 999999);
    }

    private function cacheKey(int|string $userId): string
    {
        return self::CACHE_PREFIX . $userId;
    }

    private function normalizeNumber(string $number): string
    {
        // Remove +, spaces, dashes — Meta API expects digits only (e.g. 573155533324)
        return preg_replace('/[^0-9]/', '', $number);
    }
}
