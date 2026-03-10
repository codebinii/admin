<?php

declare(strict_types=1);

namespace App\Models\Saas;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EmpresaApiKey extends Model
{
    use HasUlids;

    protected $table = 'empresa_api_keys';

    protected $fillable = [
        'empresa_id',
        'key_prefix',
        'api_key_hash',
        'nombre',
        'activo',
    ];

    protected $hidden = ['api_key_hash'];

    protected $casts = [
        'activo'     => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
