<?php

use App\Core\Http\Controllers\{AuthController, SchoolController,};
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
    Route::apiResource('schools', SchoolController::class);
    Route::get('schools/{id}/statistics', [SchoolController::class, 'statistics']);
});

