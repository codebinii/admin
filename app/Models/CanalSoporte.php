<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class CanalSoporte extends Model
{
    protected $table = 'canales_soporte';

    protected $fillable = [
        'canal',
        'detalle',
        'agente',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
