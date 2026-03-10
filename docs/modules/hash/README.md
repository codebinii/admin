# Módulo Hash

Utilidades criptográficas para el panel admin. Sin DB, sin estado — entrada de parámetros, salida calculada al instante.

---

## Endpoints

### `POST /api/hash` 🔒

Genera el hash del texto de entrada usando todos los algoritmos soportados por la instalación PHP (`hash_algos()`), más `bcrypt` y `base64`.

**Body:**
```json
{ "texto": "texto de ejemplo" }
```

> Límite: 5000 caracteres. `bcrypt` varía en cada ejecución por el salt aleatorio.

**Response `200`:**
```json
{
  "success": true,
  "data": {
    "texto": "texto de ejemplo",
    "total": 62,
    "hashes": [
      { "algoritmo": "md5",     "valor": "f0e166dc34d14d6c228ffac576c9a43c" },
      { "algoritmo": "sha1",    "valor": "a6d56e93e9b25a5509db372e9f4b3d2b9db4c12f" },
      { "algoritmo": "sha256",  "valor": "3c9683017f9e4bf33d0fbedd26bf143fd72de9b9..." },
      { "algoritmo": "sha512",  "valor": "..." },
      { "algoritmo": "bcrypt",  "valor": "$2y$12$..." },
      { "algoritmo": "base64",  "valor": "dGV4dG8gZGUgZWplbXBsbw==" },
      { "...": "..." }
    ]
  }
}
```

**Algoritmos incluidos:** 60 via `hash_algos()` (md2, md4, md5, sha1, sha224, sha256, sha384, sha512, sha3-256, sha3-512, ripemd, whirlpool, crc32, fnv, xxh, haval, ...) + bcrypt + base64.

---

### `GET /api/hash/clave` 🔒

Genera una clave/contraseña aleatoria criptográficamente segura (`random_int`).

**Query params (todos opcionales):**

| Param | Tipo | Default | Descripción |
|---|---|---|---|
| `longitud` | integer | `12` | Longitud de la clave (min: 1, max: 256) |
| `numeros` | boolean | `true` | Incluir dígitos `0-9` |
| `minusculas` | boolean | `true` | Incluir letras minúsculas `a-z` |
| `mayusculas` | boolean | `true` | Incluir letras mayúsculas `A-Z` |
| `especiales` | boolean | `true` | Incluir caracteres especiales `!@#$%^&*...` |

> Al menos un conjunto de caracteres debe estar activo. Si todos son `false`, retorna `422`.

**Ejemplo de request:**
```
GET /api/hash/clave?longitud=16&especiales=false
```

**Response `200`:**
```json
{
  "success": true,
  "data": {
    "clave": "aB3xZ9mK2wQr4nPv",
    "longitud": 16,
    "opciones": {
      "numeros": true,
      "minusculas": true,
      "mayusculas": true,
      "especiales": false
    }
  }
}
```

**Response `422` (todos los conjuntos desactivados):**
```json
{
  "success": false,
  "message": "Al menos un conjunto de caracteres debe estar activo.",
  "errors": {
    "opciones": ["Al menos un conjunto de caracteres debe estar activo."]
  }
}
```

---

## Estructura

```
app/Http/
├── Controllers/Hash/
│   ├── HashController.php           ← invokable, POST /
│   └── GenerarClaveController.php   ← invokable, GET /clave
└── Requests/Hash/
    ├── HashRequest.php              ← valida texto max:5000
    └── GenerarClaveRequest.php      ← valida params + al menos 1 charset activo

routes/hash.php
```
