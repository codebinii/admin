<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar correo — {{ $appName }}</title>
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
                                Confirma tu correo electrónico
                            </p>

                            <p style="margin:0 0 24px;font-size:15px;color:#71717a;line-height:1.6;">
                                Hola <strong style="color:#18181b;">{{ $user->name }}</strong>,
                                verifica tu correo para aprovechar al 100% todas las funcionalidades de tu cuenta.
                            </p>

                            {{-- CTA Button --}}
                            <table cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
                                <tr>
                                    <td style="background:#18181b;border-radius:8px;">
                                        <a href="{{ $verifyUrl }}"
                                           style="display:inline-block;padding:13px 32px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;letter-spacing:.2px;">
                                            Verificar correo
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- Warning --}}
                            <p style="margin:0 0 24px;font-size:13px;color:#a1a1aa;line-height:1.5;">
                                Este enlace expira en <strong style="color:#71717a;">{{ $expiresMins }} minutos</strong>.
                                Si no creaste esta cuenta, puedes ignorar este mensaje.
                            </p>

                            {{-- Divider --}}
                            <hr style="border:none;border-top:1px solid #f0f0f0;margin:0 0 24px;">

                            {{-- Fallback URL --}}
                            <p style="margin:0;font-size:12px;color:#a1a1aa;line-height:1.6;">
                                Si el botón no funciona, copia y pega este enlace en tu navegador:
                            </p>
                            <p style="margin:6px 0 0;font-size:11px;word-break:break-all;">
                                <a href="{{ $verifyUrl }}" style="color:#71717a;text-decoration:underline;">
                                    {{ $verifyUrl }}
                                </a>
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
