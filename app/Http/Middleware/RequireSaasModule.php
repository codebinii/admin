<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use App\Models\Saas\Empresa;
use App\Services\Saas\ModuloService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RequireSaasModule
{
    public function __construct(
        private readonly ModuloService $moduloService,
    ) {}

    public function handle(Request $request, Closure $next, string $module): Response
    {
        /** @var Empresa $empresa */
        $empresa = $request->attributes->get('saas_empresa');

        $activeModules = $this->moduloService->getActiveModules($empresa->id);

        if (! in_array($module, $activeModules, strict: true)) {
            return ApiResponse::forbidden(trans('api.saas_module_inactive'));
        }

        return $next($request);
    }
}
