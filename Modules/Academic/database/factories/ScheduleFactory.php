<?php

namespace Modules\Academic\Database\Factories;

use App\Core\UserManagement\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academic\Models\Course;
use Modules\Academic\Models\Schedule;
use Modules\Academic\Models\SchoolClass;

class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'class_id' => SchoolClass::factory(),
            'teacher_id' => Teacher::factory(),
            'day_of_week' => collect(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'])->random(),
            'room' => 'Salle ' . chr(65 + rand(0, 4)),
            'start_time' => '08:00',
            'end_time' => '09:00',
        ];
    }
}
