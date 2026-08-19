<?php

namespace App\Core\UserManagement\Http\Controllers;

use App\Core\Http\Controllers\BaseApiController;
use App\Core\UserManagement\Services\ParentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParentController extends BaseApiController
{
    public function __construct(
        private ParentService $parentService
    )
    {
    }

    /**
     * List all parents with optional search and filter.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search']);
        $parents = $this->parentService->getAll($filters);
        return $this->paginate($parents);
    }

    /**
     * Create or update a parent record.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'primary_first_name' => 'required|string|max:45',
            'primary_last_name'  => 'required|string|max:45',
            'primary_email'      => 'nullable|email',
            'primary_phone'      => 'nullable|string|max:45',
            'parent_id'          => 'nullable|exists:parents,id',
        ]);

        $created = $this->parentService->create($data);

        if ($created instanceof \Illuminate\Database\Eloquent\Model) {
            return $this->success($created, 'Parent created successfully', 201);
        }

        // Returns the parent model when updating an existing record
        return $this->success($created, 'Parent information updated successfully');
    }

    /**
     * Get a single parent by ID.
     */
    public function show(string $id): JsonResponse
    {
        $parent = $this->parentService->findById((int) $id);

        if (!$parent) {
            return $this->error('Parent not found', 404);
        }

        return $this->success($parent);
    }

    /**
     * Update a parent record. Accepts user_id to reassign the primary user.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $parent = $this->parentService->findById((int) $id);

        if (!$parent) {
            return $this->error('Parent not found', 404);
        }

        $data = $request->validate([
            'primary_first_name' => 'sometimes|string|max:45',
            'primary_last_name'  => 'sometimes|string|max:45',
            'primary_email'      => 'sometimes|email',
            'primary_phone'      => 'nullable|string|max:45',
        ]);

        $parent = $this->parentService->update($parent, $data);
        return $this->success($parent, 'Parent information updated successfully');
    }

    /**
     * Delete a parent record. Cleans up associated stale user records.
     */
    public function destroy(string $id): JsonResponse
    {
        $parent = $this->parentService->findById((int) $id);

        if (!$parent) {
            return $this->error('Parent not found', 404);
        }

        $this->parentService->delete($parent);
        return $this->success(null, 'Parent deleted successfully');
    }

    /**
     * Link a student to this parent by updating the student's parent_id.
     */
    public function assignStudent(Request $request, string $id): JsonResponse
    {
        $parent = $this->parentService->findById((int) $id);

        if (!$parent) {
            return $this->error('Parent not found', 404);
        }

        $data = $request->validate([
            'student_id' => 'required|exists:school_monitoring.students,id',
        ]);

        $this->parentService->assignStudent($parent, (int) $data['student_id']);
        return $this->success(null, 'Student linked to parent successfully');
    }

    /**
     * Get a report of all students belonging to this parent.
     */
    public function studentReport(string $id): JsonResponse
    {
        $parent = $this->parentService->findById((int) $id);

        if (!$parent) {
            return $this->error('Parent not found', 404);
        }

        $report = $this->parentService->getStudentsReport($parent);
        return $this->success($report, 'Student report generated');
    }
}
