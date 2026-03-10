<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class RevokeSessionController extends Controller
{
    public function __invoke(Request $request, int $id): JsonResponse
    {
        $deleted = $request->user()
            ->tokens()
            ->where('id', $id)
            ->delete();

        if (! $deleted) {
            return ApiResponse::notFound('Session');
        }

        return ApiResponse::ok(message: trans('api.session_revoked'));
    }
}
