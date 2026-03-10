<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class RefreshTokenController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $currentToken = $request->user()->currentAccessToken();
        $deviceName   = $currentToken->name;

        $currentToken->delete();

        $token = $request->user()->createToken($deviceName)->plainTextToken;

        return ApiResponse::ok(
            data:    ['token' => $token],
            message: trans('api.token_refreshed'),
        );
    }
}
