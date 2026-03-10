<?php

declare(strict_types=1);

use App\Http\Controllers\Dv\DvController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->post('/', DvController::class);
