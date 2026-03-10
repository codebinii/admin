<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SessionsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $sessions = $request->user()
            ->tokens()
            ->orderByDesc('last_used_at')
            ->get(['id', 'name', 'last_used_at', 'created_at', 'expires_at'])
            ->map(fn ($token) => [
                'id'           => $token->id,
                'device'       => $token->name,
                'last_used_at' => $token->last_used_at?->toIso8601String(),
                'created_at'   => $token->created_at?->toIso8601String(),
                'expires_at'   => $token->expires_at?->toIso8601String(),
                'current'      => $token->id === $request->user()->currentAccessToken()->id,
            ]);

        return ApiResponse::ok(data: $sessions);
    }
}
