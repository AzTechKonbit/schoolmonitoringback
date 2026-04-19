<?php

namespace App\Core\UserManagement\Tests\Unit;

use Tests\TestCase;
use App\Core\UserManagement\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_student(): void
    {
        $response = $this->postJson('/api/students', [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'pierre@test.com',
            'password' => 'password123',
            'school_id' => 1,
            'dob' => '2015-05-20',
            'class_id' => 1,
        ]);

        $response->assertStatus(201);
    }

    public function test_can_list_students(): void
    {
        $response = $this->getJson('/api/students');

        $response->assertStatus(200);
    }

    public function test_can_assign_student_to_class(): void
    {
        $student = Student::factory()->create();

        $response = $this->postJson("/api/students/{$student->id}/class", [
            'class_id' => 1,
        ]);

        $response->assertStatus(200);
    }

    public function test_can_get_attendance_report(): void
    {
        $student = Student::factory()->create();

        $response = $this->getJson("/api/students/{$student->id}/attendance-report");

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }
}
