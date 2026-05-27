<?php

namespace App\Relations;

class RelationRegistry
{
    protected static array $relations = [];

    public static function register(string $model, string $relation): void
    {
        static::$relations[$model][] = $relation;
    }

    public static function exists(string $model, string $relation): bool
    {
        return in_array($relation, static::$relations[$model] ?? []);
    }
}
