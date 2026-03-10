<?php

declare(strict_types=1);

return [

    // General
    'ok'              => 'OK',
    'server_running'  => 'Server is running.',

    // Success
    'created'         => 'Resource created successfully.',
    'no_content'      => 'No content.',
    'logged_in'       => 'Login successful.',
    'logged_out'      => 'Logged out successfully.',
    'logged_out_all'  => 'All sessions closed successfully.',
    'registered'      => 'User registered successfully.',

    // Errors — RFC 7807
    'bad_request'     => 'Bad request.',
    'unauthorized'    => 'Unauthenticated. Please log in.',
    'invalid_credentials' => 'Invalid credentials.',
    'forbidden'       => 'This action is unauthorized.',
    'not_found'       => ':model not found.',
    'server_error'    => 'An unexpected error occurred.',
    'too_many_requests' => 'Too many requests. Please slow down.',
    'validation_detail' => 'The given data was invalid.',

    // Route not found
    'route_not_found_title'      => 'Route Not Found',
    'route_not_found_detail'     => "The path ':path' does not exist in this API.",
    'method_not_allowed_title'   => 'Method Not Allowed',
    'method_not_allowed_detail'  => "The HTTP method used is not allowed for: :path",
    'route_suggestion'           => 'Please verify the endpoint path and HTTP method, or contact our support team.',

];
