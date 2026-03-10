<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Auth\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

final class ChangePasswordController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function __invoke(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->changePassword(
                user:            $request->user(),
                currentPassword: $request->string('current_password')->toString(),
                newPassword:     $request->string('password')->toString(),
            );
        } catch (AuthenticationException) {
            return ApiResponse::unauthorized(trans('api.invalid_current_password'));
        }

        return ApiResponse::ok(message: trans('api.password_changed'));
    }
}
