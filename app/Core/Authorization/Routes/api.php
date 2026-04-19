<?php

use App\Core\Authorization\Http\Controllers\{AuthorizationController,
    EmployeeTitleController,
    TitleController,
    UserRightController};
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('rights', AuthorizationController::class);

    Route::apiResource('titles', TitleController::class);

    Route::post('users/{userId}/rights', [UserRightController::class, 'assign']);
    Route::delete('users/{userId}/rights', [UserRightController::class, 'remove']);
    Route::put('users/{userId}/rights', [UserRightController::class, 'sync']);

    Route::post('employees/{employeeId}/titles', [EmployeeTitleController::class, 'assign']);
    Route::delete('employees/{employeeId}/titles', [EmployeeTitleController::class, 'remove']);
});
