<?php

namespace App;

use App\Relations\RelationRegistry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Base;

class Model extends Base
{
    use HasFactory;

//    public $incrementing = false;
//    protected $keyType = 'string';

    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
        'email_verified_at',
    ];

    public function scopeWithExisting($query, array $relations)
    {
        $existing = collect($relations)
            ->filter(fn($relation) => static::hasDynamicRelation($relation))
            ->values()
            ->toArray();

        return $query->with($existing);
    }

    protected static function hasDynamicRelation(string $relation): bool
    {
        return RelationRegistry::exists(static::class, $relation)
            || method_exists(static::class, $relation);
    }

    public function newEloquentBuilder($query): BaseBuilder
    {
        return new BaseBuilder($query);
    }
}
