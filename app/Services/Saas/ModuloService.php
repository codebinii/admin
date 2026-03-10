<?php

declare(strict_types=1);

namespace App\Services\Saas;

use App\Models\Saas\Empresa;
use App\Models\Saas\EmpresaModulo;
use App\Models\Saas\Modulo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class ModuloService
{
    private const CACHE_TTL     = 120; // minutes, fallback safety net
    private const CACHE_MODULES = 'saas_modules:';

    /**
     * Returns the list of active module slugs (nombre_modulo) for an empresa.
     * A module is active when: 04modulos.activo = true AND empresa_modulos.activo = true.
     */
    public function getActiveModules(int|string $empresaId): array
    {
        return Cache::remember(
            self::CACHE_MODULES . $empresaId,
            now()->addMinutes(self::CACHE_TTL),
            fn () => DB::table('empresa_modulos')
                ->join('04modulos', '04modulos.id', '=', 'empresa_modulos.modulo_id')
                ->where('empresa_modulos.empresa_id', $empresaId)
                ->where('empresa_modulos.activo', true)
                ->where('04modulos.activo', true)
                ->pluck('04modulos.nombre_modulo')
                ->toArray(),
        );
    }

    /**
     * Toggle a module for a specific empresa.
     * Creates the pivot row if it doesn't exist yet.
     * Returns the new activo state.
     */
    public function toggleForEmpresa(Empresa $empresa, Modulo $modulo): bool
    {
        $pivot = EmpresaModulo::where('empresa_id', $empresa->id)
            ->where('modulo_id', $modulo->id)
            ->first();

        if ($pivot) {
            $newState = ! $pivot->activo;
            $pivot->update(['activo' => $newState]);
        } else {
            $newState = true;
            EmpresaModulo::create([
                'empresa_id' => $empresa->id,
                'modulo_id'  => $modulo->id,
                'activo'     => true,
            ]);
        }

        $this->invalidateCacheForEmpresa($empresa->id);

        return $newState;
    }

    /**
     * Toggle a module globally (04modulos.activo).
     * Clears cache for all empresas that have this module assigned.
     * Returns the new activo state.
     */
    public function toggleGlobal(Modulo $modulo): bool
    {
        $newState = ! $modulo->activo;

        // Update the shared table via DB (not via model save to respect read-only convention)
        DB::table('04modulos')->where('id', $modulo->id)->update(['activo' => $newState]);

        // Invalidate cache for every empresa that has this module
        EmpresaModulo::where('modulo_id', $modulo->id)
            ->pluck('empresa_id')
            ->each(fn ($id) => $this->invalidateCacheForEmpresa($id));

        return $newState;
    }

    public function invalidateCacheForEmpresa(int|string $empresaId): void
    {
        Cache::forget(self::CACHE_MODULES . $empresaId);
    }
}
