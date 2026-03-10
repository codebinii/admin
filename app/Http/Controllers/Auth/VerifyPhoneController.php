<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyPhoneRequest;
use App\Http\Resources\Auth\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\Auth\PhoneVerificationService;
use Illuminate\Http\JsonResponse;

final class VerifyPhoneController extends Controller
{
    public function __construct(
        private readonly PhoneVerificationService $service,
    ) {}

    public function __invoke(VerifyPhoneRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->phone_verified_at) {
            return ApiResponse::badRequest(trans('api.phone_already_verified'));
        }

        if (! $this->service->verifyOtp($user, $request->string('code')->toString())) {
            return ApiResponse::badRequest(trans('api.phone_otp_invalid'));
        }

        return ApiResponse::ok(
            data:    new UserResource($user->fresh()),
            message: trans('api.phone_verified'),
        );
    }
}
