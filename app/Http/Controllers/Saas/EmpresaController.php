<?php

declare(strict_types=1);

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Http\Resources\Saas\ApiKeyResource;
use App\Http\Resources\Saas\EmpresaResource;
use App\Http\Resources\Saas\ModuloResource;
use App\Http\Responses\ApiResponse;
use App\Models\Saas\Empresa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class EmpresaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $empresas = Empresa::withCount(['apiKeys' => fn ($q) => $q->where('activo', true)])
            ->orderBy('nombre')
            ->paginate(25);

        return ApiResponse::paginated($empresas);
    }

    public function show(Empresa $empresa): JsonResponse
    {
        $empresa->load(['modulos', 'apiKeys']);

        return ApiResponse::ok([
            'empresa' => new EmpresaResource($empresa),
            'modulos' => ModuloResource::collection($empresa->modulos),
            'keys'    => ApiKeyResource::collection($empresa->apiKeys),
        ]);
    }
}
