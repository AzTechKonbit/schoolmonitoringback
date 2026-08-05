<?php

namespace Tests\Unit;

use App\Core\Models\User;
use App\Core\UserManagement\Models\Employee;
use App\Core\UserManagement\Models\Teacher;
use App\Core\UserManagement\Services\EmployeeService;
use App\Core\UserManagement\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Modules\Academic\Models\Course;
use Modules\Academic\Models\Schedule;
use Modules\Academic\Models\SchoolClass;
use Modules\Academic\Services\ScheduleService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServicesBranchesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_service_attach_role(): void
    {
        $user = User::factory()->create(['role' => 'employee']);

        $result = app(UserService::class)->attachRole($user, 'student');

        $this->assertEquals('student', $result->role->value);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 'student']);
    }

    public function test_user_service_update_with_password(): void
    {
        $user = User::factory()->create();

        $updated = app(UserService::class)->update($user, [
            'first_name' => 'Nouveau',
            'password' => 'newpassword123',
        ]);

        $this->assertEquals('Nouveau', $updated->first_name);
        $this->assertTrue(Hash::check('newpassword123', $updated->password));
    }

    public function test_user_service_delete(): void
    {
        $user = User::factory()->create();

        $this->assertTrue(app(UserService::class)->delete($user));
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_user_service_get_all_with_filters(): void
    {
        $school = \App\Core\Models\School::factory()->create();
        $user = User::factory()->create([
            'first_name' => 'UniqueName',
            'school_id' => $school->id,
        ]);

        $result = app(UserService::class)->getAll([
            'role' => $user->role->value,
            'school_id' => $user->school_id,
            'search' => 'UniqueName',
        ], 15);

        $this->assertGreaterThanOrEqual(1, $result->total());
    }

    public function test_employee_service_get_all_with_all_filters(): void
    {
        $teacher = Employee::factory()->create(['employment_status' => 'active']);
        \App\Core\UserManagement\Models\Teacher::create(['employee_id' => $teacher->id]);
        $other = Employee::factory()->create(['employment_status' => 'on_leave']);

        $result = app(EmployeeService::class)->getAll([
            'school_id' => $teacher->school_id,
            'employee_type_id' => $teacher->employee_type_id,
            'employment_status' => 'active',
            'is_teacher' => true,
        ], 15);

        $this->assertGreaterThanOrEqual(1, $result->total());
        $ids = collect($result->items())->pluck('id')->all();
        $this->assertContains($teacher->id, $ids);
        $this->assertNotContains($other->id, $ids);
    }

    public function test_employee_service_update_with_defaults(): void
    {
        $employee = Employee::factory()->create();

        $updated = app(EmployeeService::class)->update($employee, [
            'base_salary' => 700000,
        ]);

        $this->assertEquals(700000, (float) $updated->base_salary);
    }

    public function test_employee_service_generate_code_incrementally(): void
    {
        $employee = Employee::factory()->create();

        $this->assertStringStartsWith('EMP-', $employee->employee_code);
    }

    public function test_employee_service_find_by_id(): void
    {
        $employee = Employee::factory()->create();

        $found = app(EmployeeService::class)->findById($employee->id);

        $this->assertNotNull($found);
    }

    public function test_schedule_service_get_all_with_all_filters(): void
    {
        $schedule = Schedule::factory()->create(['day_of_week' => 'lundi']);

        $result = app(ScheduleService::class)->getAll([
            'course_id' => $schedule->course_id,
            'class_id' => $schedule->class_id,
            'teacher_id' => $schedule->teacher_id,
            'day_of_week' => 'lundi',
        ], 15);

        $this->assertGreaterThanOrEqual(1, $result->total());
    }

    public function test_schedule_service_update_and_delete(): void
    {
        $schedule = Schedule::factory()->create();

        $service = app(ScheduleService::class);
        $updated = $service->update($schedule, ['room' => 'Salle 42']);

        $this->assertEquals('Salle 42', $updated->room);

        $this->assertTrue($service->delete($schedule));
        $this->assertDatabaseMissing('schedules', ['id' => $schedule->id]);
    }
}