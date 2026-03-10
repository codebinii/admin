<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutAllController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:auth.login')->post('/login', LoginController::class);
Route::middleware('throttle:auth.register')->post('/register', RegisterController::class);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout',     LogoutController::class);
    Route::post('/logout-all', LogoutAllController::class);
    Route::get('/me',          MeController::class);
});
