<?php

namespace Modules\Academic\Database\Factories;

use App\Core\UserManagement\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academic\Models\Assignment;
use Modules\Academic\Models\AssignmentSubmission;

class AssignmentSubmissionFactory extends Factory
{
    protected $model = AssignmentSubmission::class;

    public function definition(): array
    {
        return [
            'assignment_id' => Assignment::factory(),
            'student_id' => Student::factory(),
            'grade' => rand(10, 20),
        ];
    }
}