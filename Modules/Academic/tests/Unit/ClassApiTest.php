<?php

namespace Modules\Academic\Tests\Unit;

use App\Core\Models\School;
use App\Core\UserManagement\Models\Student;
use Modules\Academic\Models\Attendance;
use Modules\Academic\Models\Course;
use Modules\Academic\Models\Schedule;
use Modules\Academic\Models\SchoolClass;
use Tests\TestControllerCase;

class ClassApiTest extends TestControllerCase
{

    public function test_can_create_class(): void
    {
        $schoolId = School::factory()->create()->getKey();
        $response = $this->postJson('/academic/classes', [
            'name' => 'CP',
            'school_id' => $schoolId,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('schools_classes', ['school_id' => $schoolId]);
    }

    public function test_can_list_classes(): void
    {
        SchoolClass::factory()->count(5)->create();

        $response = $this->getJson('/academic/classes');

        $response->assertStatus(200);
    }

    public function test_can_search_classes(): void
    {
        SchoolClass::factory()->create(['name' => 'Terminale']);
        SchoolClass::factory()->count(3)->create();

        $response = $this->getJson('/academic/classes?search=Terminale');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_show_class(): void
    {
        $class = SchoolClass::factory()->create();

        $response = $this->getJson("/academic/classes/{$class->id}");

        $response->assertStatus(200)
            ->assertJson(['data' => ['id' => $class->id]]);
    }

    public function test_show_non_existent_class_returns_404(): void
    {
        $this->getJson('/academic/classes/99999')->assertStatus(404);
    }

    public function test_can_update_class(): void
    {
        $class = SchoolClass::factory()->create();

        $response = $this->putJson("/academic/classes/{$class->id}", [
            'name' => 'CP2',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('classes', ['id' => $class->id, 'name' => 'CP2']);
    }

    public function test_update_non_existent_class_returns_404(): void
    {
        $this->putJson('/academic/classes/99999', ['name' => 'x'])->assertStatus(404);
    }

    public function test_can_destroy_class(): void
    {
        $class = SchoolClass::factory()->create();

        $this->deleteJson("/academic/classes/{$class->id}")->assertStatus(200);

        $this->assertDatabaseMissing('classes', ['id' => $class->id]);
    }

    public function test_destroy_non_existent_class_returns_404(): void
    {
        $this->deleteJson('/academic/classes/99999')->assertStatus(404);
    }

    public function test_can_get_class_students(): void
    {
        $item = SchoolClass::factory()->create();
        $item->students()->attach(Student::factory(3)->create()->pluck('id'));


        $response = $this->getJson("/academic/classes/{$item->getKey()}/students");

        $response->assertStatus(200);
    }

    public function test_get_class_students_non_existent_class_returns_404(): void
    {
        $this->getJson('/academic/classes/99999/students')->assertStatus(404);
    }

    public function test_can_get_class_schedule(): void
    {
        $item = SchoolClass::factory()->create();
        $item->schedules()->saveMany(Schedule::factory(5)->make());

        $response = $this->getJson("/academic/classes/{$item->getKey()}/schedule");

        $response->assertStatus(200);
    }

    public function test_can_get_class_schedule_filtered_by_day(): void
    {
        $item = SchoolClass::factory()->create();
        $item->schedules()->saveMany(Schedule::factory(5)->make());

        $response = $this->getJson("/academic/classes/{$item->getKey()}/schedule?day_of_week=lundi");

        $response->assertStatus(200);
    }

    public function test_get_class_schedule_non_existent_class_returns_404(): void
    {
        $this->getJson('/academic/classes/99999/schedule')->assertStatus(404);
    }

    public function test_can_get_class_attendance_report(): void
    {
        $class = SchoolClass::factory()->create();
        $schedule = Schedule::factory()->create(['class_id' => $class->id]);
        Attendance::factory()->count(3)->create(['schedule_id' => $schedule->id]);

        $response = $this->getJson("/academic/classes/{$class->id}/attendance-report");

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data' => ['total', 'present', 'absent', 'attendance_rate']]);
    }

    public function test_get_class_attendance_report_with_date(): void
    {
        $class = SchoolClass::factory()->create();
        $schedule = Schedule::factory()->create(['class_id' => $class->id]);
        Attendance::factory()->create(['schedule_id' => $schedule->id]);

        $response = $this->getJson("/academic/classes/{$class->id}/attendance-report?date=" . now()->toDateString());

        $response->assertStatus(200);
    }

    public function test_get_class_attendance_report_non_existent_class_returns_404(): void
    {
        $this->getJson('/academic/classes/99999/attendance-report')->assertStatus(404);
    }

    public function test_can_assign_student_to_class(): void
    {
        $student = Student::factory()->create();
        $classId = SchoolClass::factory()->create()->getKey();

        $response = $this->postJson("/students/{$student->id}/class", [
            'class_id' => $classId,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('classes_students', ['student_id' => $student->id, 'class_id' => $classId]);
    }

    public function test_class_service_assign_and_remove_course_flow(): void
    {
        $class = SchoolClass::factory()->create();
        $course = Course::factory()->create();
        $student = Student::factory()->create();

        $service = app(\Modules\Academic\Services\ClassService::class);

        $service->assignCourse($class, $course->id);
        $this->assertDatabaseHas('classes_courses', ['classe_id' => $class->id, 'course_id' => $course->id]);

        $service->addStudent($class, $student->id);
        $this->assertDatabaseHas('classes_students', ['class_id' => $class->id, 'student_id' => $student->id]);

        $service->removeStudent($class, $student->id);
        $this->assertDatabaseMissing('classes_students', ['class_id' => $class->id, 'student_id' => $student->id]);
    }

    public function test_class_service_get_schedule_with_nested_user(): void
    {
        $class = SchoolClass::factory()->create();
        $schedule = Schedule::factory()->create();
        $class->schedules()->save($schedule);

        $result = app(\Modules\Academic\Services\ClassService::class)->getSchedule($class, 'lundi');

        $this->assertTrue($result instanceof \Illuminate\Database\Eloquent\Collection);
    }
}