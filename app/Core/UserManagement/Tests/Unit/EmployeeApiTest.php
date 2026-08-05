<?php

namespace App\Core\UserManagement\Tests\Unit;

use App\Core\Models\School;
use App\Core\Models\User;
use App\Core\UserManagement\Models\Employee;
use App\Core\UserManagement\Models\EmployeeType;
use Modules\Academic\Models\Course;
use Tests\TestControllerCase;

class EmployeeApiTest extends TestControllerCase
{

    public function test_can_create_employee(): void
    {
        $response = $this->postJson('/employees', [
            'first_name' => 'Jean',
            'last_name' => 'Koffi',
            'email' => 'jean.koffi@test.com',
            'password' => 'password123',
            'employee_type_id' => EmployeeType::factory()->create()->getKey(),
            'school_id' => School::factory()->create()->getKey(),
            'employment_status' => 'active',
            'contract_type' => 'full-time',
            'salary_type' => 'monthly',
            'base_salary' => 500000,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('employees', ['employee_code' => 'EMP-000001']);
        $this->assertDatabaseHas('users', ['email' => 'jean.koffi@test.com']);
    }

    public function test_can_create_employee_as_teacher(): void
    {
        $response = $this->postJson('/employees', [
            'first_name' => 'Mme',
            'last_name' => 'Professeur',
            'email' => 'teacher@test.com',
            'password' => 'password123',
            'employee_type_id' => EmployeeType::factory()->create()->getKey(),
            'school_id' => School::factory()->create()->getKey(),
            'is_teacher' => true,
        ]);

        $response->assertStatus(201);
        $employee = Employee::whereHas('user', fn ($q) => $q->where('email', 'teacher@test.com'))->first();
        $this->assertNotNull($employee);
        $this->assertTrue($employee->isTeacher());
    }

    public function test_can_list_employees(): void
    {
        Employee::factory()->count(20)->create();
        $response = $this->getJson('/employees');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data', 'meta']);
    }

    public function test_can_filter_employees_by_school(): void
    {
        $school = School::factory()->create();
        Employee::factory()->count(2)->create(['school_id' => $school->id]);
        Employee::factory()->count(3)->create();

        $response = $this->getJson("/employees?school_id={$school->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_show_employee(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->getJson("/employees/{$employee->id}");

        $response->assertStatus(200)
            ->assertJson(['data' => ['id' => $employee->id]]);
    }

    public function test_show_non_existent_employee_returns_404(): void
    {
        $this->getJson('/employees/99999')->assertStatus(404);
    }

    public function test_can_update_employee(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->putJson("/employees/{$employee->id}", [
            'base_salary' => 600000,
            'employment_status' => 'on_leave',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'base_salary' => 600000]);
    }

    public function test_can_update_employee_user_fields(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->putJson("/employees/{$employee->id}", [
            'first_name' => 'NouveauNom',
            'national_id' => 'NID123',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $employee->user_id, 'first_name' => 'NouveauNom']);
    }

    public function test_update_non_existent_employee_returns_404(): void
    {
        $this->putJson('/employees/99999', ['base_salary' => 100])->assertStatus(404);
    }

    public function test_can_terminate_employee(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->postJson("/employees/{$employee->id}/terminate");

        $response->assertStatus(200);
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'employment_status' => 'terminated',
        ]);
        $this->assertNotNull($employee->fresh()->termination_date);
    }

    public function test_terminate_non_existent_employee_returns_404(): void
    {
        $this->postJson('/employees/99999/terminate')->assertStatus(404);
    }

    public function test_can_destroy_employee(): void
    {
        $employee = Employee::factory()->create();

        $this->deleteJson("/employees/{$employee->id}")->assertStatus(200);

        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
    }

    public function test_destroy_non_existent_employee_returns_404(): void
    {
        $this->deleteJson('/employees/99999')->assertStatus(404);
    }

    public function test_create_employee_requires_email(): void
    {
        $this->postJson('/employees', [
            'first_name' => 'Jean',
            'last_name' => 'Koffi',
            'employee_type_id' => EmployeeType::factory()->create()->getKey(),
            'school_id' => School::factory()->create()->getKey(),
        ])->assertStatus(422);
    }

    public function test_assign_course_to_non_existent_employee_returns_404(): void
    {
        $this->postJson('/employees/99999/courses', ['course_id' => 1])->assertStatus(404);
    }

    public function test_remove_course_from_non_existent_employee_returns_404(): void
    {
        $this->deleteJson('/employees/99999/courses', ['course_id' => 1])->assertStatus(404);
    }

    public function test_can_assign_course_to_employee(): void
    {
        $employee = Employee::factory()->create();
        $course = Course::factory()->create();

        $response = $this->postJson("/employees/{$employee->id}/courses", [
            'course_id' => $course->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('employees_courses', [
            'employee_id' => $employee->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_can_remove_course_from_employee(): void
    {
        $employee = Employee::factory()->create();
        $course = Course::factory()->create();
        $employee->courses()->attach($course->id);

        $response = $this->deleteJson("/employees/{$employee->id}/courses", [
            'course_id' => $course->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('employees_courses', [
            'employee_id' => $employee->id,
            'course_id' => $course->id,
        ]);
    }
}