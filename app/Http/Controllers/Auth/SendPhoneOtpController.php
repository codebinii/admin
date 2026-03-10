<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\Auth\PhoneVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SendPhoneOtpController extends Controller
{
    public function __construct(
        private readonly PhoneVerificationService $service,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->phone) {
            return ApiResponse::badRequest(trans('api.phone_not_set'));
        }

        if ($user->phone_verified_at) {
            return ApiResponse::badRequest(trans('api.phone_already_verified'));
        }

        $remainingMinutes = $this->service->resendLockRemainingMinutes($user);

        if ($remainingMinutes > 0) {
            return ApiResponse::badRequest(
                trans('api.phone_otp_resend_locked', ['minutes' => $remainingMinutes]),
            );
        }

        try {
            $result = $this->service->sendOtp($user);
        } catch (\RuntimeException $e) {
            return ApiResponse::serverError($e->getMessage(), $e);
        }

        return ApiResponse::ok(
            data:    [
                'otp_expires_in' => $result['otp_expires_in'],
                'resend_in'      => $result['resend_in'],
            ],
            message: trans('api.phone_otp_sent'),
        );
    }
}
