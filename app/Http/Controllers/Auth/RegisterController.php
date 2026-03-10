<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;

final class RegisterController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register(
            name:       $request->string('name')->toString(),
            email:      $request->string('email')->toString(),
            password:   $request->string('password')->toString(),
            deviceName: $request->string('device_name', 'api')->toString(),
            phone:      $request->filled('phone') ? $request->string('phone')->toString() : null,
            whatsapp:   $request->filled('whatsapp') ? $request->string('whatsapp')->toString() : null,
        );

        return ApiResponse::created(
            data: [
                'user'  => new UserResource($result['user']),
                'token' => $result['token'],
            ],
            message: trans('api.registered'),
        );
    }
}
