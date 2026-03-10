<?php

use App\Http\Controllers\Status\StatusController;
use Illuminate\Support\Facades\Route;

Route::get('/', StatusController::class);

Route::prefix('auth')->group(base_path('routes/auth.php'));
Route::prefix('saas')->group(base_path('routes/saas.php'));
Route::prefix('hash')->group(base_path('routes/hash.php'));
