<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CanalSoporte;
use Illuminate\Database\Seeder;

final class CanalSoporteSeeder extends Seeder
{
    public function run(): void
    {
        $canales = [
            [
                'canal'   => 'whatsapp',
                'detalle' => '+57 315 553 3324',
                'agente'  => 'Vidal Figueroa',
            ],
            [
                'canal'   => 'web',
                'detalle' => 'www.codebini.com/support',
                'agente'  => null,
            ],
            [
                'canal'   => 'email',
                'detalle' => 'soporte@codebini.com',
                'agente'  => null,
            ],
        ];

        foreach ($canales as $canal) {
            CanalSoporte::firstOrCreate(
                ['canal' => $canal['canal']],
                $canal
            );
        }
    }
}
