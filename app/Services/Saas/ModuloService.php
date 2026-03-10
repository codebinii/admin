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
    private const CACHE_TTL         = 120; // minutes, fallback safety net
    private const CACHE_MODULES     = 'saas_modules:';
    private const CACHE_VERSION_KEY = 'saas_modules:version';

    /**
     * Returns the list of active module slugs (nombre_modulo) for an empresa.
     * A module is active when: 04modulos.activo = true AND empresa_modulos.activo = true.
     */
    public function getActiveModules(int|string $empresaId): array
    {
        return Cache::remember(
            $this->moduleCacheKey($empresaId),
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
     * Uses version-bumping for O(1) cache invalidation — all empresa module caches
     * become stale immediately without iterating over every empresa.
     * Returns the new activo state.
     */
    public function toggleGlobal(Modulo $modulo): bool
    {
        $newState = ! $modulo->activo;

        DB::table('04modulos')->where('id', $modulo->id)->update(['activo' => $newState]);

        // Bump global version → all saas_modules:* caches become stale in O(1)
        $this->bumpModulesVersion();

        return $newState;
    }

    /**
     * Sync the active modules for an empresa from a list of modulo IDs.
     * - IDs in $moduloIds  → upsert with activo = true
     * - All other assigned IDs → activo = false
     * Cache is invalidated once after all changes.
     */
    public function syncForEmpresa(Empresa $empresa, array $moduloIds): void
    {
        $now = now();

        foreach ($moduloIds as $moduloId) {
            EmpresaModulo::updateOrCreate(
                ['empresa_id' => $empresa->id, 'modulo_id' => $moduloId],
                ['activo' => true, 'updated_at' => $now],
            );
        }

        EmpresaModulo::where('empresa_id', $empresa->id)
            ->when(! empty($moduloIds), fn ($q) => $q->whereNotIn('modulo_id', $moduloIds))
            ->update(['activo' => false, 'updated_at' => $now]);

        $this->invalidateCacheForEmpresa($empresa->id);
    }

    public function invalidateCacheForEmpresa(int|string $empresaId): void
    {
        Cache::forget($this->moduleCacheKey($empresaId));
    }

    // ──────────────────────────────────────────────────────────────
    // Internals
    // ──────────────────────────────────────────────────────────────

    private function moduleCacheKey(int|string $empresaId): string
    {
        return self::CACHE_MODULES . $empresaId . ':' . $this->getModulesVersion();
    }

    private function getModulesVersion(): string
    {
        return Cache::get(self::CACHE_VERSION_KEY, '1');
    }

    private function bumpModulesVersion(): void
    {
        Cache::put(self::CACHE_VERSION_KEY, (string) time(), now()->addDays(30));
    }
}
