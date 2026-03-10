<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use App\Services\Saas\ApiKeyService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ValidateSaasKey
{
    public function __construct(
        private readonly ApiKeyService $apiKeyService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $plainKey = $request->header('X-Api-Key');

        if (! $plainKey) {
            return ApiResponse::unauthorized(trans('api.saas_key_missing'));
        }

        $empresa = $this->apiKeyService->resolveEmpresa($plainKey);

        if (! $empresa) {
            return ApiResponse::unauthorized(trans('api.saas_key_invalid'));
        }

        $request->attributes->set('saas_empresa', $empresa);

        return $next($request);
    }
}
