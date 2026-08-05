<?php

namespace Modules\Academic\Tests\Unit;

use App\Core\UserManagement\Models\Teacher;
use Modules\Academic\Models\Course;
use Modules\Academic\Models\Schedule;
use Modules\Academic\Models\SchoolClass;
use Tests\TestControllerCase;

class ScheduleApiTest extends TestControllerCase
{
    public function test_can_create_schedule(): void
    {
        $course = Course::factory()->create();
        $class = SchoolClass::factory()->create();
        $teacher = Teacher::factory()->create();

        $response = $this->postJson('/academic/schedules', [
            'course_id' => $course->id,
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'day_of_week' => 'lundi',
            'room' => 'Salle A',
            'start_time' => '08:00',
            'end_time' => '09:00',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('schedules', ['course_id' => $course->id]);
    }

    public function test_can_list_schedules(): void
    {
        Schedule::factory()->count(3)->create();

        $response = $this->getJson('/academic/schedules');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data', 'meta']);
    }

    public function test_can_filter_schedules_by_course(): void
    {
        $course = Course::factory()->create();
        Schedule::factory()->count(2)->create(['course_id' => $course->id]);
        Schedule::factory()->count(2)->create();

        $response = $this->getJson("/academic/schedules?course_id={$course->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_show_schedule(): void
    {
        $schedule = Schedule::factory()->create();

        $response = $this->getJson("/academic/schedules/{$schedule->id}");

        $response->assertStatus(200)
            ->assertJson(['data' => ['id' => $schedule->id]]);
    }

    public function test_show_non_existent_schedule_returns_404(): void
    {
        $this->getJson('/academic/schedules/99999')->assertStatus(404);
    }

    public function test_can_update_schedule(): void
    {
        $schedule = Schedule::factory()->create();

        $response = $this->putJson("/academic/schedules/{$schedule->id}", [
            'room' => 'Salle Z',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('schedules', ['id' => $schedule->id, 'room' => 'Salle Z']);
    }

    public function test_update_non_existent_schedule_returns_404(): void
    {
        $this->putJson('/academic/schedules/99999', ['room' => 'Salle Z'])->assertStatus(404);
    }

    public function test_can_destroy_schedule(): void
    {
        $schedule = Schedule::factory()->create();

        $this->deleteJson("/academic/schedules/{$schedule->id}")->assertStatus(200);

        $this->assertDatabaseMissing('schedules', ['id' => $schedule->id]);
    }

    public function test_destroy_non_existent_schedule_returns_404(): void
    {
        $this->deleteJson('/academic/schedules/99999')->assertStatus(404);
    }

    public function test_create_schedule_requires_course(): void
    {
        $this->postJson('/academic/schedules', [
            'class_id' => SchoolClass::factory()->create()->id,
            'teacher_id' => Teacher::factory()->create()->id,
            'day_of_week' => 'lundi',
            'room' => 'A',
            'start_time' => '08:00',
            'end_time' => '09:00',
        ])->assertStatus(422);
    }
}