<?php

namespace Modules\Academic\Tests\Unit;

use App\Core\Models\School;
use Modules\Academic\Models\{Course};
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
    }

    public function test_can_list_courses(): void
    {
        Course::factory()->count(5)->create();

        $response = $this->getJson('/academic/courses');

        $response->assertStatus(200);
    }

    public function test_can_get_course(): void
    {
        $course = Course::factory()->create();

        $response = $this->getJson("/academic/courses/{$course->id}");

        $response->assertStatus(200);
    }
}
