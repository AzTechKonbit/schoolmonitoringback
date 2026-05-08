<?php

namespace App\Core\UserManagement\Database\Factories;

use App\Core\Models\School;
use App\Core\UserManagement\Models\{Employee, EmployeeType};
use App\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'employee_code' => 'EMP-' . str_pad(rand(1, 9999), 6, '0', STR_PAD_LEFT),
            'employment_status' => 'active',
            'hire_date' => now()->subYears(rand(1, 5))->format('Y-m-d'),
            'employment_contract_type' => 'full-time',
            'salary_type' => 'monthly',
            'base_salary' => rand(300000, 1000000),
            'employee_type_id' => EmployeeType::factory(),
            'user_id' => User::factory(),
            'school_id' => School::factory(),
            'created_by' => User::factory(),
        ];
    }
}

