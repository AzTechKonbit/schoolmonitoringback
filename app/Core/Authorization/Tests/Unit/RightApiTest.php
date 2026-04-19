<?php

namespace App\Core\Authorization\Tests\Unit;

use Tests\TestCase;
use App\Core\Authorization\Models\{Right, Title};
use App\Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RightApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_right(): void
    {
        $response = $this->postJson('/api/rights', [
            'code' => 'TEST_RIGHT',
            'description' => 'Droit de test',
        ]);

        $response->assertStatus(201);
    }

    public function test_can_list_rights(): void
    {
        Right::factory()->count(5)->create();

        $response = $this->getJson('/api/rights');

        $response->assertStatus(200);
    }

    public function test_can_assign_right_to_user(): void
    {
        $user = User::factory()->create();
        $right = Right::factory()->create();

        $response = $this->postJson("/api/users/{$user->id}/rights", [
            'right_id' => $right->id,
        ]);

        $response->assertStatus(200);
    }
}
