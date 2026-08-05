<?php

namespace App\Core\UserManagement\Tests\Unit;

use App\Core\Models\School;
use App\Core\Models\User;
use App\Core\UserManagement\Models\ParentModel;
use App\Core\UserManagement\Models\Student;
use Modules\Academic\Models\Assignment;
use Modules\Academic\Models\AssignmentSubmission;
use Modules\Academic\Models\SchoolClass;
use Tests\TestControllerCase;

class StudentApiTest extends TestControllerCase
{

    public function test_can_create_student(): void
    {
        $response = $this->postJson('/students', [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'pierre@test.com',
            'password' => 'password123',
            'gender' => 'M',
            'school_id' => School::factory()->create()->getKey(),
            'parent_id' => ParentModel::factory()->create()->getKey(),
            'dob' => '2015-05-20',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('students', ['student_number_id' => 'STU-' . date('Y') . '-000001']);
        $this->assertDatabaseHas('users', ['email' => 'pierre@test.com', 'role' => 'student']);
    }

    public function test_can_create_student_with_new_parent(): void
    {
        $response = $this->postJson('/students', [
            'first_name' => 'Marie',
            'last_name' => 'Durand',
            'email' => 'marie.durand@test.com',
            'gender' => 'F',
            'school_id' => School::factory()->create()->getKey(),
            'dob' => '2014-03-10',
            'parent_first_name' => 'Maman',
            'parent_last_name' => 'Durand',
            'parent_email' => 'maman@test.com',
            'parent_phone' => '+2250100000000',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'maman@test.com', 'role' => 'parent']);
    }

    public function test_can_create_student_attached_to_class(): void
    {
        $class = SchoolClass::factory()->create();

        $response = $this->postJson('/students', [
            'first_name' => 'Lucas',
            'last_name' => 'Durand',
            'email' => 'lucas.durand@test.com',
            'gender' => 'M',
            'school_id' => School::factory()->create()->getKey(),
            'dob' => '2014-03-10',
            'class_id' => $class->id,
            'parent_email' => 'parent.lucas@test.com',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('classes_students', ['class_id' => $class->id]);
    }

    public function test_can_list_students(): void
    {
        Student::factory(5)->create();
        $response = $this->getJson('/students');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data', 'meta']);
    }

    public function test_can_filter_students_by_school(): void
    {
        $school = School::factory()->create();
        Student::factory()->count(2)->create(['school_id' => $school->id]);
        Student::factory()->count(3)->create();

        $response = $this->getJson("/students?school_id={$school->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_search_students(): void
    {
        $user = User::factory()->create(['first_name' => 'Recherche']);
        Student::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/students?search=Recherche');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_show_student(): void
    {
        $student = Student::factory()->create();

        $response = $this->getJson("/students/{$student->id}");

        $response->assertStatus(200)
            ->assertJson(['data' => ['id' => $student->id]]);
    }

    public function test_show_non_existent_student_returns_404(): void
    {
        $this->getJson('/students/99999')->assertStatus(404);
    }

    public function test_can_update_student(): void
    {
        $student = Student::factory()->create();

        $response = $this->putJson("/students/{$student->id}", [
            'first_name' => 'PierreUpdated',
            'dob' => '2010-01-01',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $student->user_id, 'first_name' => 'PierreUpdated']);
    }

    public function test_update_non_existent_student_returns_404(): void
    {
        $this->putJson('/students/99999', ['dob' => '2010-01-01'])->assertStatus(404);
    }

    public function test_can_destroy_student(): void
    {
        $student = Student::factory()->create();
        $userId = $student->user_id;

        $this->deleteJson("/students/{$student->id}")->assertStatus(200);

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertSoftDeleted('users', ['id' => $userId]);
    }

    public function test_destroy_non_existent_student_returns_404(): void
    {
        $this->deleteJson('/students/99999')->assertStatus(404);
    }

    public function test_can_assign_student_to_class(): void
    {
        $student = Student::factory()->create();
        $class = SchoolClass::factory()->create();

        $response = $this->postJson("/students/{$student->id}/class", [
            'class_id' => $class->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('classes_students', ['student_id' => $student->id, 'class_id' => $class->id]);
    }

    public function test_can_remove_student_from_class(): void
    {
        $student = Student::factory()->create();
        $class = SchoolClass::factory()->create();
        $student->classes()->attach($class->id);

        $response = $this->deleteJson("/students/{$student->id}/class", [
            'class_id' => $class->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('classes_students', ['student_id' => $student->id, 'class_id' => $class->id]);
    }

    public function test_assign_student_to_non_existent_student_returns_404(): void
    {
        $this->postJson('/students/99999/class', ['class_id' => 1])->assertStatus(404);
    }

    public function test_remove_student_from_class_non_existent_student_returns_404(): void
    {
        $this->deleteJson('/students/99999/class', ['class_id' => 1])->assertStatus(404);
    }

    public function test_can_get_attendance_report(): void
    {
        $student = Student::factory()->create();

        $response = $this->getJson("/students/{$student->id}/attendance-report");

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data' => ['total', 'present', 'absent', 'late', 'attendance_rate']]);
    }

    public function test_attendance_report_non_existent_student_returns_404(): void
    {
        $this->getJson('/students/99999/attendance-report')->assertStatus(404);
    }

    public function test_can_get_grades_report_for_non_existent_student_returns_404(): void
    {
        $this->getJson('/students/99999/grades-report')->assertStatus(404);
    }

    public function test_grades_report_non_existent_student_returns_404(): void
    {
        $this->getJson('/students/99999/grades-report')->assertStatus(404);
    }

    public function test_can_get_grades_report(): void
    {
        $student = Student::factory()->create();
        $assignment = Assignment::factory()->create();
        AssignmentSubmission::factory()->create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'grade' => 15.5,
            'submitted_at' => now(),
        ]);

        $response = $this->getJson("/students/{$student->id}/grades-report");

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
        $this->assertSame(15.5, (float) $response->json('data.0.grade'));
        $this->assertEquals($assignment->title, $response->json('data.0.assignment'));
        $this->assertEquals($assignment->course->name, $response->json('data.0.course'));
    }
}