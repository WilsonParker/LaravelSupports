<?php

namespace LaravelSupports\Models\Relationship\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LaravelSupports\Models\Common\BaseModel;

trait SaveRelationshipTrait
{
    protected array $relationships = [
        HasOne::class => 'saveHasOne',
        HasMany::class => 'saveHasMany',
        \Awobaz\Compoships\Database\Eloquent\Relations\HasOne::class => 'saveHasOne',
        \Awobaz\Compoships\Database\Eloquent\Relations\HasMany::class => 'saveHasMany',
    ];

    /**
     *
     * @param array|string $relationship
     * @param null $data
     * @return mixed
     * @author  WilsonParker
     * @added   2021/09/15
     * @updated 2021/09/15
     */
    public function saveRelationships(array|string $relationship, $data = null): mixed
    {
        if (gettype($relationship) == 'array') {
            collect($relationship)->each(function ($value, $key) {
                $this->saveRelationship($key, $value);
            });
            return null;
        } else {
            return $this->saveRelationship($relationship, $data);
        }
    }

    protected function saveRelationship(string $relationship, $data = null): mixed
    {
        $relation = $this->$relationship();
        return $this->{$this->relationships[$relation::class]}($relation, $data);
    }

    protected function saveHasOne(HasOne $relation, BaseModel $model): bool
    {
        $this->{$relation->getLocalKeyName()} = $model->{$relation->getForeignKeyName()};
        return $this->save();
    }

    protected function saveHasMany(HasOne $relation, \Illuminate\Support\Collection|array $data): bool
    {
        return false;
    }

}
