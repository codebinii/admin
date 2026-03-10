# Módulo SaaS Access

Gestión de empresas clientes del SaaS: API keys de acceso y control de módulos habilitados por empresa.

> **Para el desarrollador frontend/UX:** las secciones marcadas con 🖥️ son las relevantes para ti. Las demás son arquitectura interna.

---

## Base URL

```
https://{dominio}/api/saas
```

Todos los endpoints de administración requieren el token del usuario admin:

```
Authorization: Bearer {token_del_admin}
Content-Type: application/json
```

---

## 🖥️ Empresas

### Listar empresas

```
GET /api/saas/empresas
```

Devuelve lista paginada. Útil para el listado principal del panel.

**Response `200`:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "cod_empresa": "EMP001",
      "nombre": "Empresa Ejemplo S.A.S",
      "sigla": "EE",
      "pais": "CO",
      "estado": true,
      "email_admin": "admin@ejemplo.com",
      "no_celular": "+57 300 000 0000",
      "created_at": "2024-01-15T10:00:00Z"
    }
  ],
  "meta": {
    "pagination": {
      "total": 42,
      "per_page": 25,
      "current_page": 1,
      "last_page": 2
    }
  }
}
```

---

### Detalle de empresa

```
GET /api/saas/empresas/{id}
```

Devuelve la empresa con sus módulos asignados y sus API keys. Usar para cargar la pantalla de configuración de una empresa.

**Response `200`:**
```json
{
  "success": true,
  "data": {
    "empresa": {
      "id": 1,
      "cod_empresa": "EMP001",
      "nombre": "Empresa Ejemplo S.A.S",
      "sigla": "EE",
      "pais": "CO",
      "estado": true,
      "email_admin": "admin@ejemplo.com",
      "no_celular": "+57 300 000 0000",
      "created_at": "2024-01-15T10:00:00Z"
    },
    "modulos": [
      {
        "id": 1,
        "nombre_modulo": "pdf",
        "activo": true,
        "activo_empresa": true
      },
      {
        "id": 2,
        "nombre_modulo": "facturacion",
        "activo": true,
        "activo_empresa": false
      },
      {
        "id": 3,
        "nombre_modulo": "reportes",
        "activo": false,
        "activo_empresa": true
      }
    ],
    "keys": [
      {
        "id": "01jfx...",
        "nombre": "producción",
        "key_prefix": "sk_AbCdEf",
        "activo": true,
        "created_at": "2024-01-15T10:00:00Z"
      }
    ]
  }
}
```

> **Nota sobre módulos:**
> - `activo` = interruptor global (lo controla el súper admin, afecta a todas las empresas)
> - `activo_empresa` = interruptor por empresa (lo configuras tú desde esta pantalla)
> - Un módulo **funciona** solo cuando ambos están en `true`
> - Si `activo = false`, el módulo aparece deshabilitado globalmente — muéstraselo al usuario como bloqueado

---

## 🖥️ Gestión de módulos por empresa

### Guardar configuración de módulos *(recomendado)*

```
PUT /api/saas/empresas/{id}/modulos
```

Envía la lista completa de IDs de módulos que deben estar **activos** para esta empresa. Los que no aparezcan en la lista quedan desactivados.

Diseñado para pantallas tipo "checklist" donde el usuario configura todo y presiona **Guardar**.

**Body:**
```json
{
  "modulos": [1, 3]
}
```

> Para desactivar **todos** los módulos: enviar `"modulos": []`

**Response `200`:**
```json
{
  "success": true,
  "message": "Módulos de la empresa actualizados.",
  "data": [
    {
      "id": 1,
      "nombre_modulo": "pdf",
      "activo": true,
      "activo_empresa": true
    },
    {
      "id": 2,
      "nombre_modulo": "facturacion",
      "activo": true,
      "activo_empresa": false
    }
  ]
}
```

**Flujo UX sugerido:**
```
1. Cargar GET /empresas/{id}  →  obtener modulos con activo_empresa actual
2. Mostrar lista de módulos con toggles/checkboxes
3. Usuario activa/desactiva localmente (sin llamadas al API)
4. Usuario presiona "Guardar"
5. Enviar PUT /empresas/{id}/modulos con los IDs activos
6. Actualizar UI con la respuesta
```

---

### Toggle rápido de un módulo *(acción inmediata)*

```
POST /api/saas/empresas/{empresa_id}/modulos/{modulo_id}/toggle
```

Cambia el estado de un módulo específico sin necesidad de botón de guardar. Útil para listas con switches que responden inmediatamente.

**Response `200`:**
```json
{
  "success": true,
  "message": "Estado del módulo actualizado.",
  "data": { "activo": true }
}
```

---

## 🖥️ Gestión de API Keys

La API key es la credencial que la app SaaS usa para conectarse a esta plataforma. **Solo se muestra una vez al generarla** — si se pierde, hay que revocar y generar una nueva.

### Generar API key

```
POST /api/saas/empresas/{id}/keys
```

**Body** *(opcional)*:
```json
{ "nombre": "producción" }
```

**Response `201`:**
```json
{
  "success": true,
  "message": "API key generada exitosamente. Guárdala — no se mostrará de nuevo.",
  "data": {
    "key": "sk_AbCdEfGhIjKlMnOpQrStUvWxYz12345678901234",
    "meta": {
      "id": "01jfx...",
      "nombre": "producción",
      "key_prefix": "sk_AbCdEf",
      "activo": true,
      "created_at": "2024-03-09T22:37:00Z"
    }
  }
}
```

> ⚠️ **Mostrar el valor de `key` en un modal con opción de copiar.** Una vez cerrado, no es recuperable. Solo `key_prefix` queda visible para identificar la key en el listado.

---

### Revocar API key

```
DELETE /api/saas/empresas/{empresa_id}/keys/{key_id}
```

Elimina la key permanentemente. La app SaaS perderá acceso de inmediato.

**Response `200`:**
```json
{
  "success": true,
  "message": "API key revocada exitosamente."
}
```

> Pedir confirmación al usuario antes de llamar este endpoint.

---

## 🖥️ Historial de auditoría

### Consultar historial

```
GET /api/saas/audit
```

Devuelve el historial paginado de acciones administrativas. Útil para revisar y confrontar cambios con el cliente SaaS.

**Query params (todos opcionales):**

| Parámetro    | Tipo    | Descripción                              |
|--------------|---------|------------------------------------------|
| `empresa_id` | integer | Filtrar por empresa                      |
| `accion`     | string  | Filtrar por tipo de acción               |
| `desde`      | date    | Fecha inicio `YYYY-MM-DD`                |
| `hasta`      | date    | Fecha fin `YYYY-MM-DD`                   |

**Acciones posibles:**

| `accion`               | Qué registra                                      |
|------------------------|---------------------------------------------------|
| `key_generated`        | Generación de nueva API key                       |
| `key_revoked`          | Revocación de API key                             |
| `module_toggled`       | Activación/desactivación de módulo por empresa    |
| `modules_synced`       | Sincronización masiva de módulos para una empresa |
| `module_global_toggled`| Cambio de estado global de un módulo              |

**Ejemplo:** historial de una empresa en una fecha

```
GET /api/saas/audit?empresa_id=1&desde=2026-03-01&hasta=2026-03-09
```

**Response `200`:**
```json
{
  "success": true,
  "data": [
    {
      "id": "01jfx...",
      "accion": "modules_synced",
      "empresa_id": 1,
      "usuario": {
        "id": 1,
        "name": "Admin",
        "email": "admin@codebini.com"
      },
      "datos": {
        "modulos_activos": [1, 3]
      },
      "created_at": "2026-03-09T22:37:00Z"
    },
    {
      "id": "01jfy...",
      "accion": "key_generated",
      "empresa_id": 1,
      "usuario": { "id": 1, "name": "Admin", "email": "admin@codebini.com" },
      "datos": {
        "key_id": "01jfz...",
        "key_prefix": "sk_AbCdEf",
        "nombre": "producción"
      },
      "created_at": "2026-03-09T22:30:00Z"
    }
  ],
  "meta": {
    "pagination": {
      "total": 48,
      "per_page": 50,
      "current_page": 1,
      "last_page": 1
    }
  }
}
```

> Para acciones globales (`module_global_toggled`), `empresa_id` es `null` ya que el cambio afecta a todas las empresas.

---

## 🖥️ Catálogo global de módulos

### Listar todos los módulos

```
GET /api/saas/modulos
```

Devuelve el catálogo completo. Usar para mostrar el estado global antes de configurar empresas.

**Response `200`:**
```json
{
  "success": true,
  "data": [
    { "id": 1, "nombre_modulo": "pdf",         "activo": true,  "created_at": "..." },
    { "id": 2, "nombre_modulo": "facturacion",  "activo": true,  "created_at": "..." },
    { "id": 3, "nombre_modulo": "reportes",     "activo": false, "created_at": "..." }
  ]
}
```

---

### Toggle global de un módulo *(súper admin)*

```
PATCH /api/saas/modulos/{id}/toggle
```

Apaga o enciende un módulo para **todas las empresas** simultáneamente. Usar con precaución.

**Response `200`:**
```json
{
  "success": true,
  "message": "Estado del módulo actualizado.",
  "data": { "activo": false }
}
```

> Si `activo` pasa a `false`, el módulo queda bloqueado para todas las empresas aunque tengan `activo_empresa: true`. Recomendable mostrar una advertencia antes de confirmar.

---

## 🖥️ Códigos de error comunes

| Código | Cuándo ocurre |
|--------|---------------|
| `401`  | Token de admin ausente o inválido |
| `403`  | Módulo deshabilitado para la empresa (en rutas SaaS) |
| `404`  | Empresa, módulo o key no encontrada |
| `422`  | Validación fallida — revisar campo `errors` en la respuesta |

**Estructura de error:**
```json
{
  "success": false,
  "status": 422,
  "title": "Unprocessable Entity",
  "detail": "Los datos proporcionados no son válidos.",
  "errors": {
    "modulos.0": ["El campo modulos.0 debe existir en 04modulos."]
  }
}
```

---

## Arquitectura interna *(para el backend)*

### Tablas propias

**`empresa_api_keys`** — credenciales M2M
| Campo          | Tipo        | Notas                                                   |
|----------------|-------------|---------------------------------------------------------|
| `id`           | ulid PK     |                                                         |
| `empresa_id`   | bigint      | FK → `01empresas.id`                                    |
| `key_prefix`   | varchar(10) | Primeros 8 chars en texto plano para lookup rápido      |
| `api_key_hash` | varchar(64) | SHA-256 de la key completa — nunca se guarda en plano   |
| `nombre`       | varchar     | Descripción (ej: "producción", "staging")               |
| `activo`       | boolean     |                                                         |

**`empresa_modulos`** — módulos asignados por empresa
| Campo        | Tipo    | Notas                              |
|--------------|---------|------------------------------------|
| `empresa_id` | bigint  | FK → `01empresas.id`, PK compuesta |
| `modulo_id`  | bigint  | FK → `04modulos.id`, PK compuesta  |
| `activo`     | boolean | Interruptor por empresa            |

### Tablas compartidas (solo lectura)

- **`01empresas`** — clientes SaaS. `estado` controla acceso global.
- **`04modulos`** — catálogo de módulos. `activo` es el interruptor global.

### Cache

| Key                         | Contenido                        | Se invalida cuando                           |
|-----------------------------|----------------------------------|----------------------------------------------|
| `saas_key:{key_prefix}`     | hash + empresa_id + activo flags | Se genera o revoca la key                    |
| `saas_modules:{empresa_id}` | array de slugs activos           | Se sincroniza, toggle por empresa o global   |

TTL fallback: 120 min. Invalidación explícita e inmediata ante cualquier cambio.

### Flujo de autenticación M2M

```
App SaaS → POST /api/saas/{modulo}/...
           Header: X-Api-Key: sk_AbCdEf...

1. ValidateSaasKey   → verifica key + empresa.estado
2. RequireSaasModule → verifica módulo activo global + por empresa
3. Controller        → procesa la request
```

Proteger rutas de módulos SaaS:
```php
Route::middleware(['saas.auth', 'saas.module:pdf'])->group(function () {
    Route::post('/generate', GeneratePdfController::class);
});
```
