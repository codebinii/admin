# Módulo Auth

Autenticación stateless via **Laravel Sanctum** (tokens de API).

---

## Estructura

```
app/
├── Http/
│   ├── Controllers/Auth/
│   │   ├── LoginController.php
│   │   ├── RegisterController.php
│   │   ├── LogoutController.php
│   │   ├── LogoutAllController.php
│   │   └── MeController.php
│   ├── Requests/Auth/
│   │   ├── LoginRequest.php
│   │   └── RegisterRequest.php
│   └── Resources/Auth/
│       └── UserResource.php
├── Models/
│   └── User.php
└── Services/Auth/
    └── AuthService.php

routes/auth.php
lang/{en,es}/api.php   ← claves: logged_in, logged_out, registered, ...
```

---

## Endpoints

Base path: `/api/auth`

### POST `/api/auth/register`

Crea un nuevo usuario y devuelve token.

**Rate limit:** 5 req/min por IP

**Body:**
```json
{
  "name":                  "Vidal Figueroa",
  "email":                 "vidal@example.com",
  "password":              "secret1234",
  "password_confirmation": "secret1234",
  "phone":                 "+57 300 000 0000",
  "whatsapp":              "+57 300 000 0000",
  "device_name":           "mi-app"
}
```

> `phone`, `whatsapp` y `device_name` son opcionales.

**Response `201`:**
```json
{
  "success": true,
  "message": "User registered successfully.",
  "data": {
    "user": {
      "id":                   "01abc...",
      "name":                 "Vidal Figueroa",
      "email":                "vidal@example.com",
      "email_verified_at":    null,
      "phone":                "+57 300 000 0000",
      "phone_verified_at":    null,
      "whatsapp":             "+57 300 000 0000",
      "whatsapp_verified_at": null,
      "created_at":           "2026-03-09T19:00:00-05:00"
    },
    "token": "1|abcxyz..."
  }
}
```

**Response `422`** — validación:
```json
{
  "success": false,
  "status":  422,
  "title":   "Unprocessable Entity",
  "detail":  "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

---

### POST `/api/auth/login`

Autentica credenciales y devuelve token.

**Rate limit:** 10 req/min por IP

**Body:**
```json
{
  "email":       "vidal@example.com",
  "password":    "secret1234",
  "device_name": "mi-app"
}
```

> `device_name` es opcional (default: `api`).

**Response `200`:**
```json
{
  "success": true,
  "message": "Login successful.",
  "data": {
    "token": "2|abcxyz..."
  }
}
```

**Response `401`** — credenciales inválidas:
```json
{
  "success": false,
  "status":  401,
  "title":   "Unauthorized",
  "detail":  "Invalid credentials."
}
```

---

### GET `/api/auth/me`

Devuelve el perfil del usuario autenticado.

**Auth:** `Bearer {token}` requerido

**Response `200`:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id":                   "01abc...",
    "name":                 "Vidal Figueroa",
    "email":                "vidal@example.com",
    "email_verified_at":    null,
    "phone":                "+57 300 000 0000",
    "phone_verified_at":    null,
    "whatsapp":             "+57 300 000 0000",
    "whatsapp_verified_at": null,
    "created_at":           "2026-03-09T19:00:00-05:00"
  }
}
```

**Response `401`** — sin token o token inválido:
```json
{
  "success": false,
  "status":  401,
  "title":   "Unauthorized",
  "detail":  "Unauthenticated. Please log in."
}
```

---

### POST `/api/auth/logout`

Revoca el token actual del dispositivo.

**Auth:** `Bearer {token}` requerido

**Response `200`:**
```json
{
  "success": true,
  "message": "Logged out successfully."
}
```

---

### POST `/api/auth/logout-all`

Revoca **todos** los tokens activos del usuario (todos los dispositivos).

**Auth:** `Bearer {token}` requerido

**Response `200`:**
```json
{
  "success": true,
  "message": "All sessions closed successfully."
}
```

---

## Modelo User

Tabla: `users`

| Campo                  | Tipo        | Notas                        |
|------------------------|-------------|------------------------------|
| `id`                   | ULID (PK)   |                              |
| `name`                 | string      |                              |
| `email`                | string      | único                        |
| `password`             | string      | bcrypt, oculto en respuestas |
| `phone`                | string null |                              |
| `phone_verified_at`    | timestamp   | null = no verificado         |
| `whatsapp`             | string null |                              |
| `whatsapp_verified_at` | timestamp   | null = no verificado         |
| `email_verified_at`    | timestamp   | null = no verificado         |
| `remember_token`       | string null | oculto en respuestas         |
| `created_at`           | timestamp   |                              |
| `updated_at`           | timestamp   |                              |

---

## Seguridad

| Medida               | Detalle                                          |
|----------------------|--------------------------------------------------|
| Rate limiting login  | 10 req/min por IP (`throttle:auth.login`)        |
| Rate limiting register | 5 req/min por IP (`throttle:auth.register`)    |
| Tokens Sanctum       | Stateless, revocables por dispositivo o masivo   |
| Contraseña           | Mínimo 8 caracteres, almacenada con bcrypt       |
| Campos ocultos       | `password` y `remember_token` excluidos del JSON |

---

## Uso del token

Incluir en el header de cada request autenticado:

```
Authorization: Bearer {token}
```

---

## Internacionalización

Todos los mensajes resuelven desde `lang/{locale}/api.php`.
Cambiar idioma en `.env`:

```env
APP_LOCALE=es   # español
APP_LOCALE=en   # inglés (default)
```
