<?php

namespace App\Core\Authorization\Tests\Unit;

use App\Core\Authorization\Models\{Right};
use App\Core\Models\User;
use Tests\TestControllerCase;

class RightApiTest extends TestControllerCase
{

    public function test_can_create_right(): void
    {
        $response = $this->postJson('/rights', [
            'code' => 'TEST_RIGHT',
            'description' => 'Droit de test',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('rights', ['code' => 'TEST_RIGHT']);
    }

    public function test_can_list_rights(): void
    {
        Right::factory()->count(5)->create();

        $response = $this->getJson('/rights');

        $response->assertStatus(200);
    }

    public function test_can_assign_right_to_user(): void
    {
        $user = User::factory()->create();
        $right = Right::factory()->create();

        $response = $this->postJson("/users/{$user->id}/rights", [
            'right_id' => $right->id,
        ]);

        $response->assertStatus(200);
    }
}
