<?php

namespace Modules\Academic\Tests\Unit;

use App\Core\UserManagement\Models\Student;
use Modules\Academic\Models\Attendance;
use Modules\Academic\Models\Schedule;
use Modules\Academic\Services\AttendanceService;
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
        $this->assertDatabaseHas('attendances', ['student_id' => $student_id, 'schedule_id' => $schedule_id]);
    }

    public function test_record_attendance_updates_existing(): void
    {
        $student_id = Student::factory()->create()->getKey();
        $schedule_id = Schedule::factory()->create()->getKey();

        $this->postJson('/academic/attendances', [
            'student_id' => $student_id,
            'schedule_id' => $schedule_id,
            'status' => 'present',
            'recorded_at' => now()->toDateString(),
        ])->assertStatus(201);

        $this->postJson('/academic/attendances', [
            'student_id' => $student_id,
            'schedule_id' => $schedule_id,
            'status' => 'absent',
            'recorded_at' => now()->toDateString(),
        ])->assertStatus(201);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student_id,
            'schedule_id' => $schedule_id,
            'status' => 'absent',
        ]);
    }

    public function test_record_attendance_requires_student(): void
    {
        $this->postJson('/academic/attendances', [
            'schedule_id' => Schedule::factory()->create()->id,
            'status' => 'present',
        ])->assertStatus(422);
    }

    public function test_record_attendance_requires_valid_status(): void
    {
        $this->postJson('/academic/attendances', [
            'student_id' => Student::factory()->create()->id,
            'schedule_id' => Schedule::factory()->create()->id,
            'status' => 'invalid-status',
        ])->assertStatus(422);
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
        $this->assertDatabaseCount('attendances', 2);
    }

    public function test_bulk_record_requires_attendances_array(): void
    {
        $this->postJson('/academic/attendances/bulk', [])->assertStatus(422);
    }

    public function test_can_list_attendances(): void
    {
        Attendance::factory(3)->create();

        $response = $this->getJson('/academic/attendances');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data', 'meta']);
    }

    public function test_can_filter_attendances_by_status(): void
    {
        Attendance::factory()->create(['status' => 'late']);
        Attendance::factory()->count(2)->create(['status' => 'absent']);

        $response = $this->getJson('/academic/attendances?status=late');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_get_student_attendance(): void
    {
        $student = Student::factory()->create();
        Attendance::factory()->count(2)->create(['student_id' => $student->id]);

        $response = $this->getJson("/academic/attendances/student/{$student->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_get_student_attendance_with_date_range(): void
    {
        $student = Student::factory()->create();
        Attendance::factory()->create(['student_id' => $student->id]);

        $response = $this->getJson("/academic/attendances/student/{$student->id}?start_date=2000-01-01&end_date=2100-01-01");

        $response->assertStatus(200);
    }

    public function test_can_get_class_attendance_report(): void
    {
        Attendance::factory(5)->create();
        $response = $this->getJson('/academic/attendances/class/1/report');
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_can_get_class_attendance_report_with_date(): void
    {
        $schedule = Schedule::factory()->create();
        Attendance::factory()->create(['schedule_id' => $schedule->id]);

        $response = $this->getJson('/academic/attendances/class/' . $schedule->class_id . '/report?date=' . now()->toDateString());

        $response->assertStatus(200);
    }

    public function test_can_get_attendance_report(): void
    {
        $student = Student::factory()->create();

        $response = $this->getJson("/students/{$student->getKey()}/attendance-report");

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }
}