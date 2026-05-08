<?php

namespace App\Core\UserManagement\Services;

use App\Core\Models\User;
use App\Enums\UserRole;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query();

        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (!empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('first_name', 'like', "%{$filters['search']}%")
                    ->orWhere('last_name', 'like', "%{$filters['search']}%")
                    ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?User
    {
        return User::with(['employee', 'student', 'parent', 'school'])->find($id);
    }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'role' => $data['role'] ?? UserRole::EMPLOYEE,
                'address' => $data['address'] ?? null,
                'status' => $data['status'] ?? 'active',
            ]);

            return $user;
        });
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function attachRole(User $user, string $role): User
    {
        $user->update(['role' => $role]);
        return $user;
    }

    public function update(User $user, array $data): User
    {
        $updateData = collect($data)->except(['password'])->toArray();

        if (isset($data['password'])) {
            $updateData['password'] = bcrypt($data['password']);
        }

        $user->update($updateData);

        return $user->fresh();
    }
}
