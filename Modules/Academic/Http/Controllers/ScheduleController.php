<?php

namespace Modules\Academic\Http\Controllers;

use App\Core\Http\Controllers\BaseApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academic\Services\ScheduleService;

class ScheduleController extends BaseApiController
{
    public function __construct(
        private ScheduleService $scheduleService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['course_id', 'class_id', 'teacher_id', 'day_of_week']);
        $schedules = $this->scheduleService->getAll($filters, $request->get('per_page', 15));
        return $this->paginate($schedules);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'day_of_week' => 'required|string|max:45',
            'room' => 'required|string|max:45',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
        ]);

        $schedule = $this->scheduleService->create($data);
        return $this->success($schedule, 'Schedule created successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $schedule = $this->scheduleService->findById($id);

        if (!$schedule) {
            return $this->error('Schedule not found', 404);
        }

        return $this->success($schedule);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $schedule = $this->scheduleService->findById($id);

        if (!$schedule) {
            return $this->error('Schedule not found', 404);
        }

        $data = $request->validate([
            'course_id' => 'sometimes|exists:courses,id',
            'class_id' => 'sometimes|exists:classes,id',
            'teacher_id' => 'sometimes|exists:teachers,id',
            'day_of_week' => 'sometimes|string|max:45',
            'room' => 'sometimes|string|max:45',
            'start_time' => 'sometimes|string',
            'end_time' => 'sometimes|string',
        ]);

        $schedule = $this->scheduleService->update($schedule, $data);
        return $this->success($schedule, 'Schedule updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $schedule = $this->scheduleService->findById($id);

        if (!$schedule) {
            return $this->error('Schedule not found', 404);
        }

        $this->scheduleService->delete($schedule);
        return $this->success(null, 'Schedule deleted successfully');
    }
}
