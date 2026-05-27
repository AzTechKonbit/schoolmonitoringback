<?php

namespace Modules\Academic\Models;

use App\Core\Models\School;
use App\Core\UserManagement\Models\Student;
use App\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'classes_students', 'class_id', 'student_id');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'classes_courses', 'classe_id', 'course_id');
    }

    public function school(): BelongsToMany
    {
        return $this->belongsToMany(School::class, 'schools_classes', 'school_id', 'class_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class,'class_id');
    }
    protected static function newFactory()
    {
        return \Modules\Academic\Database\Factories\SchoolClassFactory::new();
    }
}
