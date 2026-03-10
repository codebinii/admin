<?php

declare(strict_types=1);

namespace App\Models\Saas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Read-only model for shared `04modulos` table.
 * Never write to this table — use ModuloService to toggle `activo` only.
 */
final class Modulo extends Model
{
    protected $table = '04modulos';

    protected $guarded = ['*'];

    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'empresa_modulos', 'modulo_id', 'empresa_id')
            ->withPivot('activo')
            ->withTimestamps();
    }
}
