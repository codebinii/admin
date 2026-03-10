<?php

declare(strict_types=1);

return [

    // General
    'ok'              => 'OK',
    'server_running'  => 'El servidor está en línea.',

    // Success
    'created'         => 'Recurso creado exitosamente.',
    'no_content'      => 'Sin contenido.',
    'logged_in'       => 'Inicio de sesión exitoso.',
    'logged_out'      => 'Sesión cerrada exitosamente.',
    'logged_out_all'  => 'Todas las sesiones fueron cerradas exitosamente.',
    'session_revoked' => 'Sesión revocada exitosamente.',
    'token_refreshed' => 'Token renovado exitosamente.',
    'registered'      => 'Usuario registrado exitosamente.',

    // Profile
    'profile_updated'          => 'Perfil actualizado exitosamente.',
    'password_changed'         => 'Contraseña cambiada exitosamente.',
    'invalid_current_password' => 'La contraseña actual es incorrecta.',

    // Phone (SMS) verification
    'phone_otp_sent'          => 'Código de verificación enviado por SMS.',
    'phone_otp_resend_locked' => 'Debes esperar :minutes minuto(s) antes de solicitar un nuevo código.',
    'phone_verified'          => 'Número de teléfono verificado exitosamente.',
    'phone_already_verified'  => 'El número de teléfono ya está verificado.',
    'phone_not_set'           => 'No tienes un número de teléfono registrado en tu cuenta.',
    'phone_otp_invalid'       => 'El código es incorrecto o ha expirado.',

    // WhatsApp verification
    'whatsapp_otp_sent'         => 'Código de verificación enviado por WhatsApp.',
    'whatsapp_otp_resend_locked' => 'Debes esperar :minutes minuto(s) antes de solicitar un nuevo código.',
    'whatsapp_verified'         => 'WhatsApp verificado exitosamente.',
    'whatsapp_already_verified' => 'El número de WhatsApp ya está verificado.',
    'whatsapp_not_set'          => 'No tienes un número de WhatsApp registrado en tu cuenta.',
    'whatsapp_otp_invalid'      => 'El código es incorrecto o ha expirado.',

    // Password recovery
    'password_reset_sent'          => 'Si tu correo está registrado, recibirás un enlace para restablecer tu contraseña.',
    'password_reset_success'       => 'Contraseña restablecida exitosamente. Inicia sesión con tu nueva contraseña.',
    'password_reset_failed'        => 'No fue posible enviar el correo de recuperación. Verifica el email ingresado.',
    'password_reset_invalid_token' => 'El token de restablecimiento es inválido o ha expirado.',

    // Email verification
    'verification_sent'          => 'Correo de verificación enviado.',
    'email_verified'             => 'Correo verificado exitosamente.',
    'email_already_verified'     => 'El correo ya está verificado.',
    'verification_link_invalid'  => 'El enlace de verificación es inválido o ha expirado.',

    // Errors — RFC 7807
    'bad_request'     => 'Solicitud incorrecta.',
    'unauthorized'    => 'No autenticado. Por favor inicia sesión.',
    'invalid_credentials' => 'Credenciales inválidas.',
    'forbidden'       => 'No tienes permiso para realizar esta acción.',
    'not_found'       => ':model no encontrado.',
    'server_error'         => 'Ocurrió un error inesperado.',
    'server_error_support' => 'Por favor contacta soporte indicando el código error_code.',
    'too_many_requests' => 'Demasiadas solicitudes. Por favor espera un momento.',
    'validation_detail' => 'Los datos proporcionados no son válidos.',

    // Route not found
    'route_not_found_title'      => 'Ruta No Encontrada',
    'route_not_found_detail'     => "La ruta ':path' no existe en esta API.",
    'method_not_allowed_title'   => 'Método No Permitido',
    'method_not_allowed_detail'  => "El método HTTP utilizado no está permitido para: :path",
    'route_suggestion'           => 'Por favor verifica la ruta y el método HTTP, o contacta a nuestro equipo de soporte.',

];
