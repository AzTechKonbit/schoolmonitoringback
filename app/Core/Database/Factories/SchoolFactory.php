<?php

namespace App\Core\Database\Factories;

use App\Core\Models\School;
use App\Enums\{SchoolType, Status};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition(): array
    {
        $year = (int) $this->faker->dateTimeThisCentury()->format("Y");
        return [
            'name' => 'École ' . Str::random(10),
            'type' => collect(SchoolType::cases())->random(),
            'default_language' => 'fr',
            'academic_year' => $year.'-'.$year+1,
            'status' => collect(Status::cases())->random(),
        ];
    }
}
