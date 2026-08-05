<?php

namespace Tests\Unit;

use App\Core\UserManagement\Models\Student;
use App\Core\UserManagement\Models\Teacher;
use Modules\Academic\Models\Attendance;
use Modules\Academic\Models\Course;
use Modules\Academic\Models\Schedule;
use Modules\Academic\Models\SchoolClass;
use Modules\Academic\Services\AttendanceService;
use Modules\Academic\Services\CourseService;
use Modules\Academic\Services\ScheduleService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AcademicServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_schedule_service_get_student_schedule(): void
    {
        $student = Student::factory()->create();
        $class = SchoolClass::factory()->create();
        $student->classes()->attach($class->id);

        $schedule = Schedule::factory()->create(['class_id' => $class->id]);

        $result = app(ScheduleService::class)->getStudentSchedule($student->id);

        $this->assertTrue($result->contains('id', $schedule->id));
    }

    public function test_schedule_service_get_student_schedule_filtered_by_day(): void
    {
        $student = Student::factory()->create();
        $class = SchoolClass::factory()->create();
        $student->classes()->attach($class->id);

        Schedule::factory()->create(['class_id' => $class->id, 'day_of_week' => 'lundi']);

        $result = app(ScheduleService::class)->getStudentSchedule($student->id, 'lundi');

        $this->assertGreaterThanOrEqual(1, $result->count());
    }

    public function test_schedule_service_get_teacher_schedule(): void
    {
        $teacher = Teacher::factory()->create();
        Schedule::factory()->create(['teacher_id' => $teacher->id, 'day_of_week' => 'mardi']);

        $result = app(ScheduleService::class)->getTeacherSchedule($teacher->id, 'mardi');

        $this->assertCount(1, $result);
    }

    public function test_schedule_service_get_teacher_schedule_all_days(): void
    {
        $teacher = Teacher::factory()->create();
        Schedule::factory()->count(2)->create(['teacher_id' => $teacher->id]);

        $result = app(ScheduleService::class)->getTeacherSchedule($teacher->id);

        $this->assertCount(2, $result);
    }

    public function test_schedule_service_find_by_id(): void
    {
        $schedule = Schedule::factory()->create();

        $found = app(ScheduleService::class)->findById($schedule->id);

        $this->assertNotNull($found);
    }

    public function test_attendance_service_find_by_id(): void
    {
        $attendance = Attendance::factory()->create();

        $found = app(AttendanceService::class)->findById($attendance->id);

        $this->assertNotNull($found);
    }

    public function test_attendance_service_get_all_with_all_filters(): void
    {
        $attendance = Attendance::factory()->create(['status' => 'present']);

        $result = app(AttendanceService::class)->getAll([
            'student_id' => $attendance->student_id,
            'schedule_id' => $attendance->schedule_id,
            'status' => 'present',
            'date_from' => '2000-01-01',
            'date_to' => '2100-01-01',
        ], 15);

        $this->assertGreaterThanOrEqual(1, $result->total());
    }

    public function test_attendance_service_get_student_attendance(): void
    {
        $student = Student::factory()->create();
        Attendance::factory()->count(2)->create(['student_id' => $student->id]);

        $result = app(AttendanceService::class)->getStudentAttendance($student->id, '2000-01-01', '2100-01-01');

        $this->assertCount(2, $result);
    }

    public function test_attendance_service_get_class_attendance_report(): void
    {
        $class = SchoolClass::factory()->create();
        $schedule = Schedule::factory()->create(['class_id' => $class->id]);
        Attendance::factory()->count(3)->create(['schedule_id' => $schedule->id]);

        $report = app(AttendanceService::class)->getClassAttendanceReport($class->id);

        $this->assertArrayHasKey('total', $report);
        $this->assertEquals(3, $report['total']);
        $this->assertArrayHasKey('attendance_rate', $report);
    }

    public function test_course_service_assign_and_remove_teacher(): void
    {
        $course = Course::factory()->create();
        $teacher = Teacher::factory()->create();

        $service = app(CourseService::class);
        $service->assignTeacher($course, $teacher->id);

        $this->assertDatabaseHas('teachers_courses', ['course_id' => $course->id, 'teacher_id' => $teacher->id]);

        $service->removeTeacher($course, $teacher->id);

        $this->assertDatabaseMissing('teachers_courses', ['course_id' => $course->id, 'teacher_id' => $teacher->id]);
    }

    public function test_course_service_generates_code_when_missing(): void
    {
        $course = app(CourseService::class)->create(['name' => 'Sans code']);

        $this->assertStringStartsWith('CRS-', $course->code);
    }
}