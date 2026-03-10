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
    'registered'      => 'Usuario registrado exitosamente.',

    // Profile
    'profile_updated'          => 'Perfil actualizado exitosamente.',
    'password_changed'         => 'Contraseña cambiada exitosamente.',
    'invalid_current_password' => 'La contraseña actual es incorrecta.',

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
