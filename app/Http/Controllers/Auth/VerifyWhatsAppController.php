<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyWhatsAppRequest;
use App\Http\Resources\Auth\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\Auth\WhatsAppVerificationService;
use Illuminate\Http\JsonResponse;

final class VerifyWhatsAppController extends Controller
{
    public function __construct(
        private readonly WhatsAppVerificationService $service,
    ) {}

    public function __invoke(VerifyWhatsAppRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->whatsapp_verified_at) {
            return ApiResponse::badRequest(trans('api.whatsapp_already_verified'));
        }

        if (! $this->service->verifyOtp($user, $request->string('code')->toString())) {
            return ApiResponse::badRequest(trans('api.whatsapp_otp_invalid'));
        }

        return ApiResponse::ok(
            data:    new UserResource($user->fresh()),
            message: trans('api.whatsapp_verified'),
        );
    }
}
