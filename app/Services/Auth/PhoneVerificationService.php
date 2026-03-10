<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\ConfigDefecto;
use Illuminate\Support\Facades\Http;

final class PhoneVerificationService extends OtpVerificationService
{
    protected function channel(): string        { return 'phone'; }
    protected function userPhoneField(): string  { return 'phone'; }
    protected function verifiedAtField(): string { return 'phone_verified_at'; }

    protected function dispatch(ConfigDefecto $config, string $to, string $otp): void
    {
        $response = Http::post($config->sms_url, [
            'token'     => $config->sms_token,
            'email'     => $config->sms_user,
            'type_send' => $config->sms_type_send,
            'data'      => [
                [
                    'cellphone' => $to,
                    'message'   => trans('sms.otp_message', ['code' => $otp, 'app' => config('app.name')]),
                ],
            ],
        ]);

        if ($response->failed()) {
            throw new \RuntimeException(
                'SMS API error: ' . $response->body(),
                $response->status(),
            );
        }
    }
}
