<?php

namespace App\Core\UserManagement\Http\Controllers;

use App\Core\Http\Controllers\BaseApiController;
use App\Core\UserManagement\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends BaseApiController
{
    public function __construct(
        private EmployeeService $employeeService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['school_id', 'employee_type_id', 'employment_status', 'is_teacher']);
        $employees = $this->employeeService->getAll($filters, $request->get('per_page', 15));
        return $this->paginate($employees);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:45',
            'gender' => 'nullable|string|max:45',
            'address' => 'nullable|string|max:45',
            'employee_type_id' => 'required|exists:employee_types,id',
            'school_id' => 'required|exists:schools,id',
            'employment_status' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'national_id' => 'nullable|string|max:45',
            'emergency_contact_name' => 'nullable|string|max:45',
            'emergency_contact_phone' => 'nullable|string|max:45',
            'contract_type' => 'nullable|string',
            'salary_type' => 'nullable|string',
            'base_salary' => 'nullable|numeric',
            'is_teacher' => 'nullable|boolean',
        ]);

        $employee = $this->employeeService->create($data);
        return $this->success($employee, 'Employee created successfully', 201);
    }

    public function show(string $id): JsonResponse
    {
        $employee = $this->employeeService->findById($id);

        if (!$employee) {
            return $this->error('Employee not found', 404);
        }

        return $this->success($employee);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $employee = $this->employeeService->findById($id);

        if (!$employee) {
            return $this->error('Employee not found', 404);
        }

        $data = $request->validate([
            'first_name' => 'sometimes|string|max:45',
            'last_name' => 'sometimes|string|max:45',
            'email' => 'sometimes|email|unique:users,email,' . $employee->user_id,
            'phone' => 'nullable|string|max:45',
            'gender' => 'nullable|string|max:45',
            'address' => 'nullable|string|max:45',
            'employment_status' => 'nullable|string',
            'national_id' => 'nullable|string|max:45',
            'emergency_contact_name' => 'nullable|string|max:45',
            'emergency_contact_phone' => 'nullable|string|max:45',
            'contract_type' => 'nullable|string',
            'salary_type' => 'nullable|string',
            'base_salary' => 'nullable|numeric',
        ]);

        $employee = $this->employeeService->update($employee, $data);
        return $this->success($employee, 'Employee updated successfully');
    }

    public function destroy(string $id): JsonResponse
    {
        $employee = $this->employeeService->findById($id);

        if (!$employee) {
            return $this->error('Employee not found', 404);
        }

        $this->employeeService->delete($employee);
        return $this->success(null, 'Employee deleted successfully');
    }

    public function terminate(string $id): JsonResponse
    {
        $employee = $this->employeeService->findById($id);

        if (!$employee) {
            return $this->error('Employee not found', 404);
        }

        $employee = $this->employeeService->terminate($employee);
        return $this->success($employee, 'Employee terminated successfully');
    }

    public function assignCourse(Request $request, string $id): JsonResponse
    {
        $employee = $this->employeeService->findById($id);

        if (!$employee) {
            return $this->error('Employee not found', 404);
        }

        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $this->employeeService->assignCourse($employee, $data['course_id']);
        return $this->success($employee->fresh('courses'), 'Course assigned successfully');
    }

    public function removeCourse(Request $request, string $id): JsonResponse
    {
        $employee = $this->employeeService->findById($id);

        if (!$employee) {
            return $this->error('Employee not found', 404);
        }

        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $this->employeeService->removeCourse($employee, $data['course_id']);
        return $this->success($employee->fresh('courses'), 'Course removed successfully');
    }
}
