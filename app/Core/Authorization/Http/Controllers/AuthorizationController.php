<?php

namespace App\Core\Authorization\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Core\Authorization\Models\Right;
use App\Core\Authorization\Models\Title;
use App\Core\Authorization\Services\AuthorizationService;
use App\Core\Http\Controllers\BaseApiController;
use App\Core\Models\User;
use App\Core\UserManagement\Models\Employee;

class AuthorizationController extends BaseApiController
{
    public function __construct(
        private AuthorizationService $authorizationService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search']);
        $rights = $this->authorizationService->getAllRights($filters, $request->get('per_page', 15));
        return $this->paginate($rights);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => 'required|string|max:45|unique:rights,code',
            'description' => 'nullable|string|max:45',
            'status' => 'nullable|string|max:45',
        ]);

        $right = $this->authorizationService->createRight($data);
        return $this->success($right, 'Right created successfully', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $right = Right::find($id);

        if (!$right) {
            return $this->error('Right not found', 404);
        }

        $data = $request->validate([
            'code' => 'sometimes|string|max:45|unique:rights,code,' . $id,
            'description' => 'nullable|string|max:45',
            'status' => 'nullable|string|max:45',
        ]);

        $right = $this->authorizationService->updateRight($right, $data);
        return $this->success($right, 'Right updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $right = Right::find($id);

        if (!$right) {
            return $this->error('Right not found', 404);
        }

        $this->authorizationService->deleteRight($right);
        return $this->success(null, 'Right deleted successfully');
    }
}
