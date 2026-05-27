<?php

namespace App\Core\UserManagement\Database\Factories;

use App\Core\UserManagement\Models\Employee;
use App\Core\UserManagement\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'teacher_number_id' => 'FOR-' . now()->subYears(rand(5, 12))->format('Y') . '-' . str_pad(rand(1, 9999), 6, '0', STR_PAD_LEFT),
        ];
    }
}
