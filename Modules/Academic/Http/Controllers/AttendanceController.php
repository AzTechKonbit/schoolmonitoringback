<?php

namespace Modules\Academic\Http\Controllers;

use App\Core\Http\Controllers\BaseApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academic\Services\AttendanceService;

class AttendanceController extends BaseApiController
{
    public function __construct(
        private AttendanceService $attendanceService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['student_id', 'schedule_id', 'status', 'date_from', 'date_to']);
        $attendances = $this->attendanceService->getAll($filters, $request->get('per_page', 15));
        return $this->paginate($attendances);
    }

    public function record(Request $request): JsonResponse
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'schedule_id' => 'required|exists:schedules,id',
            'status' => 'required|string|in:present,absent,late,excused',
            'recorded_at' => 'nullable|date',
        ]);

        $attendance = $this->attendanceService->recordAttendance($data);
        return $this->success($attendance, 'Attendance recorded successfully', 201);
    }

    public function bulkRecord(Request $request): JsonResponse
    {
        $data = $request->validate([
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.schedule_id' => 'required|exists:schedules,id',
            'attendances.*.status' => 'required|string|in:present,absent,late,excused',
            'attendances.*.recorded_at' => 'nullable|date',
        ]);

        $attendances = $this->attendanceService->bulkRecordAttendance($data['attendances']);
        return $this->success($attendances, 'Attendances recorded successfully', 201);
    }

    public function studentAttendance(Request $request, int $studentId): JsonResponse
    {
        $attendances = $this->attendanceService->getStudentAttendance(
            $studentId,
            $request->get('start_date'),
            $request->get('end_date')
        );

        return $this->success($attendances);
    }

    public function classReport(Request $request, int $classId): JsonResponse
    {
        $report = $this->attendanceService->getClassAttendanceReport($classId, $request->get('date'));
        return $this->success($report);
    }
}
