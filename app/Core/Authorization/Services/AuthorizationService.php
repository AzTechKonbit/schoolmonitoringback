<?php

namespace App\Core\Authorization\Services;

use App\Core\Models\User;
use Illuminate\Database\Eloquent\Collection;
use App\Core\Authorization\Models\{Right, Title};
use App\Core\UserManagement\Models\{Employee, EmployeeType};
use Illuminate\Pagination\LengthAwarePaginator;

class AuthorizationService
{
    public function getAllRights(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Right::query();

        if (!empty($filters['search'])) {
            $query->where('code', 'like', "%{$filters['search']}%")
                ->orWhere('description', 'like', "%{$filters['search']}%");
        }

        return $query->paginate($perPage);
    }

    public function createRight(array $data): Right
    {
        return Right::create($data);
    }

    public function updateRight(Right $right, array $data): Right
    {
        $right->update($data);
        return $right->fresh();
    }

    public function deleteRight(Right $right): bool
    {
        return $right->delete();
    }

    public function getAllTitles(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Title::with('employees');

        if (!empty($filters['search'])) {
            $query->where('title', 'like', "%{$filters['search']}%");
        }

        return $query->paginate($perPage);
    }

    public function createTitle(array $data): Title
    {
        return Title::create($data);
    }

    public function updateTitle(Title $title, array $data): Title
    {
        $title->update($data);
        return $title->fresh();
    }

    public function deleteTitle(Title $title): bool
    {
        return $title->delete();
    }

    public function assignRightToUser(User $user, int $rightId): void
    {
        $user->rights()->syncWithoutDetaching([$rightId]);
    }

    public function removeRightFromUser(User $user, int $rightId): void
    {
        $user->rights()->detach($rightId);
    }

    public function assignRightToGroup(EmployeeType $type, int $rightId): void
    {
        $type->groupRights()->syncWithoutDetaching([$rightId]);
    }

    public function removeRightFromGroup(EmployeeType $type, int $rightId): void
    {
        $type->groupRights()->detach($rightId);
    }

    public function assignTitleToEmployee(Employee $employee, int $titleId): void
    {
        $employee->titles()->syncWithoutDetaching([$titleId]);
    }

    public function removeTitleFromEmployee(Employee $employee, int $titleId): void
    {
        $employee->titles()->detach($titleId);
    }

    public function syncUserRights(User $user, array $rightIds): void
    {
        $user->rights()->sync($rightIds);
    }

    public function getUserRights(User $user): Collection
    {
        return $user->rights;
    }

    public function hasRight(User $user, string $rightCode): bool
    {
        return $user->rights()->where('code', $rightCode)->exists();
    }
}
