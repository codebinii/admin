<?php

declare(strict_types=1);

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Saas\SyncModulosRequest;
use App\Http\Resources\Saas\ModuloResource;
use App\Http\Responses\ApiResponse;
use App\Models\Saas\Empresa;
use App\Models\Saas\Modulo;
use App\Services\Saas\ModuloService;
use App\Services\Saas\SaasAuditService;
use Illuminate\Http\JsonResponse;

final class ModuloController extends Controller
{
    public function __construct(
        private readonly ModuloService    $moduloService,
        private readonly SaasAuditService $audit,
    ) {}

    /**
     * List all modules with their global status.
     */
    public function index(): JsonResponse
    {
        $modulos = Modulo::orderBy('nombre_modulo')->get();

        return ApiResponse::ok(ModuloResource::collection($modulos));
    }

    /**
     * Toggle a module globally — affects all empresas simultaneously.
     * Cache invalidated in O(1) via version bump.
     */
    public function toggleGlobal(Modulo $modulo): JsonResponse
    {
        $newState = $this->moduloService->toggleGlobal($modulo);

        $this->audit->log(SaasAuditService::MODULE_GLOBAL_TOGGLED, [
            'modulo_id'     => $modulo->id,
            'nombre_modulo' => $modulo->nombre_modulo,
            'activo'        => $newState,
        ]);

        return ApiResponse::ok(
            data:    ['activo' => $newState],
            message: trans('api.saas_module_toggled'),
        );
    }

    /**
     * Sync active modules for an empresa from a full list of IDs.
     * IDs present → activo = true. IDs absent → activo = false.
     * Send empty array to deactivate all modules for the empresa.
     */
    public function syncEmpresa(SyncModulosRequest $request, Empresa $empresa): JsonResponse
    {
        $moduloIds = $request->input('modulos', []);

        $this->moduloService->syncForEmpresa($empresa, $moduloIds);

        $this->audit->log(SaasAuditService::MODULES_SYNCED, [
            'modulos_activos' => $moduloIds,
        ], $empresa->id);

        $empresa->load('modulos');

        return ApiResponse::ok(
            data:    ModuloResource::collection($empresa->modulos),
            message: trans('api.saas_modules_synced'),
        );
    }

    /**
     * Toggle a module for a specific empresa.
     * Creates the assignment if it doesn't exist yet.
     */
    public function toggleEmpresa(Empresa $empresa, Modulo $modulo): JsonResponse
    {
        $newState = $this->moduloService->toggleForEmpresa($empresa, $modulo);

        $this->audit->log(SaasAuditService::MODULE_TOGGLED, [
            'modulo_id'     => $modulo->id,
            'nombre_modulo' => $modulo->nombre_modulo,
            'activo'        => $newState,
        ], $empresa->id);

        return ApiResponse::ok(
            data:    ['activo' => $newState],
            message: trans('api.saas_module_toggled'),
        );
    }
}
