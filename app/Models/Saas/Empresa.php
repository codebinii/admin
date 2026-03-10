<?php

declare(strict_types=1);

namespace App\Models\Saas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Read-only model for shared `01empresas` table.
 * Never write to this table — it belongs to the host system.
 */
final class Empresa extends Model
{
    protected $table = '01empresas';

    protected $guarded = ['*'];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function apiKeys(): HasMany
    {
        return $this->hasMany(EmpresaApiKey::class, 'empresa_id');
    }

    public function modulos(): BelongsToMany
    {
        return $this->belongsToMany(Modulo::class, 'empresa_modulos', 'empresa_id', 'modulo_id')
            ->withPivot('activo')
            ->withTimestamps();
    }
}
