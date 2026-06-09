<?php

namespace App\Core\UserManagement\Database\Seeders;

use App\Core\Models\School;
use Database\Seeders\ModuleSeeder;
use Illuminate\Database\Seeder;
use App\Core\UserManagement\Models\{EmployeeType, Employee, Teacher, Student, ParentModel};
use App\Core\Models\User;
use Illuminate\Support\Facades\Hash;

class UserManagementSeeder extends Seeder implements ModuleSeeder
{
    public function run(): void
    {
        $school = School::factory()->create();
        $admin = User::factory()->create(['role' => 'administrator']);

        $tupe1 = EmployeeType::factory()->create([
            'code' => 'TEACHER',
            'type' => 'Enseignant',
            'description' => 'Professeur',
            'is_active' => true,
        ]);

        $type2 = EmployeeType::factory()->create([
            'code' => 'ADMIN',
            'type' => 'Administratif',
            'description' => 'Personnel administratif',
            'is_active' => true,
        ]);

        $user1 = User::factory()->create([
            'school_id' => $school->getKey(),
        ]);

        $teacher = Employee::factory()->create([
            'employee_type_id' => $type2->getKey(),
            'user_id' => $user1->id,
            'school_id' => $school->getKey(),
            'created_by' => $admin->getKey(),
        ]);

        Teacher::factory()->create(['employee_id' => $teacher->id]);

        $parentUser = User::factory()->create([
            'role' => 'parent',
            'status' => 'active',
            'school_id' => $school->getKey(),
        ]);

        $parent = ParentModel::factory()->create(['user_id' => $parentUser->id]);

        $studentUser = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
            'school_id' => $school->getKey(),
        ]);

        Student::factory()->create([
            'school_id' => $school->getKey(),
        ]);

        //$this->command->info('Employee types created');
        //$this->command->info('Teacher created');
        //$this->command->info('Parent created');
        //$this->command->info('Student created');
    }
}
