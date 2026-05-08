<?php

namespace App\Core\UserManagement\Models;

use App\Core\Models\User;
use App\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentModel extends Model
{
    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'user_id2',
    ];

    public function primaryUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function secondaryUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id2');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
        protected static function newFactory()
    {
        return \App\Core\UserManagement\Database\Factories\ParentFactory::new();
    }
}
