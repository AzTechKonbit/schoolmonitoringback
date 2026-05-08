<?php

namespace App\Core\Authorization\Models;

use App\Core\UserManagement\Models\Employee;
use App\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Title extends Model
{
    protected $fillable = [
        'code',
        'title',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_titles', 'title_id', 'employee_id');
    }

    protected static function newFactory()
    {
        return \App\Core\Authorization\Database\Factories\TitleFactory::new();
    }
}
