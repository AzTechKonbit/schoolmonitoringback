<?php

use App\Core\UserManagement\Http\Controllers\{EmployeeController, StudentController, UserController};
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);

    Route::apiResource('employees', EmployeeController::class);
    Route::post('employees/{id}/terminate', [EmployeeController::class, 'terminate']);
    Route::post('employees/{id}/courses', [EmployeeController::class, 'assignCourse']);
    Route::delete('employees/{id}/courses', [EmployeeController::class, 'removeCourse']);

    Route::apiResource('students', StudentController::class);
    Route::post('students/{id}/class', [StudentController::class, 'assignToClass']);
    Route::delete('students/{id}/class', [StudentController::class, 'removeFromClass']);
    Route::get('students/{id}/attendance-report', [StudentController::class, 'attendanceReport']);
    Route::get('students/{id}/grades-report', [StudentController::class, 'gradesReport']);
});
