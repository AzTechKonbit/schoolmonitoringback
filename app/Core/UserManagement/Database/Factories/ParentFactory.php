<?php

namespace App\Core\UserManagement\Database\Factories;

use App\Core\Models\User;
use App\Core\UserManagement\Models\ParentModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParentFactory extends Factory
{
    protected $model = ParentModel::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'user_id2' => null,
        ];
    }
}
