<?php

declare(strict_types=1);

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Http\Resources\Saas\ApiKeyResource;
use App\Http\Responses\ApiResponse;
use App\Models\Saas\Empresa;
use App\Models\Saas\EmpresaApiKey;
use App\Services\Saas\ApiKeyService;
use App\Services\Saas\SaasAuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ApiKeyController extends Controller
{
    public function __construct(
        private readonly ApiKeyService    $apiKeyService,
        private readonly SaasAuditService $audit,
    ) {}

    /**
     * Generate a new API key for the empresa.
     * The plain key is returned only once — it cannot be retrieved again.
     */
    public function store(Request $request, Empresa $empresa): JsonResponse
    {
        $nombre = $request->string('nombre')->toString() ?: null;

        ['plain_key' => $plainKey, 'model' => $model] = $this->apiKeyService->generate($empresa, $nombre);

        $this->audit->log(SaasAuditService::KEY_GENERATED, [
            'key_id'     => $model->id,
            'key_prefix' => $model->key_prefix,
            'nombre'     => $model->nombre,
        ], $empresa->id);

        return ApiResponse::created([
            'key'  => $plainKey,
            'meta' => new ApiKeyResource($model),
        ], trans('api.saas_key_generated'));
    }

    /**
     * Revoke an API key — permanent, requires regeneration to restore access.
     */
    public function destroy(Empresa $empresa, EmpresaApiKey $key): JsonResponse
    {
        if ($key->empresa_id !== $empresa->id) {
            return ApiResponse::notFound('ApiKey');
        }

        $this->audit->log(SaasAuditService::KEY_REVOKED, [
            'key_id'     => $key->id,
            'key_prefix' => $key->key_prefix,
            'nombre'     => $key->nombre,
        ], $empresa->id);

        $this->apiKeyService->revoke($key);

        return ApiResponse::ok(message: trans('api.saas_key_revoked'));
    }
}
