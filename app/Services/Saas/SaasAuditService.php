<?php

declare(strict_types=1);

namespace App\Services\Saas;

use App\Models\Saas\SaasAuditLog;
use Illuminate\Support\Facades\Auth;

final class SaasAuditService
{
    // Accion constants — use these to avoid raw strings in callers
    public const KEY_GENERATED         = 'key_generated';
    public const KEY_REVOKED           = 'key_revoked';
    public const MODULE_TOGGLED        = 'module_toggled';
    public const MODULES_SYNCED        = 'modules_synced';
    public const MODULE_GLOBAL_TOGGLED  = 'module_global_toggled';
    public const EMPRESA_ESTADO_TOGGLED = 'empresa_estado_toggled';

    /**
     * Record a SaaS admin action.
     *
     * @param string   $accion      One of the class constants
     * @param array    $datos       Contextual data (empresa, modulo, key info, etc.)
     * @param int|null $empresaId   Empresa affected (null for global actions)
     */
    public function log(string $accion, array $datos = [], ?int $empresaId = null): void
    {
        SaasAuditLog::create([
            'empresa_id' => $empresaId,
            'user_id'    => Auth::id(),
            'accion'     => $accion,
            'datos'      => $datos,
        ]);
    }
}
