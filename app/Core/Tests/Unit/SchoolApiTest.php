<?php

namespace App\Core\Tests\Unit;

use Tests\TestCase;
use App\Core\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SchoolApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_school(): void
    {
        $response = $this->postJson('/api/schools', [
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

        $response = $this->getJson('/api/schools');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_can_get_school_statistics(): void
    {
        $school = School::create([
            'name' => 'Test School',
            'type' => 'primaire',
        ]);

        $response = $this->getJson("/api/schools/{$school->id}/statistics");

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }
}
