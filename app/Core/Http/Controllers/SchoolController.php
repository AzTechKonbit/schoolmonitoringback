<?php

namespace App\Core\Http\Controllers;

use App\Core\Services\SchoolService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolController extends BaseApiController
{
    public function __construct( private SchoolService $schoolService)
    {}

    public function index(Request $request): JsonResponse
    {
        $schools = $this->schoolService->getAll();
        return $this->success($schools);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:45',
            'type' => 'nullable|string|max:45',
            'logo' => 'nullable|string|max:45',
            'default_language' => 'nullable|string|max:45',
            'academic_year' => 'nullable|string|max:45',
            'status' => 'nullable|string|max:45',
        ]);

        $school = $this->schoolService->create($data);
        return $this->success($school, 'School created successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $school = $this->schoolService->findById($id);

        if (!$school) {
            return $this->error('School not found', 404);
        }

        return $this->success($school);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $school = $this->schoolService->findById($id);

        if (!$school) {
            return $this->error('School not found', 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:45',
            'type' => 'nullable|string|max:45',
            'logo' => 'nullable|string|max:45',
            'default_language' => 'nullable|string|max:45',
            'academic_year' => 'nullable|string|max:45',
            'status' => 'nullable|string|max:45',
        ]);

        $school = $this->schoolService->update($school, $data);
        return $this->success($school, 'School updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $school = $this->schoolService->findById($id);

        if (!$school) {
            return $this->error('School not found', 404);
        }

        $this->schoolService->delete($school);
        return $this->success(null, 'School deleted successfully');
    }

    public function statistics(int $id): JsonResponse
    {
        $school = $this->schoolService->findById($id);

        if (!$school) {
            return $this->error('School not found', 404);
        }

        $stats = $this->schoolService->getStatistics($school);
        return $this->success($stats);
    }
}
