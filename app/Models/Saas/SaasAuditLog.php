<?php

declare(strict_types=1);

namespace App\Models\Saas;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

final class SaasAuditLog extends Model
{
    use HasUlids;

    protected $table = 'saas_audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'empresa_id',
        'user_id',
        'accion',
        'datos',
    ];

    protected $casts = [
        'datos'      => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
