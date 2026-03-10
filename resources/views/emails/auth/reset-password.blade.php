<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña — {{ $appName }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:'Segoe UI',Arial,sans-serif;-webkit-font-smoothing:antialiased;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:40px 16px;">
        <tr>
            <td align="center">

                {{-- Card --}}
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.06);">

                    {{-- Header --}}
                    <tr>
                        <td style="background:#18181b;padding:32px 40px;text-align:center;">
                            <p style="margin:0;font-size:20px;font-weight:700;color:#ffffff;letter-spacing:.5px;">
                                {{ $appName }}
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:40px 40px 32px;">

                            <p style="margin:0 0 8px;font-size:22px;font-weight:600;color:#18181b;">
                                Restablece tu contraseña
                            </p>

                            <p style="margin:0 0 28px;font-size:15px;color:#71717a;line-height:1.6;">
                                Hola <strong style="color:#18181b;">{{ $user->name }}</strong>,
                                recibimos una solicitud para restablecer la contraseña de tu cuenta.
                            </p>

                            @if($resetUrl)
                            {{-- CTA Button --}}
                            <table cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
                                <tr>
                                    <td style="background:#18181b;border-radius:8px;">
                                        <a href="{{ $resetUrl }}"
                                           style="display:inline-block;padding:13px 32px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;letter-spacing:.2px;">
                                            Restablecer contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 20px;font-size:13px;color:#a1a1aa;line-height:1.5;">
                                Si el botón no funciona, copia y pega este enlace en tu navegador:
                            </p>
                            <p style="margin:0 0 28px;font-size:11px;word-break:break-all;">
                                <a href="{{ $resetUrl }}" style="color:#71717a;text-decoration:underline;">{{ $resetUrl }}</a>
                            </p>

                            {{-- Divider --}}
                            <hr style="border:none;border-top:1px solid #f0f0f0;margin:0 0 24px;">

                            {{-- Token fallback --}}
                            <p style="margin:0 0 8px;font-size:12px;color:#a1a1aa;">
                                ¿Usas la API directamente? Copia este token:
                            </p>
                            @endif

                            {{-- Token Box --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 8px;">
                                <tr>
                                    <td style="background:#f4f4f5;border:1px dashed #d4d4d8;border-radius:8px;padding:16px 20px;text-align:center;">
                                        <p style="margin:0 0 6px;font-size:11px;color:#a1a1aa;text-transform:uppercase;letter-spacing:.8px;">
                                            Token de restablecimiento
                                        </p>
                                        <p style="margin:0;font-family:'Courier New',monospace;font-size:13px;font-weight:700;color:#18181b;letter-spacing:1px;word-break:break-all;line-height:1.6;">
                                            {{ $token }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 24px;font-size:12px;color:#a1a1aa;line-height:1.5;">
                                Selecciona el texto del token, cópialo y úsalo en el endpoint
                                <code style="font-size:11px;color:#71717a;background:#f4f4f5;padding:1px 5px;border-radius:3px;">POST /api/auth/password/reset</code>
                                junto con tu email y la nueva contraseña.
                            </p>

                            {{-- Warning --}}
                            <p style="margin:0;font-size:13px;color:#a1a1aa;line-height:1.5;">
                                Este enlace y token expiran en <strong style="color:#71717a;">{{ $expiresMins }} minutos</strong>.
                                Si no solicitaste este cambio, ignora este mensaje — tu contraseña no será modificada.
                            </p>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#fafafa;border-top:1px solid #f0f0f0;padding:20px 40px;text-align:center;">
                            <p style="margin:0;font-size:12px;color:#a1a1aa;">
                                © {{ date('Y') }} {{ $appName }} &nbsp;·&nbsp;
                                <a href="https://www.codebini.com/support" style="color:#a1a1aa;text-decoration:none;">Soporte</a>
                            </p>
                        </td>
                    </tr>

                </table>
                {{-- /Card --}}

            </td>
        </tr>
    </table>

</body>
</html>
