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
    'session_revoked' => 'Session revoked successfully.',
    'token_refreshed' => 'Token refreshed successfully.',
    'registered'      => 'User registered successfully.',

    // Profile
    'profile_updated'          => 'Profile updated successfully.',
    'password_changed'         => 'Password changed successfully.',
    'invalid_current_password' => 'The current password is incorrect.',

    // Phone (SMS) verification
    'phone_otp_sent'          => 'Verification code sent via SMS.',
    'phone_otp_resend_locked' => 'Please wait :minutes minute(s) before requesting a new code.',
    'phone_verified'          => 'Phone number verified successfully.',
    'phone_already_verified'  => 'Your phone number is already verified.',
    'phone_not_set'           => 'You have no phone number registered on your account.',
    'phone_otp_invalid'       => 'The code is incorrect or has expired.',

    // WhatsApp verification
    'whatsapp_otp_sent'         => 'Verification code sent via WhatsApp.',
    'whatsapp_otp_resend_locked' => 'Please wait :minutes minute(s) before requesting a new code.',
    'whatsapp_verified'         => 'WhatsApp verified successfully.',
    'whatsapp_already_verified' => 'Your WhatsApp number is already verified.',
    'whatsapp_not_set'          => 'You have no WhatsApp number registered on your account.',
    'whatsapp_otp_invalid'      => 'The code is incorrect or has expired.',

    // Password recovery
    'password_reset_sent'          => 'If your email is registered, you will receive a password reset link shortly.',
    'password_reset_success'       => 'Password reset successfully. Please log in with your new password.',
    'password_reset_failed'        => 'Unable to send the recovery email. Please verify the email address provided.',
    'password_reset_invalid_token' => 'The password reset token is invalid or has expired.',

    // Email verification
    'verification_sent'          => 'Verification email sent.',
    'email_verified'             => 'Email verified successfully.',
    'email_already_verified'     => 'Email is already verified.',
    'verification_link_invalid'  => 'The verification link is invalid or has expired.',

    // Errors — RFC 7807
    'bad_request'     => 'Bad request.',
    'unauthorized'    => 'Unauthenticated. Please log in.',
    'invalid_credentials' => 'Invalid credentials.',
    'forbidden'       => 'This action is unauthorized.',
    'not_found'       => ':model not found.',
    'server_error'         => 'An unexpected error occurred.',
    'server_error_support' => 'Please contact support with the error_code provided.',
    'too_many_requests' => 'Too many requests. Please slow down.',
    'validation_detail' => 'The given data was invalid.',

    // Route not found
    'route_not_found_title'      => 'Route Not Found',
    'route_not_found_detail'     => "The path ':path' does not exist in this API.",
    'method_not_allowed_title'   => 'Method Not Allowed',
    'method_not_allowed_detail'  => "The HTTP method used is not allowed for: :path",
    'route_suggestion'           => 'Please verify the endpoint path and HTTP method, or contact our support team.',

];
