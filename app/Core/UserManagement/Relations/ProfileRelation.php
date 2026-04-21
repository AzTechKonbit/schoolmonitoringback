<?php

namespace App\Core\UserManagement\Relations;

use App\Core\UserManagement\Models\{Employee, ParentModel, Student};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class ProfileRelation extends Relation
{
    protected $typeMap = [
        'student' => Student::class,
        'employee' => Employee::class,
        'parent' => ParentModel::class,
    ];

    protected $resultsByType = [];

    public function __construct(Model $parent)
    {
        $defaultModel = reset($this->typeMap);
        $query = (new $defaultModel)->newQuery();

        parent::__construct($query, $parent);
    }

    public function addConstraints()
    {
        // Pour le lazy loading, on ne peut pas vraiment optimiser
        // getResults() fera le travail
    }

    public function addEagerConstraints(array $models)
    {
        $userIds = [];

        foreach ($models as $model) {
            $userIds[] = $model->id;
        }

        // Charger tous les profils de chaque type en une seule requête par type
        foreach ($this->typeMap as $type => $class) {
            $this->resultsByType[$type] = $class::whereIn('user_id', $userIds)
                ->get()
                ->keyBy('user_id');
        }
    }

    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, null);
        }

        return $models;
    }

    public function match(array $models, Collection $results, $relation)
    {
        foreach ($models as $user) {
            $match = null;

            // Chercher dans chaque type jusqu'à trouver un match
            foreach ($this->resultsByType as $type => $profiles) {
                if ($profiles->has($user->id)) {
                    $match = $profiles->get($user->id);
                    break; // On arrête dès qu'on trouve
                }
            }

            $user->setRelation($relation, $match);
        }

        return $models;
    }

    public function getResults()
    {
        // Lazy loading : essayer chaque type jusqu'à trouver un résultat
        foreach ($this->typeMap as $type => $class) {
            $result = $class::where('user_id', $this->parent->id)->first();

            if ($result) {
                return $result;
            }
        }

        return null;
    }
}
