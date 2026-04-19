<?php

namespace App\Core\Authorization\Models;

use App\Core\Models\User;
use App\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Right extends Model
{
    protected $fillable = [
        'code',
        'description',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_rights', 'right_id', 'user_id');
    }

    public function employeeTypes(): BelongsToMany
    {
        return $this->belongsToMany(EmployeeType::class, 'group_rights', 'right_id', 'employee_type_id');
    }
}
