<?php

namespace App\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Core\Services\AuthService;

class AuthController extends BaseApiController
{
    public function __construct(
        private AuthService $authService
    )
    {
    }

    public function register(Request $request): JsonResponse
    {
        $result = $this->authService->register($request->validate([
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:45',
            'gender' => 'nullable|string|max:45',
            'role' => 'nullable|string',
            'school_id' => 'nullable|exists:schools,id',
        ]));

        return $this->success($result, 'User registered successfully', 201);
    }

    public function login(Request $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->email,
            $request->password
        );

        if (!$result) {
            return $this->error('Invalid credentials', 401);
        }

        return $this->success($result, 'Login successful');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());
        return $this->success(null, 'Logged out successfully');
    }

    public function profile(Request $request): JsonResponse
    {
        $result = $this->authService->getProfile($request->user());
        return $this->success($result);
    }

    public function refresh(Request $request): JsonResponse
    {
        $token = $this->authService->refreshToken($request->user());
        return $this->success(['token' => $token], 'Token refreshed');
    }
}
