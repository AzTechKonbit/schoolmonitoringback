<?php

namespace Modules\Academic\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Academic\Models\{Course, SchoolClass, Schedule, Term};
use App\Core\UserManagement\Models\Teacher;

class AcademicSeeder extends Seeder
{
    public function run(): void
    {
        Term::create([
            'term' => 'Trimestre 1',
            'start_date' => '2025-09-01',
            'end_date' => '2025-12-15',
            'school_id' => 1,
        ]);

        Term::create([
            'term' => 'Trimestre 2',
            'start_date' => '2026-01-05',
            'end_date' => '2026-04-15',
            'school_id' => 1,
        ]);

        $class = SchoolClass::create(['name' => 'CP']);
        SchoolClass::create(['name' => 'CE1']);
        SchoolClass::create(['name' => 'CE2']);
        SchoolClass::create(['name' => 'CM1']);
        SchoolClass::create(['name' => 'CM2']);

        Course::create([
            'name' => 'Mathématiques',
            'description' => 'Mathématiques niveau primaire',
            'code' => 'MATH',
            'credit' => 4,
            'total_hours' => 120,
        ]);

        Course::create([
            'name' => 'Français',
            'description' => 'Français niveau primaire',
            'code' => 'FRA',
            'credit' => 4,
            'total_hours' => 120,
        ]);

        Course::create([
            'name' => 'Anglais',
            'description' => 'Anglais niveau primaire',
            'code' => 'ANG',
            'credit' => 3,
            'total_hours' => 90,
        ]);

        Course::create([
            'name' => 'Sciences',
            'description' => 'Sciences naturelles',
            'code' => 'SCI',
            'credit' => 3,
            'total_hours' => 90,
        ]);

        $teacher = Teacher::first();

        if ($teacher) {
            Schedule::create([
                'course_id' => 1,
                'class_id' => $class->id,
                'teacher_id' => $teacher->id,
                'day_of_week' => 'lundi',
                'room' => 'Salle A',
                'start_time' => '08:00',
                'end_time' => '09:00',
            ]);
        }
    }
}
