<?php

namespace Tests\Unit;

use App\Core\Authorization\Database\Seeders\AuthorizationSeeder;
use App\Core\Database\Seeders\CoreSeeder;
use App\Core\UserManagement\Database\Seeders\UserManagementSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModuleProviderBindingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorization_seeder_is_resolvable_from_container(): void
    {
        $seeder = app(AuthorizationSeeder::class);

        $this->assertInstanceOf(AuthorizationSeeder::class, $seeder);
    }

    public function test_user_management_seeder_is_resolvable_from_container(): void
    {
        $seeder = app(UserManagementSeeder::class);

        $this->assertInstanceOf(UserManagementSeeder::class, $seeder);
    }

    public function test_core_seeder_is_resolvable_and_runs(): void
    {
        $seeder = app(CoreSeeder::class);

        $this->assertInstanceOf(CoreSeeder::class, $seeder);
        $seeder->run();

        $this->assertDatabaseHas('schools', ['name' => 'École Primaire Saint-Jean']);
        $this->assertDatabaseHas('users', ['email' => 'admin@school.com']);
    }

    public function test_authorization_seeder_runs(): void
    {
        $seeder = app(AuthorizationSeeder::class);
        $seeder->run();

        $this->assertDatabaseHas('rights', ['code' => 'SUPER_ADMIN']);
        $this->assertDatabaseHas('rights', ['code' => 'TAKE_ATTENDANCE']);
        $this->assertDatabaseHas('titles', ['code' => 'DIR']);
    }

    public function test_user_management_seeder_runs(): void
    {
        $seeder = app(UserManagementSeeder::class);
        $seeder->run();

        $this->assertDatabaseHas('employee_types', ['code' => 'TEACHER']);
        $this->assertDatabaseHas('employee_types', ['code' => 'ADMIN']);
        $this->assertGreaterThan(0, \App\Core\UserManagement\Models\Employee::count());
        $this->assertGreaterThan(0, \App\Core\UserManagement\Models\Student::count());
    }
}