<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dv;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dv\DvRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Dv\DvService;
use Illuminate\Http\JsonResponse;

final class DvController extends Controller
{
    public function __construct(
        private readonly DvService $dvService,
    ) {}

    public function __invoke(DvRequest $request): JsonResponse
    {
        $resultados = collect($request->input('nits'))
            ->map(fn (string $nit): array => $this->dvService->calcular($nit))
            ->values();

        return ApiResponse::ok([
            'total' => $resultados->count(),
            'resultados' => $resultados,
        ]);
    }
}
