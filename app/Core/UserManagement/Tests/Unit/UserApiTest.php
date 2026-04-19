<?php

namespace App\Core\UserManagement\Tests\Unit;

use Tests\TestCase;
use App\Core\UserManagement\Models\{Employee, Student};
use App\Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_user(): void
    {
        $response = $this->postJson('/api/users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@school.com',
            'password' => 'password123',
            'phone' => '+2250700000000',
            'school_id' => 1,
        ]);

        $response->assertStatus(201);
    }

    public function test_can_list_users(): void
    {
        User::factory()->count(5)->create(['school_id' => 1]);

        $response = $this->getJson('/api/users');

        $response->assertStatus(200);
    }
}
