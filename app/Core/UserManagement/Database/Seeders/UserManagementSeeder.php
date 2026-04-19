<?php

namespace App\Core\UserManagement\Database\Seeders;

use Database\Seeders\ModuleSeeder;
use Illuminate\Database\Seeder;
use App\Core\UserManagement\Models\{EmployeeType, Employee, Teacher, Student, ParentModel};
use App\Core\Models\User;
use Illuminate\Support\Facades\Hash;

class UserManagementSeeder extends Seeder implements ModuleSeeder
{
    public function run(): void
    {
        EmployeeType::create([
            'code' => 'TEACHER',
            'type' => 'Enseignant',
            'description' => 'Professeur',
            'is_active' => true,
        ]);

        EmployeeType::create([
            'code' => 'ADMIN',
            'type' => 'Administratif',
            'description' => 'Personnel administratif',
            'is_active' => true,
        ]);

        $user1 = User::create([
            'first_name' => 'Jean',
            'last_name' => 'Koffi',
            'email' => 'jean.koffi@school.com',
            'password' => Hash::make('password123'),
            'phone' => '+2250700111222',
            'role' => 'employee',
            'status' => 'active',
            'school_id' => 1,
        ]);

        $teacher = Employee::create([
            'employee_code' => 'EMP-000001',
            'employment_status' => 'active',
            'hire_date' => '2020-01-15',
            'employment_contract_type' => 'full-time',
            'salary_type' => 'monthly',
            'base_salary' => 500000,
            'employee_type_id' => 1,
            'user_id' => $user1->id,
            'school_id' => 1,
            'created_by' => 1,
        ]);

        Teacher::create(['employee_id' => $teacher->id]);

        $parentUser = User::create([
            'first_name' => 'Marie',
            'last_name' => 'Dupont',
            'email' => 'marie.dupont@school.com',
            'password' => Hash::make('password123'),
            'phone' => '+2250700333444',
            'role' => 'parent',
            'status' => 'active',
            'school_id' => 1,
        ]);

        $parent = ParentModel::create(['user_id' => $parentUser->id]);

        $studentUser = User::create([
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'pierre.dupont@school.com',
            'password' => Hash::make('password123'),
            'phone' => '+2250700555666',
            'role' => 'student',
            'status' => 'active',
            'school_id' => 1,
        ]);

        Student::create([
            'dob' => '2015-05-20',
            'user_id' => $studentUser->id,
            'parent_id' => $parent->id,
            'student_number_id' => 'STU-2026-000001',
            'school_id' => 1,
        ]);

        //$this->command->info('Employee types created');
        //$this->command->info('Teacher created');
        //$this->command->info('Parent created');
        //$this->command->info('Student created');
    }
}
