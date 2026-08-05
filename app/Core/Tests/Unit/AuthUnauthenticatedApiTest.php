<?php

namespace App\Core\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthUnauthenticatedApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_routes_require_authentication(): void
    {
        $this->getJson('/auth/profile')->assertStatus(401);
        $this->postJson('/auth/logout')->assertStatus(401);
        $this->postJson('/auth/refresh')->assertStatus(401);
        $this->getJson('/schools')->assertStatus(401);
        $this->getJson('/users')->assertStatus(401);
        $this->getJson('/academic/courses')->assertStatus(401);
    }
}