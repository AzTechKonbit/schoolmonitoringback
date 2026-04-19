<?php

namespace App\Core\Services;

use App\Core\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'role' => $data['role'] ?? UserRole::STUDENT,
                'school_id' => $data['school_id'] ?? null,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }

    public function login(string $email, string $password): ?array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function refreshToken(User $user): string
    {
        $user->currentAccessToken()->delete();
        return $user->createToken('auth_token')->plainTextToken;
    }

    public function getProfile(User $user): array
    {
        $user->loadExists(['school', 'employee', 'student', 'parent']);

        return [
            'user' => $user,
            'profile_type' => $this->getProfileType($user),
        ];
    }

    private function getProfileType(User $user): ?string
    {
        if ($user->employee) {
            return $user->employee->isTeacher() ? 'teacher' : 'employee';
        }

        if ($user->student) {
            return 'student';
        }

        if ($user->parent) {
            return 'parent';
        }

        return null;
    }
}
