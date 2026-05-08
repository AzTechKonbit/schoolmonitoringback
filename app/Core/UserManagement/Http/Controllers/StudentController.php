<?php

namespace App\Core\UserManagement\Http\Controllers;

use App\Core\Http\Controllers\BaseApiController;
use App\Core\UserManagement\Services\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends BaseApiController
{
    public function __construct(
        private StudentService $studentService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['school_id', 'parent_id', 'class_id', 'search']);
        $students = $this->studentService->getAll($filters, $request->get('per_page', 15));
        return $this->paginate($students);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:45',
            'gender' => 'required|string|max:45|IN:F,M',
            'school_id' => 'required|exists:schools,id',
            'dob' => 'required|date',
            'parent_id' => 'nullable|exists:parents,id',
            'class_id' => 'nullable|exists:classes,id',
            'parent_first_name' => 'nullable|string|max:45',
            'parent_last_name' => 'nullable|string|max:45',
            'parent_email' => 'nullable|email',
            'parent_phone' => 'nullable|string|max:45',
        ]);

        $student = $this->studentService->create($data);
        return $this->success($student, 'Student created successfully', 201);
    }

    public function show(string $id): JsonResponse
    {
        $student = $this->studentService->findById($id);

        if (!$student) {
            return $this->error('Student not found', 404);
        }

        return $this->success($student);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $student = $this->studentService->findById($id);

        if (!$student) {
            return $this->error('Student not found', 404);
        }

        $data = $request->validate([
            'first_name' => 'sometimes|string|max:45',
            'last_name' => 'sometimes|string|max:45',
            'email' => 'sometimes|email|unique:users,email,' . $student->user_id,
            'phone' => 'nullable|string|max:45',
            'gender' => 'nullable|string|max:45',
            'dob' => 'sometimes|date',
        ]);

        $student = $this->studentService->update($student, $data);
        return $this->success($student, 'Student updated successfully');
    }

    public function destroy(string $id): JsonResponse
    {
        $student = $this->studentService->findById($id);

        if (!$student) {
            return $this->error('Student not found', 404);
        }

        $this->studentService->delete($student);
        return $this->success(null, 'Student deleted successfully');
    }

    public function assignToClass(Request $request, string $id): JsonResponse
    {
        $student = $this->studentService->findById($id);

        if (!$student) {
            return $this->error('Student not found', 404);
        }

        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        $this->studentService->assignToClass($student, $data['class_id']);
        return $this->success($student->fresh('classes'), 'Student assigned to class successfully');
    }

    public function removeFromClass(Request $request, string $id): JsonResponse
    {
        $student = $this->studentService->findById($id);

        if (!$student) {
            return $this->error('Student not found', 404);
        }

        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        $this->studentService->removeFromClass($student, $data['class_id']);
        return $this->success($student->fresh('classes'), 'Student removed from class successfully');
    }

    public function attendanceReport(Request $request, string $id): JsonResponse
    {
        $student = $this->studentService->findById($id);

        if (!$student) {
            return $this->error('Student not found', 404);
        }

        $report = $this->studentService->getAttendanceReport(
            $student,
            $request->get('start_date'),
            $request->get('end_date')
        );

        return $this->success($report);
    }

    public function gradesReport(string $id): JsonResponse
    {
        $student = $this->studentService->findById($id);

        if (!$student) {
            return $this->error('Student not found', 404);
        }

        $report = $this->studentService->getGradesReport($student);
        return $this->success($report);
    }
}
