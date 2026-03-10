<?php

declare(strict_types=1);

namespace App\Models\Saas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EmpresaModulo extends Model
{
    protected $table = 'empresa_modulos';

    public $incrementing = false;

    protected $primaryKey = null;

    protected $fillable = [
        'empresa_id',
        'modulo_id',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }
}
