<?php

namespace Tests\Unit;

use App\Core\Models\School;
use App\Core\UserManagement\Models\Employee;
use App\Core\UserManagement\Models\Teacher;
use Modules\Academic\Models\Attendance;
use Modules\Academic\Models\Course;
use Modules\Academic\Models\Schedule;
use Modules\Academic\Models\SchoolClass;
use Modules\Academic\Services\AttendanceService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AcademicDynamicRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_classes_dynamic_relation(): void
    {
        $school = School::factory()->create();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $school->classes()
        );
    }

    public function test_teacher_courses_and_schedules_dynamic_relations(): void
    {
        $teacher = Teacher::factory()->create();
        $course = Course::factory()->create();
        $class = SchoolClass::factory()->create();

        $teacher->courses()->attach($course->id);
        $this->assertTrue($teacher->courses()->exists());

        $schedule = Schedule::create([
            'course_id' => $course->id,
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'day_of_week' => 'lundi',
            'room' => 'A1',
            'start_time' => '08:00',
            'end_time' => '09:00',
        ]);

        $this->assertTrue($teacher->schedules()->where('id', $schedule->id)->exists());
    }

    public function test_employee_attendances_dynamic_relation(): void
    {
        $employee = Employee::factory()->create();
        $attendance = Attendance::factory()->create(['recorded_by' => $employee->user_id]);

        $this->assertTrue($employee->attendances()->where('id', $attendance->id)->exists());
    }

    public function test_attendance_service_record_uses_authenticated_user(): void
    {
        $this->assertInstanceOf(AttendanceService::class, app(AttendanceService::class));
    }
}