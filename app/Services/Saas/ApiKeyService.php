<?php

declare(strict_types=1);

namespace App\Services\Saas;

use App\Models\Saas\Empresa;
use App\Models\Saas\EmpresaApiKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

final class ApiKeyService
{
    private const KEY_PREFIX_LENGTH = 8;
    private const CACHE_TTL         = 120; // minutes, fallback safety net
    private const CACHE_KEY         = 'saas_key:';

    /**
     * Generate a new API key for an empresa.
     * Returns the plain key — shown only once.
     *
     * @return array{plain_key: string, model: EmpresaApiKey}
     */
    public function generate(Empresa $empresa, ?string $nombre = null): array
    {
        $plainKey = 'sk_' . Str::random(40);
        $prefix   = substr($plainKey, 0, self::KEY_PREFIX_LENGTH);

        $model = EmpresaApiKey::create([
            'empresa_id'   => $empresa->id,
            'key_prefix'   => $prefix,
            'api_key_hash' => hash('sha256', $plainKey),
            'nombre'       => $nombre,
            'activo'       => true,
        ]);

        return ['plain_key' => $plainKey, 'model' => $model];
    }

    /**
     * Resolve the empresa from a raw API key, with full auth checks.
     * Cache stores empresa attributes to avoid a second DB round-trip on every request.
     * Returns null when key is invalid, inactive, or empresa is inactive.
     */
    public function resolveEmpresa(string $plainKey): ?Empresa
    {
        $prefix = substr($plainKey, 0, self::KEY_PREFIX_LENGTH);
        $cached = Cache::get(self::CACHE_KEY . $prefix);

        if ($cached !== null) {
            if (! hash_equals($cached['api_key_hash'], hash('sha256', $plainKey))) {
                return null;
            }

            if (! $cached['activo'] || ! $cached['empresa_activa']) {
                return null;
            }

            // Reconstruct Empresa from cached attributes — no DB round-trip
            return (new Empresa)->setRawAttributes($cached['empresa_attrs']);
        }

        // Cache miss: query DB once, store everything needed
        $apiKey = EmpresaApiKey::where('key_prefix', $prefix)
            ->where('activo', true)
            ->with('empresa')
            ->first();

        if (! $apiKey || ! $apiKey->empresa) {
            return null;
        }

        if (! hash_equals($apiKey->api_key_hash, hash('sha256', $plainKey))) {
            return null;
        }

        $empresa = $apiKey->empresa;

        Cache::put(self::CACHE_KEY . $prefix, [
            'api_key_hash'   => $apiKey->api_key_hash,
            'activo'         => $apiKey->activo,
            'empresa_activa' => (bool) $empresa->estado,
            'empresa_attrs'  => $empresa->getAttributes(),
        ], now()->addMinutes(self::CACHE_TTL));

        if (! $empresa->estado) {
            return null;
        }

        return $empresa;
    }

    /**
     * Revoke an API key and clear its cache entry.
     */
    public function revoke(EmpresaApiKey $apiKey): void
    {
        Cache::forget(self::CACHE_KEY . $apiKey->key_prefix);
        $apiKey->delete();
    }

    /**
     * Invalidate cache for all keys of an empresa (e.g. when empresa estado changes).
     */
    public function invalidateCacheForEmpresa(Empresa $empresa): void
    {
        EmpresaApiKey::where('empresa_id', $empresa->id)
            ->pluck('key_prefix')
            ->each(fn (string $prefix) => Cache::forget(self::CACHE_KEY . $prefix));
    }
}
