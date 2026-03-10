# Módulo SaaS Access

Control de acceso M2M para empresas clientes del SaaS. Gestiona API keys y módulos habilitados por empresa.

---

## Estructura

```
app/
├── Http/
│   ├── Controllers/Saas/
│   │   ├── EmpresaController.php       ← index, show
│   │   ├── ApiKeyController.php        ← store, destroy
│   │   └── ModuloController.php        ← index, toggleGlobal, toggleEmpresa
│   ├── Middleware/
│   │   ├── ValidateSaasKey.php         ← autentica X-Api-Key
│   │   └── RequireSaasModule.php       ← verifica módulo activo para la empresa
│   └── Resources/Saas/
│       ├── EmpresaResource.php
│       ├── ModuloResource.php
│       └── ApiKeyResource.php
├── Models/Saas/
│   ├── Empresa.php        ← solo lectura, tabla 01empresas
│   ├── Modulo.php         ← solo lectura, tabla 04modulos
│   ├── EmpresaApiKey.php  ← tabla empresa_api_keys (propia)
│   └── EmpresaModulo.php  ← tabla empresa_modulos (propia)
└── Services/Saas/
    ├── ApiKeyService.php   ← generar, verificar, revocar + cache
    └── ModuloService.php   ← toggle módulos + invalidación de cache

routes/saas.php
```

---

## Tablas

### Propias (creadas por este proyecto)

**`empresa_api_keys`**
| Campo          | Tipo    | Notas                              |
|----------------|---------|------------------------------------|
| `id`           | ulid PK |                                    |
| `empresa_id`   | bigint  | FK → `01empresas.id` ON DELETE CASCADE |
| `key_prefix`   | varchar(10) | Primeros 8 chars, texto plano, búsqueda rápida |
| `api_key_hash` | varchar(64) | SHA-256 de la key completa, nunca en plano |
| `nombre`       | varchar | Descripción opcional (ej: "producción") |
| `activo`       | boolean | Default true |

**`empresa_modulos`**
| Campo        | Tipo    | Notas                                    |
|--------------|---------|------------------------------------------|
| `empresa_id` | bigint  | FK → `01empresas.id`, PK compuesta       |
| `modulo_id`  | bigint  | FK → `04modulos.id`, PK compuesta        |
| `activo`     | boolean | Interruptor por empresa                  |

### Compartidas (solo lectura)

**`01empresas`** — clientes SaaS. `estado` controla si el cliente está activo.
**`04modulos`** — catálogo global de módulos. `activo` es el interruptor global.

---

## Control de acceso — dos niveles

```
módulo activo = 04modulos.activo = true
              AND empresa_modulos.activo = true
```

Si el módulo global está apagado, ninguna empresa puede usarlo aunque tenga `activo = true` en su pivot.

---

## Endpoints admin (requieren `Authorization: Bearer {admin_token}`)

### `GET /api/saas/empresas`
Lista paginada de empresas con conteo de keys activas.

### `GET /api/saas/empresas/{empresa}`
Detalle de la empresa con sus módulos (incluye `activo_empresa` del pivot) y sus API keys.

### `POST /api/saas/empresas/{empresa}/keys`
Genera una nueva API key. La key en plano se devuelve **una sola vez**.

**Body:** `{ "nombre": "producción" }` *(opcional)*

**Response `201`:**
```json
{
  "data": {
    "key":  "sk_AbCdEfGhIjKlMnOpQrStUvWxYz12345678901234",
    "meta": { "id": "...", "nombre": "producción", "key_prefix": "sk_AbCdEf", "activo": true }
  }
}
```

### `DELETE /api/saas/empresas/{empresa}/keys/{key}`
Revoca una API key. Acción permanente — requiere generar una nueva para restaurar acceso.

### `GET /api/saas/modulos`
Lista todos los módulos del catálogo global con su estado `activo`.

### `PATCH /api/saas/modulos/{modulo}/toggle`
Activa/desactiva un módulo globalmente. Invalida el cache de todas las empresas que tengan ese módulo asignado.

### `POST /api/saas/empresas/{empresa}/modulos/{modulo}/toggle`
Activa/desactiva un módulo para una empresa específica. Crea el registro pivot si no existe.

---

## Uso en módulos SaaS

Proteger rutas que la app SaaS consume:

```php
// routes/pdf.php (ejemplo de futuro módulo)
Route::middleware(['saas.auth', 'saas.module:pdf'])->group(function () {
    Route::post('/generate', GeneratePdfController::class);
});
```

Header requerido: `X-Api-Key: sk_AbCdEfGhIj...`

---

## Cache

| Cache key                   | Contenido                          | Se invalida cuando                                |
|-----------------------------|------------------------------------|---------------------------------------------------|
| `saas_key:{key_prefix}`     | hash + empresa_id + activo flags   | Se genera o revoca la key / cambia estado empresa |
| `saas_modules:{empresa_id}` | array de `nombre_modulo` activos   | Se toggle módulo por empresa o globalmente        |

TTL fallback: 120 minutos. La invalidación es explícita e inmediata.

---

## Flujo de autenticación M2M

```
POST /api/saas/pdf/generate
X-Api-Key: sk_AbCdEf...

1. ValidateSaasKey
   → extrae key_prefix (8 chars)
   → busca en cache saas_key:{prefix}
   → verifica sha256(raw_key) == api_key_hash
   → verifica empresa.estado = true
   → inyecta $empresa en request attributes

2. RequireSaasModule('pdf')
   → carga saas_modules:{empresa_id} del cache
   → verifica 'pdf' ∈ módulos activos

3. Ejecuta el controller del módulo
```
