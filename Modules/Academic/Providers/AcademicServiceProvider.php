<?php

namespace Modules\Academic\Providers;

use App\Core\Models\School;
use App\Relations\RelationRegistry;
use App\Core\UserManagement\Models\{Employee, Student, Teacher};
use Modules\Academic\Models\{Attendance, Course, Schedule, SchoolClass};
use Nwidart\Modules\Support\ModuleServiceProvider;

class AcademicServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Academic';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'academic';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Define module schedules.
     *
     * @param $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }
    public function boot(): void
    {
        parent::boot();
        School::resolveRelationUsing('classes', function ($model) {
            return $model->hasMany(SchoolClass::class);
        });
        Teacher::resolveRelationUsing('courses', function ($model) {
            return $model->belongsToMany(Course::class, 'teachers_courses', 'teacher_id', 'course_id');
        });
        Teacher::resolveRelationUsing('schedules', function ($model) {
            return $model->hasMany(Schedule::class);
        });
        Student::resolveRelationUsing('classes', function ($model) {
            return $model->belongsToMany(SchoolClass::class, 'classes_students', 'student_id', 'class_id');
        });
        Student::resolveRelationUsing('attendances', function ($model) {
            return $model->hasMany(Attendance::class);
        });
        Employee::resolveRelationUsing('attendances', function ($model) {
            return $model->hasMany(Attendance::class, 'recorded_by', 'user_id');
        });
        //TODO optimiser mais pas tester
        RelationRegistry::register(Student::class, 'classes');

    }

}
