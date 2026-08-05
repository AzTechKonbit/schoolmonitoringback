<?php

namespace Modules\Academic\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Academic\Models\Assignment;
use Modules\Academic\Models\Course;

class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => 'Devoir ' . Str::random(6),
            'description' => 'Sujet du devoir',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ];
    }
}