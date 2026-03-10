<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutAllController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResendVerificationController;
use App\Http\Controllers\Auth\UpdateProfileController;
use Illuminate\Support\Facades\Route;

// Public
Route::middleware('throttle:auth.login')->post('/login', LoginController::class);
Route::middleware('throttle:auth.register')->post('/register', RegisterController::class);

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
});
