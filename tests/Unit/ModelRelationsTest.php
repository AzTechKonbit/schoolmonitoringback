<?php

namespace Tests\Unit;

use App\Core\Authorization\Models\Right;
use App\Core\Authorization\Models\Title;
use App\Core\Models\School;
use App\Core\Models\User;
use App\Core\UserManagement\Models\Employee;
use App\Core\UserManagement\Models\EmployeeType;
use App\Core\UserManagement\Models\ParentModel;
use App\Core\UserManagement\Models\Student;
use App\Core\UserManagement\Models\Teacher;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Academic\Models\Attendance;
use Modules\Academic\Models\Assignment;
use Modules\Academic\Models\AssignmentSubmission;
use Modules\Academic\Models\Course;
use Modules\Academic\Models\Schedule;
use Modules\Academic\Models\SchoolClass;
use Modules\Academic\Models\Term;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_relations(): void
    {
        $school = School::factory()->create();

        $this->assertInstanceOf(HasMany::class, $school->users());
        $this->assertInstanceOf(HasMany::class, $school->students());
        $this->assertInstanceOf(HasMany::class, $school->employees());
    }

    public function test_user_relations_and_accessors(): void
    {
        $user = User::factory()->create(['first_name' => 'Jean', 'last_name' => 'Kouassi']);

        $this->assertInstanceOf(BelongsTo::class, $user->school());
        $this->assertInstanceOf(BelongsToMany::class, $user->rights());
        $this->assertEquals('Jean Kouassi', $user->fullName);
    }

    public function test_user_is_admin_and_has_right(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $right = Right::factory()->create(['code' => 'SUPER_ADMIN']);

        $this->assertFalse($user->isAdmin());

        $user->rights()->attach($right->id);

        $this->assertTrue($user->hasRight('SUPER_ADMIN'));
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->hasRight('OTHER'));
    }

    public function test_employee_relations(): void
    {
        $employee = Employee::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $employee->user());
        $this->assertInstanceOf(BelongsTo::class, $employee->employeeType());
        $this->assertInstanceOf(BelongsTo::class, $employee->school());
        $this->assertInstanceOf(BelongsToMany::class, $employee->titles());
        $this->assertFalse($employee->isTeacher());
    }

    public function test_employee_type_relations(): void
    {
        $type = EmployeeType::factory()->create();

        $this->assertInstanceOf(HasMany::class, $type->employees());
    }

    public function test_parent_model_relations(): void
    {
        $user = User::factory()->create();
        $parent = ParentModel::create(['user_id' => $user->id]);

        $this->assertInstanceOf(BelongsTo::class, $parent->primaryUser());
        $this->assertInstanceOf(BelongsTo::class, $parent->secondaryUser());
        $this->assertInstanceOf(HasMany::class, $parent->students());
    }

    public function test_student_relations(): void
    {
        $student = Student::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $student->user());
        $this->assertInstanceOf(BelongsTo::class, $student->parent());
        $this->assertInstanceOf(BelongsTo::class, $student->school());
    }

    public function test_teacher_relation(): void
    {
        $teacher = \App\Core\UserManagement\Models\Teacher::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $teacher->employee());
    }

    public function test_course_relations(): void
    {
        $course = Course::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $course->teachers());
        $this->assertInstanceOf(HasMany::class, $course->schedules());
        $this->assertInstanceOf(BelongsToMany::class, $course->classes());
    }

    public function test_schedule_relations(): void
    {
        $schedule = Schedule::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $schedule->course());
        $this->assertInstanceOf(BelongsTo::class, $schedule->schoolClass());
        $this->assertInstanceOf(BelongsTo::class, $schedule->teacher());
        $this->assertInstanceOf(HasMany::class, $schedule->attendances());
    }

    public function test_school_class_relations(): void
    {
        $class = SchoolClass::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $class->students());
        $this->assertInstanceOf(BelongsToMany::class, $class->courses());
        $this->assertInstanceOf(BelongsToMany::class, $class->school());
        $this->assertInstanceOf(HasMany::class, $class->schedules());
    }

    public function test_attendance_relations(): void
    {
        $attendance = Attendance::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $attendance->student());
        $this->assertInstanceOf(BelongsTo::class, $attendance->schedule());
        $this->assertInstanceOf(BelongsTo::class, $attendance->recorder());
    }

    public function test_title_relations(): void
    {
        $title = Title::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $title->employees());
    }

    public function test_terms_is_current(): void
    {
        $current = Term::create([
            'term' => 'T1',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
        ]);

        $past = Term::create([
            'term' => 'T0',
            'start_date' => now()->subMonth(2),
            'end_date' => now()->subMonth(1),
        ]);

        $this->assertTrue($current->isCurrent());
        $this->assertFalse($past->isCurrent());
    }

    public function test_term_factory_current_uses_date_range(): void
    {
        $term = Term::factory()->create();

        $this->assertNotNull($term->start_date);
        $this->assertNotNull($term->end_date);
        $this->assertTrue($term->isCurrent());
    }

    public function test_assignment_and_submission_relations(): void
    {
        $assignment = Assignment::factory()->create();
        $submission = AssignmentSubmission::factory()->create(['assignment_id' => $assignment->id]);

        $this->assertEquals($assignment->id, $submission->assignment->id);
        $this->assertEquals($assignment->course_id, $assignment->course->id);
        $this->assertTrue($assignment->course->assignments()->where('id', $assignment->id)->exists());
        $this->assertTrue($submission->student->submissions()->where('id', $submission->id)->exists());
        $this->assertTrue($assignment->submissions()->where('id', $submission->id)->exists());
    }
}