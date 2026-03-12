<?php

declare(strict_types=1);

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

final class DbStatusController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            $pdo    = DB::connection()->getPdo();
            $config = DB::connection()->getConfig();

            $version = DB::selectOne('SELECT version() AS version')?->version ?? 'unknown';

            return ApiResponse::ok([
                'conexion' => true,
                'driver'   => $config['driver'],
                'host'     => $config['host'],
                'puerto'   => $config['port'],
                'base_datos' => $config['database'],
                'usuario'  => $config['username'],
                'version'  => $version,
                'charset'  => $pdo->getAttribute(\PDO::ATTR_CLIENT_VERSION),
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::serverError('No se pudo conectar a la base de datos: ' . $e->getMessage());
        }
    }
}
