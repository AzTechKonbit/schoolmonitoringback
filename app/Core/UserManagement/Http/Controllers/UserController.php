<?php

namespace App\Core\UserManagement\Http\Controllers;

use App\Core\Http\Controllers\BaseApiController;
use App\Core\UserManagement\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends BaseApiController
{
    public function __construct(
        private UserService $userService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['role', 'school_id', 'search']);
        $users = $this->userService->getAll($filters, $request->get('per_page', 15));
        return $this->paginate($users);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:45',
            'gender' => 'nullable|string|max:45',
            'role' => 'nullable|string',
            'address' => 'nullable|string|max:45',
            'status' => 'nullable|string',
        ]);

        $user = $this->userService->create($data);
        return $this->success($user, 'User created successfully', 201);
    }

    public function show(string $id): JsonResponse
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        return $this->success($user);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        $data = $request->validate([
            'first_name' => 'sometimes|string|max:45',
            'last_name' => 'sometimes|string|max:45',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:45',
            'gender' => 'nullable|string|max:45',
            'address' => 'nullable|string|max:255',
            'status' => 'nullable|string',
        ]);

        $user = $this->userService->update($user, $data);
        return $this->success($user, 'User updated successfully');
    }

    public function destroy(string $id): JsonResponse
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        $this->userService->delete($user);
        return $this->success(null, 'User deleted successfully');
    }
}
