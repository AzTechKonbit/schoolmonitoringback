<?php

namespace Modules\Academic\Models;

use App\Core\UserManagement\Models\Teacher;
use App\Model;
use Illuminate\Database\Eloquent\Relations\{HasMany,BelongsToMany};
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'code',
        'credit',
        'total_hours',
        'hours_td',
        'hours_tp',
    ];

    protected $casts = [
        'total_hours' => 'integer',
        'hours_td' => 'integer',
        'hours_tp' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teachers_courses', 'course_id', 'teacher_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
//TODO To add to provider other modules

//    public function assignments(): HasMany
//    {
//        return $this->hasMany(Assignment::class);
//    }
//
//    public function documents(): HasMany
//    {
//        return $this->hasMany(Document::class);
//    }
//
//    public function programs(): BelongsToMany
//    {
//        return $this->belongsToMany(Program::class, 'program_courses', 'idcourses', 'idprogram');
//    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'classes_courses', 'course_id', 'classe_id');
    }
    protected static function newFactory()
    {
        return \Modules\Academic\Database\Factories\CourseFactory::new();
    }
}
