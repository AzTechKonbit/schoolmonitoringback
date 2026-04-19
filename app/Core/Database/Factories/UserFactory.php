<?php

namespace App\Core\Database\Factories;

use App\Core\Models\{School, User};
use App\Enums\{Status, UserRole};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $school = School::inRandomOrder()->value('uuid');
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'phone' => $this->faker->phoneNumber(),
            'gender' => collect(['M', 'F'])->random(),
            'role' => collect(UserRole::class)->random(),
            'status' => collect(Status::class)->random(),
            'school_id' => $school,
            'remember_token' => Str::random(10),
        ];
    }
}
