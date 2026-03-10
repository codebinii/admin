<?php

declare(strict_types=1);

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Http\Resources\Saas\AuditLogResource;
use App\Http\Responses\ApiResponse;
use App\Models\Saas\SaasAuditLog;
use App\Services\Saas\SaasAuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AuditLogController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $logs = SaasAuditLog::with('user')
            ->when($request->filled('empresa_id'), fn ($q) => $q->where('empresa_id', $request->integer('empresa_id')))
            ->when($request->filled('accion'),     fn ($q) => $q->where('accion', $request->string('accion')))
            ->when($request->filled('desde'),      fn ($q) => $q->whereDate('created_at', '>=', $request->string('desde')))
            ->when($request->filled('hasta'),      fn ($q) => $q->whereDate('created_at', '<=', $request->string('hasta')))
            ->orderByDesc('created_at')
            ->paginate(50);

        return ApiResponse::paginated(
            paginator: $logs->through(fn ($log) => new AuditLogResource($log)),
            message:   trans('api.ok'),
        );
    }
}
