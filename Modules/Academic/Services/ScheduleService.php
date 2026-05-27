<?php

namespace Modules\Academic\Services;

use Modules\Academic\Models\{Schedule, Attendance};
use Modules\UserManagement\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ScheduleService
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Schedule::with(['course', 'schoolClass', 'teacher.employee.user']);

        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        if (!empty($filters['day_of_week'])) {
            $query->where('day_of_week', $filters['day_of_week']);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Schedule
    {
        return Schedule::with(['course', 'schoolClass', 'teacher.employee.user', 'attendances'])->find($id);
    }

    public function create(array $data): Schedule
    {
        return Schedule::create($data);
    }

    public function update(Schedule $schedule, array $data): Schedule
    {
        $schedule->update($data);
        return $schedule->fresh();
    }

    public function delete(Schedule $schedule): bool
    {
        return $schedule->delete();
    }

    public function getStudentSchedule(int $studentId, ?string $dayOfWeek = null): \Illuminate\Database\Eloquent\Collection
    {
        $student = Student::with('classes')->findOrFail($studentId);

        $query = Schedule::whereHas('schoolClass', fn($q) => $q->whereIn('classes.id', $student->classes->pluck('id')))
            ->with(['course', 'schoolClass', 'teacher.employee.user']);

        if ($dayOfWeek) {
            $query->where('day_of_week', $dayOfWeek);
        }

        return $query->orderBy('start_time')->get();
    }

    public function getTeacherSchedule(int $teacherId, ?string $dayOfWeek = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = Schedule::where('teacher_id', $teacherId)
            ->with(['course', 'schoolClass']);

        if ($dayOfWeek) {
            $query->where('day_of_week', $dayOfWeek);
        }

        return $query->orderBy('start_time')->get();
    }
}
