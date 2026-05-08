<?php

namespace App\Core\Database\Factories;

use App\Core\Models\{School, User};
use App\Enums\{SexeRole, Status, UserRole};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'phone' => $this->faker->phoneNumber(),
            'gender' => collect(SexeRole::cases())->random(),
            'role' => collect(UserRole::cases())->random(),
            'status' => collect(Status::cases())->random(),
            'remember_token' => Str::random(10),
        ];
    }
}
