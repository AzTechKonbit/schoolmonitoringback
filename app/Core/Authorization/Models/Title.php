<?php

namespace App\Core\Authorization\Models;

use App\Model;

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

//    public function employees(): BelongsToMany
//    {
//        return $this->belongsToMany(Employee::class, 'employee_titles', 'title_id', 'employee_id');
//    }
}
