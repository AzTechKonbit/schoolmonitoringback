<?php

namespace App\Core\UserManagement\Database\Factories;

use App\Core\Models\School;
use App\Core\Models\User;
use App\Core\UserManagement\Models\ParentModel;
use App\Core\UserManagement\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'dob' => now()->subYears(rand(5, 12))->format('Y-m-d'),
            'user_id' => User::factory(),
            'parent_id' => ParentModel::factory(),
            'student_number_id' => 'STU-' . now()->subYears(rand(5, 12))->format('Y') . '-' . str_pad(rand(1, 9999), 6, '0', STR_PAD_LEFT),
            'school_id' => School::factory(),
        ];
    }
}
