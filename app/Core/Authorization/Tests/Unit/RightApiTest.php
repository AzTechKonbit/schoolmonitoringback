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

    public function test_can_search_rights(): void
    {
        Right::factory()->create(['code' => 'SUPER_SEARCH']);
        Right::factory()->count(3)->create();

        $response = $this->getJson('/rights?search=SUPER_SEARCH');

        $response->assertStatus(200)
            ->assertJsonFragment(['code' => 'SUPER_SEARCH']);
    }

    public function test_can_update_right(): void
    {
        $right = Right::factory()->create(['code' => 'OLD_CODE']);

        $response = $this->putJson("/rights/{$right->id}", [
            'code' => 'NEW_CODE',
            'description' => 'Updated',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('rights', ['id' => $right->id, 'code' => 'NEW_CODE']);
    }

    public function test_update_non_existent_right_returns_404(): void
    {
        $this->putJson('/rights/99999', ['code' => 'X'])->assertStatus(404);
    }

    public function test_can_destroy_right(): void
    {
        $right = Right::factory()->create();

        $this->deleteJson("/rights/{$right->id}")->assertStatus(200);

        $this->assertDatabaseMissing('rights', ['id' => $right->id]);
    }

    public function test_destroy_non_existent_right_returns_404(): void
    {
        $this->deleteJson('/rights/99999')->assertStatus(404);
    }

    public function test_create_right_requires_unique_code(): void
    {
        Right::factory()->create(['code' => 'DUP_CODE']);

        $this->postJson('/rights', ['code' => 'DUP_CODE'])->assertStatus(422);
    }

    public function test_can_assign_right_to_user(): void
    {
        $user = User::factory()->create();
        $right = Right::factory()->create();

        $response = $this->postJson("/users/{$user->id}/rights", [
            'right_id' => $right->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('user_rights', ['user_id' => $user->id, 'right_id' => $right->id]);
    }

    public function test_can_remove_right_from_user(): void
    {
        $user = User::factory()->create();
        $right = Right::factory()->create();
        $user->rights()->attach($right->id);

        $response = $this->deleteJson("/users/{$user->id}/rights", [
            'right_id' => $right->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('user_rights', ['user_id' => $user->id, 'right_id' => $right->id]);
    }

    public function test_can_sync_user_rights(): void
    {
        $user = User::factory()->create();
        $right1 = Right::factory()->create();
        $right2 = Right::factory()->create();

        $response = $this->putJson("/users/{$user->id}/rights", [
            'right_ids' => [$right1->id, $right2->id],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('user_rights', ['user_id' => $user->id, 'right_id' => $right1->id]);
        $this->assertDatabaseHas('user_rights', ['user_id' => $user->id, 'right_id' => $right2->id]);
    }

    public function test_assign_right_to_non_existent_user_returns_404(): void
    {
        $this->postJson('/users/00000000-0000-0000-0000-000000000000/rights', ['right_id' => 1])
            ->assertStatus(404);
    }

    public function test_remove_right_from_non_existent_user_returns_404(): void
    {
        $this->deleteJson('/users/00000000-0000-0000-0000-000000000000/rights', ['right_id' => 1])
            ->assertStatus(404);
    }

    public function test_sync_rights_for_non_existent_user_returns_404(): void
    {
        $this->putJson('/users/00000000-0000-0000-0000-000000000000/rights', ['right_ids' => [1]])
            ->assertStatus(404);
    }
}
