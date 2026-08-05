<?php

namespace App\Core\UserManagement\Models;

use App\Core\Models\{School, User};
use App\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Academic\Models\AssignmentSubmission;

class Student extends Model
{
    protected $fillable = [
        'dob',
        'user_id',
        'parent_id',
        'student_number_id',
        'school_id',
    ];

    protected $casts = [
        'dob' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

//    public function programs(): BelongsToMany
//    {
//        return $this->belongsToMany(Program::class, 'program_student', 'student_id', 'idprogram');
//    }
    protected static function newFactory()
    {
        return \App\Core\UserManagement\Database\Factories\StudentFactory::new();
    }
}
