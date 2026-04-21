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

            $token = $user->createToken('api-token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }

    public function login(string $email, string $password): ?array
    {
        $user = User::where('email', $email)->with('profile')->first();


        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        $token = $user->createToken('api-token')->plainTextToken;

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
        $this->logout($user);
        return $user->createToken('api-token')->plainTextToken;
    }

    public function getProfile(User $user): array
    {
        $user->load(['school', 'profile']);
        return [
            'user' => $user,
        ];
    }

}
