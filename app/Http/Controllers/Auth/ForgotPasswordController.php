<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

final class ForgotPasswordController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->sendPasswordResetLink($request->string('email')->toString());

        if ($status === Password::RESET_LINK_SENT) {
            return ApiResponse::ok(message: trans('api.password_reset_sent'));
        }

        return ApiResponse::badRequest(trans('api.password_reset_failed'));
    }
}
