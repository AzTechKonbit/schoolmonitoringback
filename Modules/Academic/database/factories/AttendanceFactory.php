<?php

namespace Modules\Academic\Database\Factories;

use App\Core\Models\User;
use App\Core\UserManagement\Models\Student;
use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academic\Models\Attendance;
use Modules\Academic\Models\Schedule;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'schedule_id' => Schedule::factory(),
            'status' => collect(AttendanceStatus::cases())->random(),
            'recorded_by' => User::factory(),
            'recorded_at' => now()->toDateString(),
        ];
    }
}
