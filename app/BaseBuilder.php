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
            $relations = [$relations => $callback];
        }
//        $relations = $this->parseWithRelations($relations);

        $existing = collect($relations)
            ->filter(function ($constraints, $relation) use ($model) {
                return RelationRegistry::exists($model::class, $relation)
                    || method_exists($model, $relation);
            })
            ->toArray();

        return parent::with($existing);
    }

    public function with($relations, $callback = null): static
    {
        return $this->withExisting($relations, $callback);
    }
}
