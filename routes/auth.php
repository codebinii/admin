<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutAllController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResendVerificationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SendWhatsAppOtpController;
use App\Http\Controllers\Auth\UpdateProfileController;
use App\Http\Controllers\Auth\VerifyWhatsAppController;
use Illuminate\Support\Facades\Route;

// Public
Route::middleware('throttle:auth.login')->post('/login', LoginController::class);
Route::middleware('throttle:auth.register')->post('/register', RegisterController::class);

// Password recovery (throttled to 5 attempts per minute)
Route::middleware('throttle:5,1')->group(function (): void {
    Route::post('/password/forgot', ForgotPasswordController::class);
    Route::post('/password/reset',  ResetPasswordController::class);
});

// Email verification (signed URL, no token required)
Route::get('/email/verify/{id}/{hash}', EmailVerificationController::class)
    ->name('auth.email.verify');

// Protected
Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/me',                            MeController::class);
    Route::patch('/profile',                     UpdateProfileController::class);
    Route::put('/password',                      ChangePasswordController::class);
    Route::post('/logout',                       LogoutController::class);
    Route::post('/logout-all',                   LogoutAllController::class);
    Route::post('/email/resend',                 ResendVerificationController::class)
        ->middleware('throttle:6,1');

    // WhatsApp verification
    Route::post('/whatsapp/send',   SendWhatsAppOtpController::class)->middleware('throttle:5,1');
    Route::post('/whatsapp/verify', VerifyWhatsAppController::class);
});
