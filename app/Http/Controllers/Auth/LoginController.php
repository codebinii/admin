<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Auth\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

final class LoginController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function __invoke(LoginRequest $request): JsonResponse
    {
        try {
            $token = $this->authService->login(
                email:      $request->string('email')->toString(),
                password:   $request->string('password')->toString(),
                deviceName: $request->string('device_name', 'api')->toString(),
            );
        } catch (AuthenticationException) {
            return ApiResponse::unauthorized(trans('api.invalid_credentials'));
        }

        return ApiResponse::ok(['token' => $token], trans('api.logged_in'));
    }
}
