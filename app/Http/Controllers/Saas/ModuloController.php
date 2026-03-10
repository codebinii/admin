<?php

declare(strict_types=1);

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Http\Resources\Saas\ModuloResource;
use App\Http\Responses\ApiResponse;
use App\Models\Saas\Empresa;
use App\Models\Saas\Modulo;
use App\Services\Saas\ModuloService;
use Illuminate\Http\JsonResponse;

final class ModuloController extends Controller
{
    public function __construct(
        private readonly ModuloService $moduloService,
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
     * Cache is invalidated for every empresa that has this module assigned.
     */
    public function toggleGlobal(Modulo $modulo): JsonResponse
    {
        $newState = $this->moduloService->toggleGlobal($modulo);

        return ApiResponse::ok(
            data:    ['activo' => $newState],
            message: trans('api.saas_module_toggled'),
        );
    }

    /**
     * Toggle a module for a specific empresa.
     * Creates the assignment if it doesn't exist yet.
     */
    public function toggleEmpresa(Empresa $empresa, Modulo $modulo): JsonResponse
    {
        $newState = $this->moduloService->toggleForEmpresa($empresa, $modulo);

        return ApiResponse::ok(
            data:    ['activo' => $newState],
            message: trans('api.saas_module_toggled'),
        );
    }
}
