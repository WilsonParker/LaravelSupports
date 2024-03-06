<?php

namespace LaravelSupports\Database\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use LaravelSupports\Database\Repositories\Contracts\RepositoryContract;

class BaseRepository implements RepositoryContract
{
    protected Builder $model;

    /**
     * @param string $model
     */
    public function __construct(protected string $modelCls)
    {
        $this->model = $modelCls::query();
    }

    public function list(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->get($columns);
    }

    public function storeIfNotExists($id, array $attribute): Model
    {
        $model = $this->model->find($id);
        if (isset($model)) {
            return $model;
        } else {
            return $this->model->create($attribute);
        }
    }

    public function create(array $attribute): Model
    {
        return $this->model->create($attribute);
    }

    public function update(Model $model, array $attribute): bool
    {
        return $this->updateById($model->getKey(), $attribute);
    }

    public function updateById($id, array $attribute): bool
    {
        return $this->showById($id)->update($attribute);
    }

    public function delete(Model $model): bool
    {
        return $this->deleteById($model->getKey());
    }

    public function deleteById($id): bool
    {
        return $this->showById($id)->delete();
    }

    protected function getModelClass(): string
    {
        return $this->modelCls;
    }

    protected function getSearchQuery(Builder $builder, array $attributes): Builder
    {
        return $builder;
    }
}
