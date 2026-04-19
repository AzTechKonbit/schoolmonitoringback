<?php

namespace App\Core\UserManagement\Tests\Unit;

use Tests\TestCase;
use App\Core\UserManagement\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_employee(): void
    {
        $response = $this->postJson('/api/employees', [
            'first_name' => 'Jean',
            'last_name' => 'Koffi',
            'email' => 'jean.koffi@test.com',
            'password' => 'password123',
            'employee_type_id' => 1,
            'school_id' => 1,
            'employment_status' => 'active',
            'contract_type' => 'full-time',
            'salary_type' => 'monthly',
            'base_salary' => 500000,
        ]);

        $response->assertStatus(201);
    }

    public function test_can_list_employees(): void
    {
        $response = $this->getJson('/api/employees');

        $response->assertStatus(200);
    }

    public function test_can_terminate_employee(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->postJson("/api/employees/{$employee->id}/terminate");

        $response->assertStatus(200);
    }
}
