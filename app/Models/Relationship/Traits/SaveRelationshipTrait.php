<?php

namespace App\Library\LaravelSupports\app\Models\Relationship\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LaravelSupports\Models\Common\BaseModel;

trait SaveRelationshipTrait
{
    protected array $relationships = [
        HasOne::class => 'saveHasOne',
        HasMany::class => 'saveHasMany',
    ];

    /**
     *
     * @param string $relationship
     * @param $data
     * @return mixed
     * @author  WilsonParker
     * @added   2021/09/15
     * @updated 2021/09/15
     */
    public function saveRelationship(string $relationship, $data): mixed
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

    }

}
