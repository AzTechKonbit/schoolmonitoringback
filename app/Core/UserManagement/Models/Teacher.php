<?php

namespace App\Core\UserManagement\Models;

use App\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    protected $table = 'teachers';

    protected $fillable = [
        'employee_id',
        'teacher_number_id',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

//    public function courses(): BelongsToMany
//    {
//        return $this->belongsToMany(Course::class, 'teachers_courses', 'teacher_id', 'course_id');
//    }
//
//    public function schedules(): HasMany
//    {
//        return $this->hasMany(Schedule::class);
//    }
//
//    public function assignments(): HasMany
//    {
//        return $this->hasMany(Assignment::class);
//    }
//
//    public function attendances(): HasMany
//    {
//        return $this->hasMany(Attendance::class, 'recorded_by');
//    }
}
