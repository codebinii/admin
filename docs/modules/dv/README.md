# Módulo DV DIAN

Calculadora del Dígito de Verificación (DV) según el algoritmo oficial de la DIAN Colombia. Sin DB, sin estado — recibe NITs, retorna DV calculado.

---

## Endpoint

### `POST /api/dv` 🔒

Calcula el DV para uno o varios NITs en una sola solicitud.

**Body:**
```json
{
  "nits": ["900123456", "800123456-7", "8001234567"]
}
```

| Campo | Tipo | Requerido | Descripción |
|---|---|---|---|
| `nits` | array | ✅ | Lista de NITs (min: 1, max: 100) |
| `nits.*` | string | ✅ | NIT con o sin guión/DV (max: 20 caracteres) |

> Si el NIT viene con guión (ej. `900123456-7`), el DV incluido se ignora y siempre se recalcula.

**Response `200`:**
```json
{
  "success": true,
  "data": {
    "total": 3,
    "resultados": [
      {
        "nit": "900123456",
        "dv": 7,
        "nit_completo": "900123456-7"
      },
      {
        "nit": "800123456",
        "dv": 1,
        "nit_completo": "800123456-1"
      },
      {
        "nit": "abc123",
        "dv": null,
        "error": "El NIT contiene caracteres no numéricos."
      }
    ]
  }
}
```

> Los errores por NIT individual no generan un `422` global — se retornan dentro del array `resultados` con `dv: null` y `error`. Esto permite procesar lotes mixtos sin interrumpir el batch completo.

---

## Algoritmo

Implementación exacta del algoritmo oficial DIAN:

1. Limpiar el NIT (trim, separar guión si existe).
2. Validar que sea numérico y máx. 15 dígitos.
3. Multiplicar cada dígito por su peso (de derecha a izquierda):

| Posición (desde derecha) | Peso |
|---|---|
| 0 | 3 |
| 1 | 7 |
| 2 | 13 |
| 3 | 17 |
| 4 | 19 |
| 5 | 23 |
| 6 | 29 |
| 7 | 37 |
| 8 | 41 |
| 9 | 43 |
| 10 | 47 |
| 11 | 53 |
| 12 | 59 |
| 13 | 67 |
| 14 | 71 |

4. Sumar todos los productos.
5. `modulo = suma % 11`
6. Si `modulo >= 2` → `DV = 11 - modulo`, de lo contrario `DV = modulo`.

---

## Estructura

```
app/
├── Services/Dv/DvService.php              ← lógica pura del algoritmo DIAN
└── Http/
    ├── Controllers/Dv/DvController.php    ← invokable
    └── Requests/Dv/DvRequest.php          ← valida array nits max:100

routes/dv.php
docs/modules/dv/README.md
```
