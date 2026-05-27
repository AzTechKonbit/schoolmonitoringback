<?php

namespace Modules\Academic\Services;

use Modules\Academic\Models\Attendance;
use Modules\Academic\Models\Schedule;
use Modules\UserManagement\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class AttendanceService
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Attendance::with(['student.user', 'schedule.course', 'recorder']);

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['schedule_id'])) {
            $query->where('schedule_id', $filters['schedule_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Attendance
    {
        return Attendance::with(['student.user', 'schedule.course', 'recorder'])->find($id);
    }

    public function recordAttendance(array $data): Attendance
    {
        return DB::transaction(function () use ($data) {
            return Attendance::updateOrCreate(
                [
                    'student_id' => $data['student_id'],
                    'schedule_id' => $data['schedule_id'],
                    'recorded_at' => $data['recorded_at'] ?? now()->toDateString(),
                ],
                [
                    'status' => $data['status'],
                    'recorded_by' => auth()->id(),
                ]
            );
        });
    }

    public function bulkRecordAttendance(array $attendances): array
    {
        return DB::transaction(function () use ($attendances) {
            $recorded = [];

            foreach ($attendances as $attendance) {
                $recorded[] = Attendance::updateOrCreate(
                    [
                        'student_id' => $attendance['student_id'],
                        'schedule_id' => $attendance['schedule_id'],
                        'recorded_at' => $attendance['recorded_at'] ?? now()->toDateString(),
                    ],
                    [
                        'status' => $attendance['status'],
                        'recorded_by' => auth()->id(),
                    ]
                );
            }

            return $recorded;
        });
    }

    public function getStudentAttendance(int $studentId, ?string $startDate = null, ?string $endDate = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = Attendance::where('student_id', $studentId)
            ->with(['schedule.course', 'schedule.schoolClass']);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getClassAttendanceReport(int $classId, ?string $date = null): array
    {
        $scheduleIds = Schedule::where('class_id', $classId)->pluck('id');

        $query = Attendance::whereIn('schedule_id', $scheduleIds);

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $attendances = $query->get();
        $total = $attendances->count();

        return [
            'total' => $total,
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'excused' => $attendances->where('status', 'excused')->count(),
            'attendance_rate' => $total > 0 ? round($attendances->whereIn('status', ['present', 'late'])->count() / $total * 100, 2) : 0,
        ];
    }
}
