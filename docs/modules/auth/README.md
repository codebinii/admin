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
│   │   ├── MeController.php
│   │   ├── UpdateProfileController.php
│   │   ├── ChangePasswordController.php
│   │   ├── EmailVerificationController.php
│   │   ├── ResendVerificationController.php
│   │   ├── ForgotPasswordController.php
│   │   ├── ResetPasswordController.php
│   │   ├── SessionsController.php
│   │   ├── RevokeSessionController.php
│   │   ├── SendWhatsAppOtpController.php
│   │   ├── VerifyWhatsAppController.php
│   │   ├── SendPhoneOtpController.php
│   │   ├── VerifyPhoneController.php
│   │   └── RefreshTokenController.php
│   ├── Requests/Auth/
│   │   ├── LoginRequest.php
│   │   ├── RegisterRequest.php
│   │   ├── UpdateProfileRequest.php
│   │   ├── ChangePasswordRequest.php
│   │   ├── ForgotPasswordRequest.php
│   │   ├── ResetPasswordRequest.php
│   │   ├── VerifyWhatsAppRequest.php
│   │   └── VerifyPhoneRequest.php
│   └── Resources/Auth/
│       └── UserResource.php
├── Models/
│   ├── User.php
│   ├── ConfigDefecto.php       ← solo lectura, tabla 03config_defecto
│   └── CanalSoporte.php        ← solo lectura, tabla canales_soporte
├── Notifications/Auth/
│   ├── VerifyEmailNotification.php
│   └── ResetPasswordNotification.php
└── Services/Auth/
    ├── AuthService.php
    ├── OtpVerificationService.php  ← abstract: lógica OTP/cache/lock compartida
    ├── WhatsAppVerificationService.php
    └── PhoneVerificationService.php

routes/auth.php
lang/{en,es}/api.php
lang/{en,es}/auth.php          ← subjects de email: verify_subject, reset_subject
resources/views/emails/auth/
    ├── verify.blade.php
    └── reset-password.blade.php
```

---

## Endpoints

Base path: `/api/auth`

### POST `/api/auth/register`

Crea un nuevo usuario y devuelve token. Envía email de verificación automáticamente.

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
  "message": "Usuario registrado exitosamente.",
  "data": {
    "user":  { ...UserResource },
    "token": "1|abcxyz..."
  }
}
```

---

### POST `/api/auth/login`

Autentica credenciales y devuelve token.

**Rate limit:** 10 req/min por IP

**Body:**
```json
{ "email": "vidal@example.com", "password": "secret1234", "device_name": "mi-app" }
```

**Response `200`:** `{ "data": { "token": "2|abcxyz..." } }`
**Response `401`:** credenciales inválidas

---

### GET `/api/auth/me` 🔒

Devuelve el perfil del usuario autenticado.

---

### PATCH `/api/auth/profile` 🔒

Actualiza nombre, email, phone, whatsapp.
Si cambia el email, resetea `email_verified_at` y reenvía verificación.

**Body:** cualquier combinación de `name`, `email`, `phone`, `whatsapp`

---

### PUT `/api/auth/password` 🔒

Cambia la contraseña del usuario autenticado.

**Body:** `{ "current_password", "password", "password_confirmation" }`

---

### POST `/api/auth/token/refresh` 🔒

Rota el token actual: revoca el existente y emite uno nuevo con el mismo `device_name`.

**Response `200`:** `{ "data": { "token": "3|newtoken..." } }`

---

### POST `/api/auth/logout` 🔒

Revoca el token del dispositivo actual.

---

### POST `/api/auth/logout-all` 🔒

Revoca **todos** los tokens activos (todos los dispositivos).

---

### GET `/api/auth/email/verify/{id}/{hash}`

Verifica el email desde el enlace firmado recibido por correo.
URL firmada y temporal — no requiere token de autenticación.

---

### POST `/api/auth/email/resend` 🔒

Reenvía el correo de verificación.

**Rate limit:** 6 req/min

---

### POST `/api/auth/password/forgot`

Solicita enlace de recuperación de contraseña. Envía email con token.

**Rate limit:** 5 req/min

**Body:** `{ "email": "vidal@example.com" }`

**Response `200`:** siempre éxito (no revela si el email existe)

---

### POST `/api/auth/password/reset`

Restablece la contraseña con el token recibido por email.
Revoca **todos** los tokens Sanctum activos al completar.

**Rate limit:** 5 req/min

**Body:**
```json
{
  "email":                 "vidal@example.com",
  "token":                 "abc123...",
  "password":              "nuevaclave123",
  "password_confirmation": "nuevaclave123"
}
```

---

### POST `/api/auth/whatsapp/send` 🔒

Genera un OTP de 6 dígitos y lo envía al número `whatsapp` del usuario via Meta Cloud API (template: `verificacion`).

**Rate limit:** 5 req/min

**Requisitos:** el usuario debe tener `whatsapp` registrado y no verificado.

**Response `200`:**
```json
{
  "success": true,
  "message": "Código de verificación enviado por WhatsApp.",
  "data": { "expires_in_minutes": 5 }
}
```

**Errores:**
- `400` — sin número WhatsApp registrado / ya verificado
- `500` — error de Meta Cloud API

---

### POST `/api/auth/whatsapp/verify` 🔒

Valida el OTP recibido por WhatsApp y marca `whatsapp_verified_at`.

**Body:** `{ "code": "123456" }`

**Response `200`:** devuelve `UserResource` con `whatsapp_verified_at` actualizado.
**Response `400`:** código incorrecto o expirado.

---

### GET `/api/auth/sessions` 🔒

Lista todos los tokens activos del usuario.

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "mi-app",
      "last_used_at": "2026-03-09T10:00:00Z",
      "created_at": "2026-03-01T08:00:00Z",
      "expires_at": null,
      "current": true
    }
  ]
}
```

---

### DELETE `/api/auth/sessions/{id}` 🔒

Revoca un token específico por ID.

**Response `200`:** sesión revocada.
**Response `404`:** token no encontrado o no pertenece al usuario.

---

### POST `/api/auth/phone/send` 🔒

Genera un OTP de 6 dígitos y lo envía al número `phone` del usuario via SMS.

**Rate limit:** 5 req/min

**Requisitos:** el usuario debe tener `phone` registrado y no verificado. Resend lock de **30 minutos**.

**Response `200`:**
```json
{
  "success": true,
  "message": "Código de verificación enviado por SMS.",
  "data": { "otp_expires_in": 5, "resend_in": 30 }
}
```

**Errores:**
- `400` — sin teléfono registrado / ya verificado / lock activo (incluye `minutes` restantes en el mensaje)
- `500` — error de SMS API

---

### POST `/api/auth/phone/verify` 🔒

Valida el OTP recibido por SMS y marca `phone_verified_at`.

**Body:** `{ "code": "123456" }`

**Response `200`:** devuelve `UserResource` con `phone_verified_at` actualizado.
**Response `400`:** código incorrecto o expirado.

---

## WhatsApp — Configuración Meta Cloud API

Credenciales leídas desde `03config_defecto` (tabla compartida, solo lectura):

| Campo                          | Uso                                  |
|--------------------------------|--------------------------------------|
| `whatsapp_phone_id`            | Phone Number ID del endpoint         |
| `whatsapp_token`               | Bearer token de acceso               |
| `whatsapp_api_version`         | Versión de Graph API (ej. `v22.0`)   |
| `whatsapp_template_verificacion` | Nombre de plantilla aprobada       |
| `tiempo_otp`                   | Minutos de vigencia del OTP (cache)  |

**Endpoint Meta:** `POST https://graph.facebook.com/{version}/{phone_id}/messages`

**Payload enviado:**
```json
{
  "messaging_product": "whatsapp",
  "to": "573155533324",
  "type": "template",
  "template": {
    "name": "verificacion",
    "language": { "code": "es" },
    "components": [
      {
        "type": "body",
        "parameters": [
          { "type": "text", "text": "834521" },
          { "type": "text", "text": "+57315553324" }
        ]
      },
      {
        "type": "button",
        "sub_type": "url",
        "index": "0",
        "parameters": [
          { "type": "text", "text": "834521" }
        ]
      }
    ]
  }
}
```

- `body {{1}}` → OTP de 6 dígitos
- `body {{2}}` → contacto de soporte WhatsApp de `canales_soporte` (sin espacios, máx 15 chars)
- `button url {{1}}` → OTP como sufijo dinámico de la URL del botón

El número del destinatario se normaliza a dígitos puros antes del envío (ej. `+57 315 553 3324` → `573155533324`).

---

## Phone (SMS) — Configuración

Credenciales leídas desde `03config_defecto`:

| Campo          | Uso                                |
|----------------|------------------------------------|
| `sms_url`      | Endpoint del proveedor SMS         |
| `sms_token`    | Token de autenticación             |
| `sms_user`     | Email/usuario de la cuenta         |
| `sms_type_send`| Tipo de envío del proveedor        |
| `tiempo_otp`   | Minutos de vigencia del OTP        |

**Endpoint:** `POST https://contacto-virtual.com/a/api/send/sms/json`

**Payload:**
```json
{
  "token": "...",
  "email": "cuenta@ejemplo.com",
  "type_send": "sms",
  "data": [{ "cellphone": "573155533324", "message": "Tu código de verificación de AppName es: 834521. Válido por unos minutos." }]
}
```

Mensaje traducible via `lang/{locale}/sms.php` → clave `otp_message`.

---

## Modelo User

Tabla: `users`

| Campo                  | Tipo        | Notas                        |
|------------------------|-------------|------------------------------|
| `id`                   | ULID (PK)   |                              |
| `name`                 | string      |                              |
| `email`                | string      | único                        |
| `password`             | string      | bcrypt, oculto               |
| `phone`                | string null |                              |
| `phone_verified_at`    | timestamp   | null = no verificado         |
| `whatsapp`             | string null |                              |
| `whatsapp_verified_at` | timestamp   | null = no verificado         |
| `email_verified_at`    | timestamp   | null = no verificado         |
| `created_at`           | timestamp   |                              |

---

## Seguridad

| Medida                   | Detalle                                                  |
|--------------------------|----------------------------------------------------------|
| Rate limit login           | 10 req/min por IP                                          |
| Rate limit register        | 5 req/min por IP                                           |
| Rate limit whatsapp/phone  | 5 req/min por usuario                                      |
| Resend lock OTP            | 30 min por canal (WhatsApp y Phone independientes)         |
| Tokens Sanctum             | Stateless, revocables por dispositivo o masivo             |
| Expiración tokens          | `SANCTUM_TOKEN_EXPIRATION=1440` (24 h)                     |
| OTP                        | 6 dígitos, cache TTL configurable (`tiempo_otp` minutos)   |
| Reset password             | Revoca todos los tokens Sanctum al completar               |
| Campos ocultos             | `password` y `remember_token` excluidos del JSON           |

---

## Uso del token

```
Authorization: Bearer {token}
```

---

## Internacionalización

Mensajes desde `lang/{locale}/api.php`. Configurar en `.env`:

```env
APP_LOCALE=es   # español
APP_LOCALE=en   # inglés
```
