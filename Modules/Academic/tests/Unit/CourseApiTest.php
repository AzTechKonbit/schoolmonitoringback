<?php

namespace Modules\Academic\Tests\Unit;

use App\Core\Models\School;
use App\Core\UserManagement\Models\Teacher;
use Modules\Academic\Models\{Course, SchoolClass};
use Tests\TestControllerCase;

class CourseApiTest extends TestControllerCase
{

    public function test_can_create_course(): void
    {
        $response = $this->postJson('/academic/courses', [
            'name' => 'Mathématiques',
            'description' => 'Cours de math',
            'code' => 'MATH',
            'credit' => 4,
            'total_hours' => 120,
            'school_id' => School::factory()->create()->getKey(),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('courses', ['code' => 'MATH']);
    }

    public function test_can_create_course_with_teacher_and_class(): void
    {
        $teacher = Teacher::factory()->create();
        $class = SchoolClass::factory()->create();

        $response = $this->postJson('/academic/courses', [
            'name' => 'Physique',
            'school_id' => School::factory()->create()->getKey(),
            'teacher_ids' => [$teacher->id],
            'class_ids' => [$class->id],
        ]);

        $response->assertStatus(201);
        $course = Course::where('name', 'Physique')->first();
        $this->assertDatabaseHas('teachers_courses', ['course_id' => $course->id, 'teacher_id' => $teacher->id]);
        $this->assertDatabaseHas('classes_courses', ['course_id' => $course->id, 'classe_id' => $class->id]);
    }

    public function test_can_list_courses(): void
    {
        Course::factory()->count(5)->create();

        $response = $this->getJson('/academic/courses');

        $response->assertStatus(200);
    }

    public function test_can_search_courses(): void
    {
        Course::factory()->create(['name' => 'Astronomie']);
        Course::factory()->count(3)->create();

        $response = $this->getJson('/academic/courses?search=Astronomie');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_get_course(): void
    {
        $course = Course::factory()->create();

        $response = $this->getJson("/academic/courses/{$course->id}");

        $response->assertStatus(200);
    }

    public function test_get_non_existent_course_returns_404(): void
    {
        $this->getJson('/academic/courses/99999')->assertStatus(404);
    }

    public function test_can_update_course(): void
    {
        $course = Course::factory()->create();
        $teacher = Teacher::factory()->create();

        $response = $this->putJson("/academic/courses/{$course->id}", [
            'name' => 'Chimie',
            'teacher_ids' => [$teacher->id],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'name' => 'Chimie']);
        $this->assertDatabaseHas('teachers_courses', ['course_id' => $course->id, 'teacher_id' => $teacher->id]);
    }

    public function test_update_non_existent_course_returns_404(): void
    {
        $this->putJson('/academic/courses/99999', ['name' => 'x'])->assertStatus(404);
    }

    public function test_can_destroy_course(): void
    {
        $course = Course::factory()->create();

        $this->deleteJson("/academic/courses/{$course->id}")->assertStatus(200);

        $this->assertSoftDeleted('courses', ['id' => $course->id]);
    }

    public function test_destroy_non_existent_course_returns_404(): void
    {
        $this->deleteJson('/academic/courses/99999')->assertStatus(404);
    }

    public function test_create_course_requires_name(): void
    {
        $this->postJson('/academic/courses', [
            'school_id' => School::factory()->create()->getKey(),
        ])->assertStatus(422);
    }
}