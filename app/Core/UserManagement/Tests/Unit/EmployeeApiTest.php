<?php

namespace App\Core\UserManagement\Tests\Unit;

use App\Core\Models\School;
use App\Core\UserManagement\Models\EmployeeType;
use Tests\TestControllerCase;
use App\Core\UserManagement\Models\Employee;

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
    }

    public function test_can_list_employees(): void
    {
        Employee::factory()->count(20)->create();
        $response = $this->getJson('/employees');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data', 'meta']);
    }

    public function test_can_terminate_employee(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->postJson("/employees/{$employee->id}/terminate");

        $response->assertStatus(200);
    }
}
