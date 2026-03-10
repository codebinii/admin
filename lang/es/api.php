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

    // Errors — RFC 7807
    'bad_request'     => 'Solicitud incorrecta.',
    'unauthorized'    => 'No autenticado. Por favor inicia sesión.',
    'invalid_credentials' => 'Credenciales inválidas.',
    'forbidden'       => 'No tienes permiso para realizar esta acción.',
    'not_found'       => ':model no encontrado.',
    'server_error'    => 'Ocurrió un error inesperado.',
    'too_many_requests' => 'Demasiadas solicitudes. Por favor espera un momento.',
    'validation_detail' => 'Los datos proporcionados no son válidos.',

    // Route not found
    'route_not_found_title'      => 'Ruta No Encontrada',
    'route_not_found_detail'     => "La ruta ':path' no existe en esta API.",
    'method_not_allowed_title'   => 'Método No Permitido',
    'method_not_allowed_detail'  => "El método HTTP utilizado no está permitido para: :path",
    'route_suggestion'           => 'Por favor verifica la ruta y el método HTTP, o contacta a nuestro equipo de soporte.',

];
