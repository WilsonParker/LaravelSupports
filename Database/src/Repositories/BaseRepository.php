<?php

namespace LaravelSupports\Database\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use LaravelSupports\Database\Repositories\Contracts\RepositoryContract;
use Override;

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
        return $model->update($attribute);
    }

    #[Override]
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    protected function getSearchQuery(Builder $builder, array $attributes): Builder
    {
        return $builder;
    }

    protected function getQuery(): Builder
    {
        return $this->getModelClass()::query();
    }

    protected function getModelClass(): string
    {
        return $this->modelCls;
    }
}
