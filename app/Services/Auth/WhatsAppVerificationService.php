<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\CanalSoporte;
use App\Models\ConfigDefecto;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class WhatsAppVerificationService extends OtpVerificationService
{
    private const CACHE_SUPPORT = 'whatsapp_support_contact';
    private const GRAPH_BASE    = 'https://graph.facebook.com';

    protected function channel(): string        { return 'whatsapp'; }
    protected function userPhoneField(): string  { return 'whatsapp'; }
    protected function verifiedAtField(): string { return 'whatsapp_verified_at'; }

    protected function dispatch(ConfigDefecto $config, string $to, string $otp): void
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
                                ['type' => 'text', 'text' => $this->getSupportContact()],
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

    private function getSupportContact(): string
    {
        $raw = Cache::remember(
            self::CACHE_SUPPORT,
            now()->addMinutes(60),
            fn () => CanalSoporte::where('canal', 'whatsapp')->where('activo', true)->value('detalle') ?? '',
        );

        return preg_replace('/\s+/', '', $raw);
    }
}
