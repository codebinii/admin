<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\Auth\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\Auth\AuthService;
use App\Services\Auth\WhatsAppVerificationService;
use Illuminate\Http\JsonResponse;

final class UpdateProfileController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly WhatsAppVerificationService $whatsAppService,
    ) {}

    public function __invoke(UpdateProfileRequest $request): JsonResponse
    {
        $currentUser     = $request->user();
        $whatsappChanged = $request->filled('whatsapp')
            && $request->input('whatsapp') !== $currentUser->whatsapp;

        $user = $this->authService->updateProfile(
            $currentUser,
            $request->only(['name', 'email', 'phone', 'whatsapp']),
        );

        if ($whatsappChanged) {
            $this->whatsAppService->clearResendLock($user);
        }

        return ApiResponse::ok(
            data:    new UserResource($user),
            message: trans('api.profile_updated'),
        );
    }
}
