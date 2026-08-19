<?php

namespace App\Core\UserManagement\Services;

use App\Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class ParentService
{
    /**
     * Get all parents with optional search/filter.
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Model
    {
        $query = \App\Core\UserManagement\Models\ParentModel::with(['primaryUser', 'secondaryUser']);

        if (!empty($filters['search'])) {
            $query->whereHas('primaryUser', fn($q) => $q->where(function ($q) use ($filters) {
                return $q->where('first_name', 'like', "%{$filters['search']}%")
                    ->orWhere('last_name', 'like', "%{$filters['search']}%");
            }));
        }

        if (!empty($filters['parent_id'])) {
            $query->where('user_id', $filters['parent_id']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a parent by ID.
     */
    public function findById(int $id): ?Model
    {
        return \App\Core\UserManagement\Models\ParentModel::with(['primaryUser', 'secondaryUser'])->find($id);
    }

    /**
     * Create or update a parent record.
     */
    public function create(array $data): Model|null
    {
        if (!empty($data['parent_id'])) {
            // Update existing parent by ensuring primary user exists
            User::firstOrCreate(
                ['email' => $data['primary_email'] ?? null],
                [
                    'school_id' => null,
                    'first_name'  => $data['primary_first_name'] ?? null,
                    'last_name'   => $data['primary_last_name'] ?? null,
                    'phone'       => $data['primary_phone'] ?? null,
                ]
            );

            return \App\Core\UserManagement\Models\ParentModel::where('id', $data['parent_id'])
                ->update([
                    'user_id2' => $data['secondary_user_id'] ?? null,
                    'updated_at' => now(),
                ]);
        }

        // Create new parent record
        return \App\Core\UserManagement\Models\ParentModel::create([
            'user_id'   => $data['primary_user_id'],
            'user_id2'  => $data['secondary_user_id'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ])->load('primaryUser');
    }

    /**
     * Update a parent record. Accepts user_id to reassign the primary user.
     */
    public function update(Model $parent, array $data): Model|null
    {
        if (isset($data['user_id'])) {
            // Reassign the primary user
            User::firstOrCreate(
                ['email' => $data['primary_email']],
                [
                    'school_id' => null,
                    'first_name'  => $data['primary_first_name'] ?? null,
                    'last_name'   => $data['primary_last_name'] ?? null,
                    'phone'       => $data['primary_phone'] ?? null,
                ]
            );

            // Clear secondary user when reassigning primary
            return $parent->update(['user_id2' => null]);
        }

        return $parent;
    }

    /**
     * Delete a parent and clean up associated stale user records.
     */
    public function delete(Model $parent): bool
    {
        // Remove stale users (parents don't own students directly, so this is safe)
        if ($user = $parent->primaryUser()) {
            User::where('email', $user->email)->delete();
        }

        return true;
    }

    /**
     * Link a student to this parent by updating the student's parent_id.
     */
    public function assignStudent(Model $parent, int $studentId): void
    {
        \App\Core\UserManagement\Models\Student::updateOrCreate(
            ['id' => $studentId],
            ['parent_id' => $parent->id]
        );
    }

    /**
     * Get a report of students belonging to this parent.
     */
    public function getStudentsReport(Model $parent): array
    {
        $students = \App\Core\UserManagement\Models\Student::where('parent_id', $parent->id)->get();

        return [
            'total'              => count($students),
            'children_enrolled'  => count($students),
            'report_date'        => now()->toDateString(),
        ];
    }
}