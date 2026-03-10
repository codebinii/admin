<?php

declare(strict_types=1);

use App\Http\Controllers\Hash\GenerarClaveController;
use App\Http\Controllers\Hash\HashController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/', HashController::class);
    Route::get('/clave', GenerarClaveController::class);
});
