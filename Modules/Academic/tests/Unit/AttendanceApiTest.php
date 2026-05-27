<?php

namespace Modules\Academic\Tests\Unit;

use App\Core\UserManagement\Models\Student;
use Modules\Academic\Models\Attendance;
use Modules\Academic\Models\Schedule;
use Tests\TestControllerCase;

class AttendanceApiTest extends TestControllerCase
{

    public function test_can_record_attendance(): void
    {
        $student_id = Student::factory()->create()->getKey();
        $schedule_id = Schedule::factory()->create()->getKey();
        $response = $this->postJson('/academic/attendances', [
            'student_id' => $student_id,
            'schedule_id' => $schedule_id,
            'status' => 'present',
        ]);

        $response->assertStatus(201);
    }

    public function test_can_bulk_record_attendance(): void
    {
        Student::factory(2)->create();
        Schedule::factory()->create();
        $response = $this->postJson('/academic/attendances/bulk', [
            'attendances' => [
                ['student_id' => 1, 'schedule_id' => 1, 'status' => 'present'],
                ['student_id' => 2, 'schedule_id' => 1, 'status' => 'present'],
            ],
        ]);

        $response->assertStatus(201);
    }

    public function test_can_get_class_attendance_report(): void
    {
        Attendance::factory(5)->create();
        $response = $this->getJson('/academic/attendances/class/1/report');
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_can_get_attendance_report(): void
    {
        $student = Student::factory()->create();

        $response = $this->getJson("/students/{$student->getKey()}/attendance-report");

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }
}
