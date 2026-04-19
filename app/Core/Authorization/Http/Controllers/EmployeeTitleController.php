<?php

namespace App\Core\Authorization\Http\Controllers;

use App\Core\Authorization\Services\AuthorizationService;
use App\Core\Http\Controllers\BaseApiController;
use App\Core\UserManagement\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeTitleController extends BaseApiController
{
    public function __construct(
        private AuthorizationService $authorizationService
    )
    {
    }

    public function assign(Request $request, int $employeeId): JsonResponse
    {
        $employee = Employee::find($employeeId);

        if (!$employee) {
            return $this->error('Employee not found', 404);
        }

        $data = $request->validate([
            'title_id' => 'required|exists:titles,id',
        ]);

        $this->authorizationService->assignTitleToEmployee($employee, $data['title_id']);
        return $this->success($employee->fresh('titles'), 'Title assigned successfully');
    }

    public function remove(Request $request, int $employeeId): JsonResponse
    {
        $employee = Employee::find($employeeId);

        if (!$employee) {
            return $this->error('Employee not found', 404);
        }

        $data = $request->validate([
            'title_id' => 'required|exists:titles,id',
        ]);

        $this->authorizationService->removeTitleFromEmployee($employee, $data['title_id']);
        return $this->success($employee->fresh('titles'), 'Title removed successfully');
    }
}
