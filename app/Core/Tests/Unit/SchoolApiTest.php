<?php

namespace App\Core\Tests\Unit;

use App\Core\Models\School;
use App\Core\Models\User;
use App\Core\UserManagement\Models\Student;
use Tests\TestControllerCase;

class SchoolApiTest extends TestControllerCase
{

    public function test_can_create_school(): void
    {
        $response = $this->postJson('/schools', [
            'name' => 'Test School',
            'type' => 'primaire',
            'default_language' => 'fr',
            'academic_year' => '2025-2026',
            'status' => 'active',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'message', 'data']);

        $this->assertDatabaseHas('schools', ['name' => 'Test School']);
    }

    public function test_can_list_schools(): void
    {
        School::factory()->count(3)->create();

        $response = $this->getJson('/schools');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_can_get_school_statistics(): void
    {
        $school = School::create([
            'name' => 'Test School',
            'type' => 'primaire',
        ]);

        $response = $this->getJson("/schools/{$school->id}/statistics");

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_can_update_school_statistics(): void
    {
        $school = School::create([
            'name' => 'Test School',
            'type' => 'primaire',
        ]);


        $response = $this->putJson("/schools/{$school->id}",
        ['name' => 'Test School modified',
            'type' => 'secondaire',
            'default_language' => 'fr',
            'academic_year' => '2025-2026',
            'status' => 'active',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_can_show_school(): void
    {
        $school = School::factory()->create();

        $response = $this->getJson("/schools/{$school->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data'])
            ->assertJson(['data' => ['id' => $school->id]]);
    }

    public function test_show_non_existent_school_returns_404(): void
    {
        $this->getJson('/schools/99999')->assertStatus(404);
    }

    public function test_can_destroy_school(): void
    {
        $school = School::factory()->create();

        $response = $this->deleteJson("/schools/{$school->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('schools', ['id' => $school->id]);
    }

    public function test_destroy_non_existent_school_returns_404(): void
    {
        $this->deleteJson('/schools/99999')->assertStatus(404);
    }

    public function test_update_non_existent_school_returns_404(): void
    {
        $this->putJson('/schools/99999', ['name' => 'x'])->assertStatus(404);
    }

    public function test_statistics_for_non_existent_school_returns_404(): void
    {
        $this->getJson('/schools/99999/statistics')->assertStatus(404);
    }

    public function test_statistics_counts_include_student_and_employee_counts(): void
    {
        $school = School::factory()->create();

        Student::factory()
            ->count(2)
            ->create(['school_id' => $school->id]);

        $response = $this->getJson("/schools/{$school->id}/statistics");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['students_count', 'students_f_count', 'students_m_count', 'students_o_count', 'employees_count'],
            ]);
    }

    public function test_store_requires_a_name(): void
    {
        $this->postJson('/schools', [])->assertStatus(422);
    }
}
