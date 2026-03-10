<?php

declare(strict_types=1);

namespace App\Http\Controllers\Status;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\CanalSoporte;
use Illuminate\Http\JsonResponse;

final class StatusController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return ApiResponse::ok(
            data: [
                'server'  => 'online',
                'uptime'  => $this->uptime(),
                'support' => CanalSoporte::where('activo', true)
                    ->get(['canal', 'detalle', 'agente']),
            ],
            message: 'Server is running.',
        );
    }

    private function uptime(): string
    {
        if (PHP_OS_FAMILY === 'Linux' && is_readable('/proc/uptime')) {
            $seconds = (int) explode(' ', file_get_contents('/proc/uptime'))[0];
        } else {
            $seconds = (int) (microtime(true) - LARAVEL_START);
        }

        $days    = intdiv($seconds, 86400);
        $hours   = intdiv($seconds % 86400, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs    = $seconds % 60;

        return "{$days}d {$hours}h {$minutes}m {$secs}s";
    }
}
