<?php

declare(strict_types=1);

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Http\Resources\Saas\ApiKeyResource;
use App\Http\Resources\Saas\EmpresaResource;
use App\Http\Resources\Saas\ModuloResource;
use App\Http\Responses\ApiResponse;
use App\Models\Saas\Empresa;
use App\Services\Saas\ApiKeyService;
use App\Services\Saas\SaasAuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class EmpresaController extends Controller
{
    public function __construct(
        private readonly ApiKeyService    $apiKeyService,
        private readonly SaasAuditService $audit,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $empresas = Empresa::withCount(['apiKeys' => fn ($q) => $q->where('activo', true)])
            ->when(
                $request->filled('search'),
                fn ($q) => $q->where(function ($q) use ($request): void {
                    $term = '%' . $request->string('search')->toString() . '%';
                    $q->where('nombre', 'ilike', $term)
                      ->orWhere('cod_empresa', 'ilike', $term);
                }),
            )
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->boolean('estado')))
            ->orderBy('nombre')
            ->paginate(25);

        return ApiResponse::paginated($empresas);
    }

    public function show(Empresa $empresa): JsonResponse
    {
        $empresa->loadCount(['apiKeys' => fn ($q) => $q->where('activo', true)])
                ->load(['modulos', 'apiKeys']);

        return ApiResponse::ok([
            'empresa' => new EmpresaResource($empresa),
            'modulos' => ModuloResource::collection($empresa->modulos),
            'keys'    => ApiKeyResource::collection($empresa->apiKeys),
        ]);
    }

    /**
     * Toggle empresa estado (activo/inactivo).
     * When deactivated: all API key caches are invalidated immediately
     * so the SaaS app receives the blocked response on next request.
     */
    public function toggleEstado(Empresa $empresa): JsonResponse
    {
        $newState = ! $empresa->estado;

        // Direct DB update — 01empresas is a shared table, model is read-only
        DB::table('01empresas')->where('id', $empresa->id)->update(['estado' => $newState]);

        // Invalidate all key caches so auth reflects new estado immediately
        $this->apiKeyService->invalidateCacheForEmpresa($empresa);

        $this->audit->log(SaasAuditService::EMPRESA_ESTADO_TOGGLED, [
            'nombre'  => $empresa->nombre,
            'estado'  => $newState,
        ], $empresa->id);

        return ApiResponse::ok(
            data:    ['estado' => $newState],
            message: $newState
                ? trans('api.saas_empresa_activada')
                : trans('api.saas_empresa_desactivada'),
        );
    }
}
