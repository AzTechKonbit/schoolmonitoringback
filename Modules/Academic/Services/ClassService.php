<?php

namespace Modules\Academic\Services;

use Modules\Academic\Models\{SchoolClass, Schedule, Attendance, Term};
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ClassService
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SchoolClass::with(['school', 'students', 'courses']);

        if (!empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?SchoolClass
    {
        return SchoolClass::with(['school', 'students.user', 'courses', 'schedules'])->find($id);
    }

    public function create(array $data): SchoolClass
    {
        return DB::transaction(function () use ($data) {
            $class = SchoolClass::create([
                'name' => $data['name'],
            ]);
            $class->school()->attach($data['school_id']);
            return $class;
        });
    }

    public function update(SchoolClass $class, array $data): SchoolClass
    {
        $class->update($data);
        return $class->fresh();
    }

    public function delete(SchoolClass $class): bool
    {
        return $class->delete();
    }

    public function addStudent(SchoolClass $class, int $studentId): void
    {
        $class->students()->syncWithoutDetaching([$studentId]);
    }

    public function removeStudent(SchoolClass $class, int $studentId): void
    {
        $class->students()->detach($studentId);
    }

    public function assignCourse(SchoolClass $class, int $courseId): void
    {
        $class->courses()->syncWithoutDetaching([$courseId]);
    }

    public function getStudentList(SchoolClass $class): \Illuminate\Database\Eloquent\Collection
    {
        return $class->students()->with('user')->get();
    }

    public function getSchedule(SchoolClass $class, ?string $dayOfWeek = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = $class->schedules()->with(['course', 'teacher.employee.user']);

        if ($dayOfWeek) {
            $query->where('day_of_week', $dayOfWeek);
        }

        return $query->orderBy('start_time')->get();
    }

    public function getAttendanceReport(SchoolClass $class, ?string $date = null): array
    {
        $query = Attendance::whereHas('schedule', fn($q) => $q->where('class_id', $class->id));

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $attendances = $query->get();

        $total = $attendances->count();
        $present = $attendances->where('status', 'present')->count();
        $absent = $attendances->where('status', 'absent')->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'attendance_rate' => $total > 0 ? round($present / $total * 100, 2) : 0,
        ];
    }
}
