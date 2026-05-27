<?php

use Illuminate\Support\Facades\Route;
use Modules\Academic\Http\Controllers\{AttendanceController, ClassController, CourseController, ScheduleController};


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('courses', CourseController::class);

    Route::apiResource('classes', ClassController::class);
    Route::get('classes/{id}/students', [ClassController::class, 'students']);
    Route::get('classes/{id}/schedule', [ClassController::class, 'schedule']);
    Route::get('classes/{id}/attendance-report', [ClassController::class, 'attendanceReport']);

    Route::apiResource('schedules', ScheduleController::class);

    Route::get('attendances', [AttendanceController::class, 'index']);
    Route::post('attendances', [AttendanceController::class, 'record']);
    Route::post('attendances/bulk', [AttendanceController::class, 'bulkRecord']);
    Route::get('attendances/student/{studentId}', [AttendanceController::class, 'studentAttendance']);
    Route::get('attendances/class/{classId}/report', [AttendanceController::class, 'classReport']);
});
