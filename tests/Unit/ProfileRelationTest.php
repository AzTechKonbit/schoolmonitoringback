<?php

namespace Tests\Unit;

use App\Core\Models\User;
use App\Core\UserManagement\Models\Employee;
use App\Core\UserManagement\Models\ParentModel;
use App\Core\UserManagement\Models\Student;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_lazy_load_profile_for_student(): void
    {
        $student = Student::factory()->create();

        $profile = $student->user->profile;

        $this->assertNotNull($profile);
        $this->assertInstanceOf(Student::class, $profile);
    }

    public function test_lazy_load_profile_for_employee(): void
    {
        $employee = Employee::factory()->create();

        $profile = $employee->user->profile;

        $this->assertNotNull($profile);
        $this->assertInstanceOf(Employee::class, $profile);
    }

    public function test_lazy_load_profile_for_parent(): void
    {
        $parent = ParentModel::factory()->create();

        $profile = $parent->primaryUser->profile;

        $this->assertNotNull($profile);
        $this->assertInstanceOf(ParentModel::class, $profile);
    }

    public function test_lazy_load_profile_returns_null_when_no_profile(): void
    {
        $user = User::factory()->create();

        $this->assertNull($user->profile);
    }

    public function test_eager_load_profile_via_with(): void
    {
        $student = Student::factory()->create();
        User::factory()->count(2)->create();

        $users = User::with('profile')->get();

        $loaded = $users->firstWhere('id', $student->user_id);
        $this->assertNotNull($loaded);
        $this->assertTrue($loaded->relationLoaded('profile'));
    }
}