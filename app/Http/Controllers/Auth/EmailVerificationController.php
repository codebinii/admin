<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class EmailVerificationController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function __invoke(Request $request, string $id, string $hash): JsonResponse
    {
        if (! $request->hasValidSignature()) {
            return ApiResponse::forbidden(trans('api.verification_link_invalid'));
        }

        $user = User::findOrFail($id);

        if (! $this->authService->verifyEmail($user, $hash)) {
            return ApiResponse::forbidden(trans('api.verification_link_invalid'));
        }

        return ApiResponse::ok(
            data: new UserResource($user->fresh()),
            message: trans('api.email_verified'),
        );
    }
}
