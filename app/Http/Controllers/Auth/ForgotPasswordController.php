<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;

final class ForgotPasswordController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        // Always return 200 — never reveal whether the email is registered
        $this->authService->sendPasswordResetLink($request->string('email')->toString());

        return ApiResponse::ok(message: trans('api.password_reset_sent'));
    }
}
