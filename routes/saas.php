<?php

declare(strict_types=1);

use App\Http\Controllers\Saas\ApiKeyController;
use App\Http\Controllers\Saas\EmpresaController;
use App\Http\Controllers\Saas\ModuloController;
use Illuminate\Support\Facades\Route;

// Admin management routes — require authenticated admin user
Route::middleware('auth:sanctum')->group(function (): void {

    // Empresa listing & detail
    Route::get('/empresas',       [EmpresaController::class, 'index']);
    Route::get('/empresas/{empresa}', [EmpresaController::class, 'show']);

    // API key management
    Route::post('/empresas/{empresa}/keys',          [ApiKeyController::class, 'store']);
    Route::delete('/empresas/{empresa}/keys/{key}',  [ApiKeyController::class, 'destroy']);

    // Module management
    Route::get('/modulos',                                          [ModuloController::class, 'index']);
    Route::patch('/modulos/{modulo}/toggle',                        [ModuloController::class, 'toggleGlobal']);
    Route::put('/empresas/{empresa}/modulos',                       [ModuloController::class, 'syncEmpresa']);
    Route::post('/empresas/{empresa}/modulos/{modulo}/toggle',      [ModuloController::class, 'toggleEmpresa']);
});
