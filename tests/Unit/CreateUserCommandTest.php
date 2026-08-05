<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateUserCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_creates_employee_user(): void
    {
        $this->artisan('user:create', [
            '--name' => 'Jean',
            '--email' => 'j@test.com',
            '--password' => 'password123',
        ])->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'j@test.com',
            'first_name' => 'Jean',
            'role' => 'employee',
        ]);
    }

    public function test_command_creates_admin_user_with_flag(): void
    {
        $this->artisan('user:create', [
            '--name' => 'Admin',
            '--email' => 'admin@test.com',
            '--password' => 'password123',
            '--admin' => true,
        ])->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@test.com',
            'role' => 'administrator',
        ]);
    }

    public function test_command_fails_on_invalid_email(): void
    {
        $this->artisan('user:create', [
            '--name' => 'Jean',
            '--email' => 'not-an-email',
            '--password' => 'password123',
        ])->assertFailed();
    }

    public function test_command_fails_on_duplicate_email(): void
    {
        $this->artisan('user:create', [
            '--name' => 'Jean',
            '--email' => 'dup@test.com',
            '--password' => 'password123',
        ])->assertSuccessful();

        $this->artisan('user:create', [
            '--name' => 'Autre',
            '--email' => 'dup@test.com',
            '--password' => 'password123',
        ])->assertFailed();
    }
}