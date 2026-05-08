<?php

namespace App\Core\UserManagement\Models;

use App\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeType extends Model
{
    protected $fillable = [
        'code',
        'type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function groupRights(): HasMany
    {
        return $this->hasMany(GroupRight::class);
    }

    protected static function newFactory()
    {
        return \App\Core\UserManagement\Database\Factories\EmployeeTypeFactory::new();
    }
}
