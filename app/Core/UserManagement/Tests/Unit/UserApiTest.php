<?php

namespace App\Core\UserManagement\Tests\Unit;

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
    }

    public function test_can_list_users(): void
    {
        User::factory()->count(5)->create();

        $response = $this->getJson('/users');

        $response->assertStatus(200);
    }
}
