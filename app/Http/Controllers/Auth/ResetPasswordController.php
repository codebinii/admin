<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

final class ResetPasswordController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function __invoke(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->resetPassword(
            email:    $request->string('email')->toString(),
            token:    $request->string('token')->toString(),
            password: $request->string('password')->toString(),
        );

        if ($status === Password::PASSWORD_RESET) {
            return ApiResponse::ok(message: trans('api.password_reset_success'));
        }

        return ApiResponse::badRequest(trans('api.password_reset_invalid_token'));
    }
}
