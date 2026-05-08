<?php

namespace App\Core\Tests\Unit;

use App\Core\Models\School;
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
}
