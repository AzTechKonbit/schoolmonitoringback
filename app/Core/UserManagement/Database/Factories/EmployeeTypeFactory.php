<?php

namespace App\Core\UserManagement\Database\Factories;

use App\Core\UserManagement\Models\EmployeeType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmployeeTypeFactory extends Factory
{
    protected $model = EmployeeType::class;

    public function definition(): array
    {
        return [
            'code' => Str::random(5),
            'type' => 'Enseignant',
            'description' => 'Description',
            'is_active' => true,
        ];
    }
}
