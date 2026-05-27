<?php

namespace Modules\Academic\Database\Factories;

use App\Core\Models\School;
use Modules\Academic\Models\{Course, SchoolClass, Schedule, Attendance, Term};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'name' => 'Cours ' . Str::random(10),
            'description' => 'Description du cours',
            'code' => strtoupper(Str::random(3)),
            'credit' => rand(2, 5),
            'total_hours' => rand(60, 120),
            'hours_td' => rand(20, 40),
            'hours_tp' => rand(10, 30),
        ];
    }
}
