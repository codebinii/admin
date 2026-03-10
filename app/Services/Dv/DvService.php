<?php

declare(strict_types=1);

namespace App\Services\Dv;

final class DvService
{
    /** Pesos DIAN aplicados de derecha a izquierda (posición 0 = dígito más a la derecha). */
    private const PESOS = [3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71];

    /**
     * Calcula el dígito de verificación DIAN para un NIT.
     *
     * Acepta formato con o sin guión: "900123456" o "900123456-7".
     * El DV incluido en el formato con guión se ignora; siempre se recalcula.
     *
     * @return array{nit: string, dv: int, nit_completo: string}|array{nit: string, dv: null, error: string}
     */
    public function calcular(string $nit): array
    {
        $nit = trim($nit);

        // Separar el NIT del DV si viene con guión
        $nitLimpio = explode('-', $nit)[0];

        if ($nitLimpio === '') {
            return $this->error($nit, 'El NIT no puede ser vacío.');
        }

        if (! ctype_digit($nitLimpio)) {
            return $this->error($nit, 'El NIT contiene caracteres no numéricos.');
        }

        $digitos = str_split($nitLimpio);
        $total = count($digitos);

        if ($total > count(self::PESOS)) {
            return $this->error($nit, 'El NIT excede la longitud máxima permitida (15 dígitos).');
        }

        // Sumar multiplicando cada dígito por su peso (de derecha a izquierda)
        $suma = 0;
        $aux = $total - 1;

        for ($i = 0; $i < $total; $i++) {
            $suma += self::PESOS[$i] * (int) $digitos[$aux - $i];
        }

        $modulo = $suma % 11;

        $dv = $modulo >= 2 ? 11 - $modulo : $modulo;

        return [
            'nit' => $nitLimpio,
            'dv' => $dv,
            'nit_completo' => "{$nitLimpio}-{$dv}",
        ];
    }

    /**
     * @return array{nit: string, dv: null, error: string}
     */
    private function error(string $nit, string $mensaje): array
    {
        return [
            'nit' => $nit,
            'dv' => null,
            'error' => $mensaje,
        ];
    }
}
