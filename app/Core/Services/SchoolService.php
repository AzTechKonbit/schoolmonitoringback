<?php

namespace App\Core\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Core\Models\School;

class SchoolService
{
    public function getAll(): Collection
    {
        return School::all();
    }

    public function findById(int $id): ?School
    {
        return School::find($id);
    }

    public function create(array $data): School
    {
        return DB::transaction(function () use ($data) {
            return School::create($data);
        });
    }

    public function update(School $school, array $data): School
    {
        $school->update($data);
        return $school->fresh();
    }

    public function delete(School $school): bool
    {
        return $school->delete();
    }

    public function getStatistics(School $school): array
    {
        return [
            'students_count' => $school->students()->count(),
            'employees_count' => $school->employees()->count(),
//            'departments_count' => $school->departments()->count(),
//            'classes_count' => $school->classes()->count(),
        ];
    }
}
