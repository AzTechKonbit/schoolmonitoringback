<?php

namespace Tests\Unit;

use App\Core\Models\School;
use App\Core\UserManagement\Models\ParentModel;
use App\Core\UserManagement\Models\Student;
use App\Core\UserManagement\Services\StudentService;
use Modules\Academic\Models\Attendance;
use Modules\Academic\Models\Schedule;
use Modules\Academic\Models\SchoolClass;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_find_by_id(): void
    {
        $student = Student::factory()->create();

        $found = app(StudentService::class)->findById($student->id);

        $this->assertNotNull($found);
    }

    public function test_service_get_all_with_filters(): void
    {
        $school = School::factory()->create();
        $parent = ParentModel::factory()->create();
        $class = SchoolClass::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id, 'parent_id' => $parent->id]);
        $student->classes()->attach($class->id);

        $result = app(StudentService::class)->getAll([
            'school_id' => $school->id,
            'parent_id' => $parent->id,
            'class_id' => $class->id,
            'search' => $student->user->first_name,
        ], 15);

        $this->assertGreaterThanOrEqual(1, $result->total());
    }

    public function test_service_update_student(): void
    {
        $student = Student::factory()->create();

        $updated = app(StudentService::class)->update($student, [
            'first_name' => 'Renamed',
            'dob' => '2011-05-05',
        ]);

        $this->assertEquals('Renamed', $updated->user->first_name);
        $this->assertEquals('2011-05-05', $updated->dob->format('Y-m-d'));
    }

    public function test_service_assign_and_remove_from_class(): void
    {
        $student = Student::factory()->create();
        $class = SchoolClass::factory()->create();

        $service = app(StudentService::class);
        $service->assignToClass($student, $class->id);
        $this->assertDatabaseHas('classes_students', ['student_id' => $student->id, 'class_id' => $class->id]);

        $service->removeFromClass($student, $class->id);
        $this->assertDatabaseMissing('classes_students', ['student_id' => $student->id, 'class_id' => $class->id]);
    }

    public function test_service_get_attendance_report_with_date_range(): void
    {
        $student = Student::factory()->create();
        $schedule = Schedule::factory()->create();
        Attendance::factory()->create(['student_id' => $student->id, 'schedule_id' => $schedule->id, 'status' => 'present']);

        $report = app(StudentService::class)->getAttendanceReport($student, '2000-01-01', '2100-01-01');

        $this->assertEquals(1, $report['total']);
        $this->assertEquals(1, $report['present']);
        $this->assertEquals(100.0, $report['attendance_rate']);
    }

    public function test_service_get_attendance_report_empty_returns_zero_rate(): void
    {
        $student = Student::factory()->create();

        $report = app(StudentService::class)->getAttendanceReport($student);

        $this->assertEquals(0, $report['total']);
        $this->assertEquals(0, $report['attendance_rate']);
    }

    public function test_service_delete_student(): void
    {
        $student = Student::factory()->create();
        $userId = $student->user_id;

        $result = app(StudentService::class)->delete($student);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertSoftDeleted('users', ['id' => $userId]);
    }
}