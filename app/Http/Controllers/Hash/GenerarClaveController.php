<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hash;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hash\GenerarClaveRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

final class GenerarClaveController extends Controller
{
    private const NUMEROS = '0123456789';

    private const MINUSCULAS = 'abcdefghijklmnopqrstuvwxyz';

    private const MAYUSCULAS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private const ESPECIALES = '!@#$%^&*()-_=+[]{}|;:,.<>?';

    public function __invoke(GenerarClaveRequest $request): JsonResponse
    {
        $longitud = (int) $request->input('longitud', 12);
        $numeros = filter_var($request->input('numeros', true), FILTER_VALIDATE_BOOLEAN);
        $minusculas = filter_var($request->input('minusculas', true), FILTER_VALIDATE_BOOLEAN);
        $mayusculas = filter_var($request->input('mayusculas', true), FILTER_VALIDATE_BOOLEAN);
        $especiales = filter_var($request->input('especiales', true), FILTER_VALIDATE_BOOLEAN);

        $charset = '';

        if ($numeros) {
            $charset .= self::NUMEROS;
        }
        if ($minusculas) {
            $charset .= self::MINUSCULAS;
        }
        if ($mayusculas) {
            $charset .= self::MAYUSCULAS;
        }
        if ($especiales) {
            $charset .= self::ESPECIALES;
        }

        $max = strlen($charset) - 1;
        $clave = '';

        for ($i = 0; $i < $longitud; $i++) {
            $clave .= $charset[random_int(0, $max)];
        }

        return ApiResponse::ok([
            'clave' => $clave,
            'longitud' => $longitud,
            'opciones' => [
                'numeros' => $numeros,
                'minusculas' => $minusculas,
                'mayusculas' => $mayusculas,
                'especiales' => $especiales,
            ],
        ]);
    }
}
