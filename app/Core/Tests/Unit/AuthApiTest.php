<?php

namespace App\Core\Tests\Unit;

use App\Core\Models\School;
use App\Core\Models\User;
use Tests\TestControllerCase;

class AuthApiTest extends TestControllerCase
{
    public function test_can_register(): void
    {
        $response = $this->postJson('/auth/register', [
            'first_name' => 'Alice',
            'last_name' => 'Martin',
            'email' => 'alice@school.com',
            'password' => 'password123',
            'phone' => '+2250700000000',
            'gender' => 'F',
            'role' => 'employee',
            'school_id' => School::factory()->create()->getKey(),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'message', 'data' => ['user', 'token']]);

        $this->assertDatabaseHas('users', ['email' => 'alice@school.com']);
    }

    public function test_register_validates_unique_email(): void
    {
        User::factory()->create(['email' => 'duplicate@school.com']);

        $this->postJson('/auth/register', [
            'first_name' => 'Bob',
            'last_name' => 'Doe',
            'email' => 'duplicate@school.com',
            'password' => 'password123',
        ])->assertStatus(422);
    }

    public function test_register_validates_password_minimum(): void
    {
        $this->postJson('/auth/register', [
            'first_name' => 'Bob',
            'last_name' => 'Doe',
            'email' => 'bob@school.com',
            'password' => 'short',
        ])->assertStatus(422);
    }

    public function test_can_login(): void
    {
        User::factory()->create([
            'email' => 'login@school.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/auth/login', [
            'email' => 'login@school.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data' => ['user', 'token']]);
    }

    public function test_login_with_invalid_credentials_returns_401(): void
    {
        $response = $this->postJson('/auth/login', [
            'email' => 'nobody@school.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->postJson('/auth/login', [])->assertStatus(422);
    }

    public function test_can_logout(): void
    {
        $this->postJson('/auth/logout')->assertStatus(200);
    }

    public function test_can_get_profile(): void
    {
        $response = $this->getJson('/auth/profile');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data' => ['user']]);
    }

    public function test_can_refresh_token(): void
    {
        $response = $this->postJson('/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data' => ['token']]);
    }
}