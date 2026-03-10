<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\Auth\WhatsAppVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SendWhatsAppOtpController extends Controller
{
    public function __construct(
        private readonly WhatsAppVerificationService $service,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->whatsapp) {
            return ApiResponse::badRequest(trans('api.whatsapp_not_set'));
        }

        if ($user->whatsapp_verified_at) {
            return ApiResponse::badRequest(trans('api.whatsapp_already_verified'));
        }

        $remainingMinutes = $this->service->resendLockRemainingMinutes($user);

        if ($remainingMinutes > 0) {
            return ApiResponse::badRequest(
                trans('api.whatsapp_otp_resend_locked', ['minutes' => $remainingMinutes]),
            );
        }

        try {
            $result = $this->service->sendOtp($user);
        } catch (\RuntimeException $e) {
            return ApiResponse::serverError($e->getMessage(), $e);
        }

        return ApiResponse::ok(
            data:    [
                'otp_expires_in'  => $result['otp_expires_in'],
                'resend_in'       => $result['resend_in'],
            ],
            message: trans('api.whatsapp_otp_sent'),
        );
    }
}
