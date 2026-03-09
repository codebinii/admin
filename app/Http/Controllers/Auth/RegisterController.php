<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\UserResource;
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
        );

        return response()->json([
            'user'  => new UserResource($result['user']),
            'token' => $result['token'],
        ], 201);
    }
}
