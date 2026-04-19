<?php

namespace App\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Core\UserManagement\Models\{Student,Employee};
use App\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'name',
        'type',
        'logo',
        'default_language',
        'academic_year',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

//    public function departments(): HasMany
//    {
//        return $this->hasMany(Department::class);
//    }
//
//    public function classes(): HasMany
//    {
//        return $this->hasMany(SchoolClass::class);
//    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
