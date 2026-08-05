<?php

namespace Tests\Unit;

use App\BaseBuilder;
use App\Core\Models\School;
use App\Core\Models\User;
use App\Core\UserManagement\Models\Student;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelHelpersTest extends TestCase
{
    use RefreshDatabase;

    public function test_scope_with_existing_filters_relations(): void
    {
        $student = Student::factory()->create();

        $query = Student::withExisting(['user', 'parent', 'school', 'ghostRelation'])->where('id', $student->id);

        $this->assertInstanceOf(BaseBuilder::class, $query);

        $model = $query->first();

        $this->assertTrue($model->relationLoaded('user'));
        $this->assertTrue($model->relationLoaded('parent'));
        $this->assertFalse($model->relationLoaded('ghostRelation'));
    }

    public function test_scope_with_existing_with_associative_relations(): void
    {
        $student = Student::factory()->create();

        $model = Student::withExisting(['user' => fn ($q) => $q->select('id', 'first_name')])
            ->where('id', $student->id)
            ->first();

        $this->assertTrue($model->relationLoaded('user'));
    }

    public function test_new_eloquent_builder_returns_base_builder(): void
    {
        $model = new Student;
        $this->assertInstanceOf(BaseBuilder::class, $model->newEloquentBuilder(Student::query()->getQuery()));
    }

    public function test_school_scope_with_existing(): void
    {
        $school = School::factory()->create();

        $model = School::withExisting(['users', 'students', 'employees', 'ghost'])->find($school->id);

        $this->assertTrue($model->relationLoaded('users'));
        $this->assertFalse($model->relationLoaded('ghost'));
    }

    public function test_scope_with_existing_called_directly(): void
    {
        $student = Student::factory()->create();

        $model = (new Student)->scopeWithExisting(
            Student::query(),
            ['user', 'parent', 'school']
        )->where('id', $student->id)->first();

        $this->assertTrue($model->relationLoaded('user'));
        $this->assertTrue($model->relationLoaded('parent'));
    }
}