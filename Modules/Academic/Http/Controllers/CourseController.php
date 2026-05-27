<?php

namespace Modules\Academic\Http\Controllers;

use App\Core\Http\Controllers\BaseApiController;
use Modules\Academic\Services\{CourseService, ClassService, ScheduleService, AttendanceService};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CourseController extends BaseApiController
{
    public function __construct(
        private CourseService $courseService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['school_id', 'search']);
        $courses = $this->courseService->getAll($filters, $request->get('per_page', 15));
        return $this->paginate($courses);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:45',
            'description' => 'nullable|string',
            'code' => 'nullable|string|max:45',
            'credit' => 'nullable|numeric',
            'total_hours' => 'nullable|integer',
            'hours_td' => 'nullable|integer',
            'hours_tp' => 'nullable|integer',
            'school_id' => 'required|exists:schools,id',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'exists:teachers,id',
            'class_ids' => 'nullable|array',
            'class_ids.*' => 'exists:classes,id',
        ]);

        $course = $this->courseService->create($data);
        return $this->success($course, 'Course created successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $course = $this->courseService->findById($id);

        if (!$course) {
            return $this->error('Course not found', 404);
        }

        return $this->success($course);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $course = $this->courseService->findById($id);

        if (!$course) {
            return $this->error('Course not found', 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:45',
            'description' => 'nullable|string',
            'code' => 'nullable|string|max:45',
            'credit' => 'nullable|numeric',
            'total_hours' => 'nullable|integer',
            'hours_td' => 'nullable|integer',
            'hours_tp' => 'nullable|integer',
            'teacher_ids' => 'nullable|array',
            'class_ids' => 'nullable|array',
        ]);

        $course = $this->courseService->update($course, $data);
        return $this->success($course, 'Course updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $course = $this->courseService->findById($id);

        if (!$course) {
            return $this->error('Course not found', 404);
        }

        $this->courseService->delete($course);
        return $this->success(null, 'Course deleted successfully');
    }
}
