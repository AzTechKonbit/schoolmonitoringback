<?php

namespace App\Core\UserManagement\Tests\Unit;

use App\Core\Models\School;
use App\Core\Models\User;
use Tests\TestControllerCase;

class UserApiTest extends TestControllerCase
{

    public function test_can_create_user(): void
    {
        $response = $this->postJson('/users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@school.com',
            'password' => 'password123',
            'phone' => '+2250700000000',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'john@school.com']);
    }

    public function test_can_list_users(): void
    {
        User::factory()->count(5)->create();

        $response = $this->getJson('/users');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data', 'meta']);
    }

    public function test_can_filter_users_by_role(): void
    {
        User::factory()->create(['role' => 'student', 'first_name' => 'RoleStudent']);

        $response = $this->getJson('/users?role=student');

        $response->assertStatus(200)
            ->assertJsonFragment(['first_name' => 'RoleStudent']);
    }

    public function test_can_filter_users_by_search(): void
    {
        User::factory()->create(['first_name' => 'Zaphod']);
        User::factory()->create(['first_name' => 'Marvin']);

        $response = $this->getJson('/users?search=Zaphod');

        $response->assertStatus(200)
            ->assertJsonFragment(['first_name' => 'Zaphod']);
    }

    public function test_can_show_user(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson("/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson(['data' => ['id' => $user->id]]);
    }

    public function test_show_non_existent_user_returns_404(): void
    {
        $this->getJson('/users/00000000-0000-0000-0000-000000000000')->assertStatus(404);
    }

    public function test_can_update_user(): void
    {
        $user = User::factory()->create();

        $response = $this->putJson("/users/{$user->id}", [
            'first_name' => 'Updated',
            'phone' => '+2250700000000',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'first_name' => 'Updated']);
    }

    public function test_can_update_user_password(): void
    {
        $user = User::factory()->create();

        $this->putJson("/users/{$user->id}", ['password' => 'newpassword123'])
            ->assertStatus(200);

        $this->assertNotEquals('newpassword123', $user->fresh()->password);
    }

    public function test_update_non_existent_user_returns_404(): void
    {
        $this->putJson('/users/00000000-0000-0000-0000-000000000000', ['first_name' => 'x'])
            ->assertStatus(404);
    }

    public function test_can_destroy_user(): void
    {
        $user = User::factory()->create();

        $this->deleteJson("/users/{$user->id}")->assertStatus(200);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_destroy_non_existent_user_returns_404(): void
    {
        $this->deleteJson('/users/00000000-0000-0000-0000-000000000000')->assertStatus(404);
    }

    public function test_create_user_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'taken@school.com']);

        $this->postJson('/users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'taken@school.com',
            'password' => 'password123',
        ])->assertStatus(422);
    }
}