<?php

namespace App\Core\Models;

use App\Core\Authorization\Models\Right;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasUuids;

    protected $fillable = [
        'first_name',
        'last_name',
        'role',
        'status',
        'phone',
        'gender',
        'email',
        'password',
        'address',
        'school_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'email_verified_at',
        'deleted_at',
    ];

    protected $casts = [
        'role' => UserRole::class,
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::EMPLOYEE && $this->hasRight('SUPER_ADMIN');
    }

    public function hasRight(string $code): bool
    {
        return $this->rights()->where('code', $code)->exists();
    }

    public function rights(): BelongsToMany
    {
        return $this->belongsToMany(Right::class, 'user_rights', 'user_id', 'right_id');
    }

    public function fullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    protected static function newFactory()
    {
        return \App\Core\Database\Factories\UserFactory::new();
    }
}
