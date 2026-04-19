<?php

namespace App\Core\UserManagement\Services;

use App\Core\Models\User;
use App\Core\UserManagement\Models\{Employee, Teacher};
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Employee::with(['user', 'employeeType', 'school']);

        if (!empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (!empty($filters['employee_type_id'])) {
            $query->where('employee_type_id', $filters['employee_type_id']);
        }

        if (!empty($filters['employment_status'])) {
            $query->where('employment_status', $filters['employment_status']);
        }

        if (!empty($filters['is_teacher'])) {
            $query->whereHas('teacher');
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Employee
    {
        return Employee::with(['user', 'employeeType', 'school', 'teacher', 'titles', 'courses'])->find($id);
    }

    public function create(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password'] ?? 'password123'),
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'role' => 'employee',
                'address' => $data['address'] ?? null,
                'school_id' => $data['school_id'],
            ]);

            $employee = Employee::create([
                'employee_code' => $this->generateEmployeeCode(),
                'employment_status' => $data['employment_status'] ?? 'active',
                'hire_date' => $data['hire_date'] ?? now(),
                'national_id' => $data['national_id'] ?? null,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'employment_contract_type' => $data['contract_type'] ?? 'full-time',
                'salary_type' => $data['salary_type'] ?? 'monthly',
                'base_salary' => $data['base_salary'] ?? 0,
                'employee_type_id' => $data['employee_type_id'],
                'user_id' => $user->id,
                'school_id' => $data['school_id'],
                'created_by' => auth()->id(),
            ]);

            if (!empty($data['is_teacher'])) {
                Teacher::create(['employee_id' => $employee->id]);
            }

            return $employee->load('user', 'employeeType', 'teacher');
        });
    }

    private function generateEmployeeCode(): string
    {
        return 'EMP-' . str_pad(Employee::max('id') + 1, 6, '0', STR_PAD_LEFT);
    }

    public function delete(Employee $employee): bool
    {
        return DB::transaction(function () use ($employee) {
            $employee->user->delete();
            return $employee->delete();
        });
    }

    public function terminate(Employee $employee): Employee
    {
        $employee->update([
            'employment_status' => 'terminated',
            'termination_date' => now(),
        ]);

        return $employee;
    }

    public function update(Employee $employee, array $data): Employee
    {
        $employee->update(collect($data)->except(['first_name', 'last_name', 'email'])->toArray());

        $employee->user->update([
            'first_name' => $data['first_name'] ?? $employee->user->first_name,
            'last_name' => $data['last_name'] ?? $employee->user->last_name,
            'email' => $data['email'] ?? $employee->user->email,
        ]);

        return $employee->fresh(['user', 'employeeType', 'teacher', 'titles']);
    }

    public function assignCourse(Employee $employee, int $courseId): void
    {
        $employee->courses()->syncWithoutDetaching([$courseId]);
    }

    public function removeCourse(Employee $employee, int $courseId): void
    {
        $employee->courses()->detach($courseId);
    }
}
