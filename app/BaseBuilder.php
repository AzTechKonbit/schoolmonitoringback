<?php

namespace App;

use App\Relations\RelationRegistry;
use Illuminate\Database\Eloquent\Builder;

class BaseBuilder extends Builder
{
    public function withExisting($relations, $callback = null): static
    {
        $model = $this->getModel();

        if (is_string($relations)) {
            $relations = $callback
                ? [$relations => $callback]
                : [$relations];
        }
//        $relations = $this->parseWithRelations($relations);

        $existing = collect($relations)
            ->filter(function ($constraints, $relation) use ($model) {
                $name = is_string($relation) ? $relation : (string) $constraints;
                return RelationRegistry::exists($model::class, $name)
                    || method_exists($model, $name);
            })
            ->toArray();

        return parent::with($existing);
    }

    public function with($relations, $callback = null): static
    {
        return $this->withExisting($relations, $callback);
    }
}
