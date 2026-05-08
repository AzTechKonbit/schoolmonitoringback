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

//    public function assignments(): HasMany
//    {
//        return $this->hasMany(Assignment::class);
//    }
}
