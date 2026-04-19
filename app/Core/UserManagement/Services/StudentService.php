<?php

namespace App\Core\UserManagement\Services;

use App\Core\Models\User;
use App\Core\UserManagement\Models\{ParentModel, Student};
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StudentService
{
    /**
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15)
    {
        $query = Student::with(['user', 'parent', 'school', 'classes']);

        if (!empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (!empty($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        if (!empty($filters['class_id'])) {
            $query->whereHas('classes', fn($q) => $q->where('class_id', $filters['class_id']));
        }

        if (!empty($filters['search'])) {
            $query->whereHas('user', fn($q) => $q->where('first_name', 'like', "%{$filters['search']}%")
                ->orWhere('last_name', 'like', "%{$filters['search']}%"));
        }

        return $query->paginate($perPage);
    }

    /**
     * @param int $id
     * @return Student|null
     */
    public function findById(int $id): ?Student
    {
        return Student::with(['user', 'parent', 'school', 'classes', 'programs', 'attendances', 'submissions'])->find($id);
    }

    /**
     * @param array $data
     * @return Student
     */
    public function create(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            $parentId = $data['parent_id'] ?? $this->createParent($data);

            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'] ?? null,
                'password' => bcrypt($data['password'] ?? 'student123'),
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'role' => 'student',
                'school_id' => $data['school_id'],
            ]);

            $student = Student::create([
                'dob' => $data['dob'],
                'user_id' => $user->id,
                'parent_id' => $parentId,
                'student_number_id' => $this->generateStudentNumber(),
                'school_id' => $data['school_id'],
            ]);

            if (!empty($data['class_id'])) {
                $student->classes()->attach($data['class_id']);
            }

            return $student->load('user', 'parent', 'classes');
        });
    }

    private function createParent(array $data): int
    {
        $parentUser = User::create([
            'first_name' => $data['parent_first_name'] ?? 'Parent',
            'last_name' => $data['parent_last_name'] ?? $data['last_name'],
            'email' => $data['parent_email'] ?? null,
            'password' => bcrypt($data['password'] ?? 'parent123'),
            'phone' => $data['parent_phone'] ?? $data['phone'],
            'role' => 'parent',
            'school_id' => $data['school_id'],
        ]);

        return ParentModel::create(['user_id' => $parentUser->id])->id;
    }

    private function generateStudentNumber(): string
    {
        return 'STU-' . date('Y') . '-' . str_pad(Student::max('id') + 1, 6, '0', STR_PAD_LEFT);
    }

    /**
     * @param Student $student
     * @param array $data
     * @return Student
     */
    public function update(Student $student, array $data): Student
    {
        $student->update(collect($data)->only(['dob'])->toArray());

        $student->user->update(collect($data)->except(['dob', 'password'])->toArray());

        return $student->fresh(['user', 'classes', 'programs']);
    }

    public function delete(Student $student): bool
    {
        return DB::transaction(function () use ($student) {
            $student->user->delete();
            return $student->delete();
        });
    }

    public function assignToClass(Student $student, int $classId): void
    {
        $student->classes()->syncWithoutDetaching([$classId]);
    }

    public function removeFromClass(Student $student, int $classId): void
    {
        $student->classes()->detach($classId);
    }

    public function getAttendanceReport(Student $student, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = $student->attendances();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $attendances = $query->get();

        $total = $attendances->count();
        $present = $attendances->where('status', 'present')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $late = $attendances->where('status', 'late')->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'attendance_rate' => $total > 0 ? round(($present + $late) / $total * 100, 2) : 0,
        ];
    }

    public function getGradesReport(Student $student): array
    {
        $submissions = $student->submissions()->with('assignment.course')->get();

        return $submissions->map(function ($submission) {
            return [
                'assignment' => $submission->assignment->title,
                'course' => $submission->assignment->course->name,
                'grade' => $submission->grade,
                'submitted_at' => $submission->submitted_at,
            ];
        })->toArray();
    }
}
