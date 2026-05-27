<?php

namespace Modules\Academic\Http\Controllers;

use App\Core\Http\Controllers\BaseApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academic\Services\ClassService;

class ClassController extends BaseApiController
{
    public function __construct(
        private ClassService $classService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['school_id', 'search']);
        $classes = $this->classService->getAll($filters, $request->get('per_page', 15));
        return $this->paginate($classes);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:45',
            'school_id' => 'required|exists:schools,id',
        ]);

        $class = $this->classService->create($data);
        return $this->success($class, 'Class created successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $class = $this->classService->findById($id);

        if (!$class) {
            return $this->error('Class not found', 404);
        }

        return $this->success($class);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $class = $this->classService->findById($id);

        if (!$class) {
            return $this->error('Class not found', 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:45',
        ]);

        $class = $this->classService->update($class, $data);
        return $this->success($class, 'Class updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $class = $this->classService->findById($id);

        if (!$class) {
            return $this->error('Class not found', 404);
        }

        $this->classService->delete($class);
        return $this->success(null, 'Class deleted successfully');
    }

    public function students(int $id): JsonResponse
    {
        $class = $this->classService->findById($id);

        if (!$class) {
            return $this->error('Class not found', 404);
        }

        $students = $this->classService->getStudentList($class);
        return $this->success($students);
    }

    public function schedule(Request $request, int $id): JsonResponse
    {
        $class = $this->classService->findById($id);

        if (!$class) {
            return $this->error('Class not found', 404);
        }

        $schedule = $this->classService->getSchedule($class, $request->get('day_of_week'));
        return $this->success($schedule);
    }

    public function attendanceReport(Request $request, int $id): JsonResponse
    {
        $class = $this->classService->findById($id);

        if (!$class) {
            return $this->error('Class not found', 404);
        }

        $report = $this->classService->getAttendanceReport($class, $request->get('date'));
        return $this->success($report);
    }
}
