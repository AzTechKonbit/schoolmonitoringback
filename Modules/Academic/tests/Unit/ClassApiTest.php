<?php

namespace Modules\Academic\Tests\Unit;

use App\Core\Models\School;
use App\Core\UserManagement\Models\Student;
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
    }

    public function test_can_list_classes(): void
    {
        SchoolClass::factory()->count(5)->create();

        $response = $this->getJson('/academic/classes');

        $response->assertStatus(200);
    }

    public function test_can_get_class_students(): void
    {
        $item = SchoolClass::factory()->create();
        $item->students()->saveMany(Student::factory(3)->make());


        $response = $this->getJson("/academic/classes/{$item->getKey()}/students");

        $response->assertStatus(200);
    }

    public function test_can_get_class_schedule(): void
    {
        $item = SchoolClass::factory()->create();
        $item->schedules()->saveMany(Schedule::factory(5)->make());

        $response = $this->getJson("/academic/classes/{$item->getKey()}/schedule");

        $response->assertStatus(200);
    }

    public function test_can_assign_student_to_class(): void
    {
        $student = Student::factory()->create();
        $classId = SchoolClass::factory()->create()->getKey();

        $response = $this->postJson("/students/{$student->id}/class", [
            'class_id' => $classId,
        ]);

        $response->assertStatus(200);
    }


}
