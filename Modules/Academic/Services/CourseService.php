<?php

namespace Modules\Academic\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Academic\Models\{Course};
use Modules\UserManagement\Models\Teacher;

class CourseService
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Course::with(['teachers', 'classes']);

        if (!empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%")
                ->orWhere('code', 'like', "%{$filters['search']}%");
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Course
    {
        return Course::with(['teachers.employee.user', 'classes', 'schedules', 'assignments'])->find($id);
    }

    public function create(array $data): Course
    {
        return DB::transaction(function () use ($data) {
            $course = Course::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'code' => $data['code'] ?? $this->generateCourseCode(),
                'credit' => $data['credit'] ?? null,
                'total_hours' => $data['total_hours'] ?? null,
                'hours_td' => $data['hours_td'] ?? null,
                'hours_tp' => $data['hours_tp'] ?? null,
            ]);

            if (!empty($data['teacher_ids'])) {
                $course->teachers()->attach($data['teacher_ids']);
            }

            if (!empty($data['class_ids'])) {
                $course->classes()->attach($data['class_ids']);
            }

            return $course;
        });
    }

    public function update(Course $course, array $data): Course
    {
        $course->update(collect($data)->except(['teacher_ids', 'class_ids'])->toArray());

        if (isset($data['teacher_ids'])) {
            $course->teachers()->sync($data['teacher_ids']);
        }

        if (isset($data['class_ids'])) {
            $course->classes()->sync($data['class_ids']);
        }

        return $course->fresh(['teachers', 'classes']);
    }

    public function delete(Course $course): bool
    {
        return $course->delete();
    }

    public function assignTeacher(Course $course, int $teacherId): void
    {
        $course->teachers()->syncWithoutDetaching([$teacherId]);
    }

    public function removeTeacher(Course $course, int $teacherId): void
    {
        $course->teachers()->detach($teacherId);
    }

    private function generateCourseCode(): string
    {
        return 'CRS-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }
}
